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
        $auditData = $this->makeCanceledRelationToInActiveIfNeed();
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
    protected function makeCanceledRelationToInActiveIfNeed()
    {
        $auditData = [];
        // get all expired relation
        $relations = Relation::whereIn('status', [Relation::StatusCanceled, Relation::StatusActive])
            ->where('end_date', '<', now()->format('Y-m-d')) // @TODO correct today  must be consider as  past or future
            ->get(['id', 'resident_id', 'status']);

        $relationIds = $relations->pluck('id')->all();
        if (empty($relationIds)) {
            return $auditData;
        }

        $canceledIds = $relations->where('status', Relation::StatusCanceled)->pluck('id')->all();
        if ($canceledIds) {
            $auditData['relations']['status'][] = [
                'ids' => $canceledIds,
                'status' => [
                    'old' => Relation::StatusCanceled,
                    'new' => Relation::StatusInActive,
                ]
            ];
        }
        $activeIds = $relations->where('status', Relation::StatusActive)->pluck('id')->all();
        if ($activeIds) {
            $auditData['relations']['status'][] = [
                'ids' => $activeIds,
                'status' => [
                    'old' => Relation::StatusActive,
                    'new' => Relation::StatusInActive,
                ]
            ];
        }
        // make inactive expired relations
        Relation::whereIn('id', $relationIds)->update(['status' => Relation::StatusInActive]);

        $residentIds = $relations->pluck('resident_id')->unique()->toArray();
        if (empty($residentIds)) {
            return $auditData;
        }


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
}
