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
        // get all expired relation
        $relations = Relation::whereIn('status', [Relation::StatusCanceled, Relation::StatusActive])
            ->with('resident:id,resident_format')
            ->where('end_date', '<', now()->format('Y-m-d'))
            ->get(['id', 'resident_id', 'status']);

        $relationIds = $relations->pluck('id')->all();

        if (empty($relationIds)) {
            return ;
        }

        $this->saveResidentRelationChangesAudits($relations, Relation::StatusCanceled);
        $this->saveResidentRelationChangesAudits($relations, Relation::StatusActive);

        // make inactive expired relations
        Relation::whereIn('id', $relationIds)->update(['status' => Relation::StatusInActive]);

        $residentIds = $relations->pluck('resident_id')->unique()->toArray();
        if (empty($residentIds)) {
            return;
        }

        // change default_relation_id
        $residentWithDefaultRelation = Resident::whereIn('default_relation_id', $relationIds)->get(['id', 'default_relation_id', 'resident_format']);
        $activeRelations = Relation::whereIn('resident_id', $residentWithDefaultRelation->pluck('id'))
            ->where('status', Relation::StatusActive)->get(['id', 'resident_id']);

        foreach ($residentWithDefaultRelation as $resident) {
            $relationId = $activeRelations->where('resident_id', $resident->id)->first()->id ?? null;
            $resident->update([
                'default_relation_id' => $relationId
            ]);

            $resident->makeAuditAsSystem();
        }

        // make inactive residents how all relations expired
        $residents = Resident::whereIn('id', $residentIds)
            ->where('status', Resident::StatusActive)
            ->whereDoesntHave('relations', function ($q) {
                $q->where('status', Relation::StatusActive);
            })->get(['id', 'status', 'resident_format']);

        foreach ($residents as $resident) {
            $resident->update(['status' => Resident::StatusInActive]);
            $resident->makeAuditAsSystem();
        }

        return;
    }

    /**
     * @param $relations
     * @param $oldStatus
     * @throws \OwenIt\Auditing\Exceptions\AuditingException
     */
    protected function saveResidentRelationChangesAudits($relations, $oldStatus)
    {
        $canceledRelations = $relations->where('status', $oldStatus);
        foreach ($canceledRelations as $relation) {
            $resident = $relation->resident;
            if (empty($resident)) {
                return;
            }
            $resident->makeNewSystemAudit(
                AuditableModel::EventRelationUpdated,
                [
                    'status' => Relation::StatusCanceled
                ],
                [
                    'status' => Relation::StatusInActive
                ],
                true
            );
        }

    }
}
