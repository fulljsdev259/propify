<?php

namespace App\Console\Commands;

use App\Models\AuditableModel;
use App\Models\Relation;
use App\Models\Resident;
use Illuminate\Console\Command;

class FixRelationStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix-relation-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'make relation inactive after expiration';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @throws \OwenIt\Auditing\Exceptions\AuditingException
     */
    public function handle()
    {
        Resident::disableAuditing();
        $auditData = $this->makeActiveRelationToInActiveIfNeed();
//        $auditData = $this->makeInactiveRelationToActiveIfNeed($auditData);
        Resident::enableAuditing();

        if (!empty($auditData)) {
            (new AuditableModel())->newSystemAudit(
                AuditableModel::MergeInMainData,
                $auditData,
                AuditableModel::EventUpdated,
                true,
                ['command' => $this->signature]
            );
        }
    }

    /**
     *
     */
    protected function makeActiveRelationToInActiveIfNeed()
    {
        $auditData = [];
        // get all expired relation
        $relations = Relation::where('status', Relation::StatusActive)
            ->where('end_date', '<', now()->format('Y-m-d'))
            ->get(['id', 'resident_id']);

        $relationIds = $relations->pluck('id')->all();
        if (empty($relationIds)) {
            return $auditData;
        }

        $auditData['relations']['status'][] = [
            'ids' => $relationIds,
            'status' => [
                'old' => Relation::StatusActive,
                'new' => Relation::StatusInActive,
            ]
        ];

        // make inactive expired relations
        Relation::whereIn('id', $relationIds)->update(['status' => Relation::StatusInActive]);


        // change default_relation_id
        $residentWithDefaultRelation = Resident::whereIn('default_relation_id', $relationIds)->get(['id', 'default_relation_id']);
        $activeRelations = Relation::whereIn('resident_id', $residentWithDefaultRelation->pluck('id'))
            ->where('status', Relation::StatusActive)->get(['id', 'resident_id']);

        foreach ($residentWithDefaultRelation as $resident) {
            $relationId = $activeRelations->where('resident_id', $resident->id)->first()->id ?? null;
            $auditData['residents']['default_relation'][$resident->id] = [
                'old' => $resident->default_relation_id,
                'new' => $relationId,
            ];
            $resident->update([
                'default_relation_id' => $relationId
            ]);
        }


        $residentIds = $relations->pluck('resident_id')->unique()->toArray();
        if (empty($residentIds)) {
            return $auditData;
        }

        // make inactive residents how all relations expired
        $residents = Resident::whereIn('id', $residentIds)
            ->where('status', Resident::StatusActive)
            ->whereDoesntHave('relations', function ($q) {
                $q->where('status', Relation::StatusActive);
            })->get(['id', 'status']);

        $activeResidentIds = $residents->pluck('id')->all();
        if (empty($activeResidentIds)) {
            return $auditData;
        }


        $auditData['residents']['status'][] = [
            'ids' => $activeResidentIds,
            'status' => [
                'old' => Resident::StatusActive,
                'new' => Resident::StatusInActive,
            ]
        ];
        Resident::whereIn('id', $activeResidentIds)->update(['status' => Resident::StatusInActive]);
        return $auditData;
    }

//    /**
//     *
//     */
//    protected function makeInactiveRelationToActiveIfNeed($auditData)
//    {
//        // get all not active relation started today
//        $relations = Relation::where('status', Relation::StatusInActive)
//            ->where('start_date', '=', now()->format('Y-m-d'))
//            ->get(['id', 'resident_id']);
//
//        $relationIds = $relations->pluck('id')->all();
//
//        if (empty($relationIds)) {
//            return $auditData;
//        }
//
//        // make  relations active
//        Relation::whereIn('id', $relationIds)->update(['status' => Relation::StatusActive]);
//        $auditData['relations'][] = [
//            'ids' => $relationIds,
//            'status' => [
//                'old' => Relation::StatusInActive,
//                'new' => Relation::StatusActive,
//            ]
//        ];
//
//
//        $residentIds = $relations->pluck('resident_id')->unique()->toArray();
//        if (empty($residentIds)) {
//            return $auditData;
//        }
//
//
//        // make  inactive resident to active
//        $residents = Resident::whereIn('id', $residentIds)
//            ->where('status', Resident::StatusInActive)
//            ->get(['id']);
//
//        $notActiveResidentIds = $residents->pluck('id')->all();
//        if (empty($notActiveResidentIds)) {
//            return $auditData;
//        }
//
//        $auditData['residents'][] = [
//            'ids' => $notActiveResidentIds,
//            'status' => [
//                'old' => Resident::StatusInActive,
//                'new' => Resident::StatusActive,
//            ]
//        ];
//        Resident::whereIn('id', $notActiveResidentIds)->update(['status' => Resident::StatusActive]);
//        return $auditData;
//    }

}
