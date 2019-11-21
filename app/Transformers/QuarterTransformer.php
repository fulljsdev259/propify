<?php

namespace App\Transformers;

use App\Models\Quarter;
use App\Models\Relation;

/**
 * Class QuarterTransformer.
 *
 * @package namespace App\Transformers;
 */
class QuarterTransformer extends BaseTransformer
{
    /**
     * @param Quarter $model
     * @return array
     */
    public function transform(Quarter $model)
    {
        $response = $this->getAttributesIfExists($model, [
            'id',
            'name',
            'quarter_format',
            'internal_quarter_id',
            'description',
            'count_of_buildings',
            'url',
            'types',
            'assignment_type',
            'count_of_apartments_units',
            'has_email_receptionists' // @TODO kill
        ]);

        $relationsStatusCount = $model->getRelationStatusCounts();
        $response = array_merge($response, $relationsStatusCount);

        if ($model->relationExists('address')) {
            if (array_keys($model->address->getAttributes()) == ['id', 'city']) {
                $response['city'] = $model->address->city;
            } else {
                $response['address'] = (new AddressTransformer)->transform($model->address);
            }
        }

        $response = $this->includeRelationIfExists($model, $response, [
            'buildings' => BuildingTransformer::class,
            'relations' => RelationTransformer::class,
            'media' => MediaTransformer::class,
            'workflows' => WorkflowTransformer::class,
            'users' => UserTransformer::class
        ]);

        return $response;
    }

    /**
     * @param Quarter $model
     * @return array
     */
    public function transformWithStatistics(Quarter $model)
    {
        $buildings = $model->buildings;
        $response = $this->transform($model);

        $units = $buildings->pluck('units')->collapse()->merge($model->units)->unique();
        $statusCounts = $this->getUnitsStatus($units);
        $response = array_merge($response, $statusCounts);
//        $requestsCount = $buildings->pluck('requests')->collapse()->unique()->countBy('status');
//        // @TODO improve for not depend new status
//        $response['requests_archived_count'] = $requestsCount[Request::StatusArchived] ?? 0;
//        $response['requests_assigned_count'] = $requestsCount[Request::StatusAssigned] ?? 0;
//        $response['requests_count'] = $requestsCount->sum();
//        $response['requests_done_count'] = $requestsCount[Request::StatusDone] ?? 0;
//        $response['requests_in_processing_count'] = $requestsCount[Request::StatusInProcessing] ?? 0;
//        $response['requests_reactivated_count'] = $requestsCount[Request::StatusReactivated] ?? 0;
//        $response['requests_received_count'] = $requestsCount[Request::StatusReceived] ?? 0;

        $response['buildings_count'] = $buildings->count();
        $response['active_residents_count'] = $model->relations->where('status', Relation::StatusActive)
            ->pluck('resident_id')
            ->unique()
            ->count(); // @TODO check

        if ($model->relationExists('media')) {
            $response['media'] = (new MediaTransformer())->transformCollection($model->media);
        }

        return $response;
    }


    /**
     * @param $data
     * @return array
     */
    protected function getUnitsStatus($units)
    {
        $data = (new UnitTransformer())->transformCollectionBy($units, 'transformForIndex');
        $unitsCountByStatus = collect($data)->countBy('status_color');
        $statusCodes = Relation::StatusColorCode;
        $response = [];
        foreach ($statusCodes as $status => $color) {
            $response[Relation::Status[$status] . '_units_count'] = $unitsCountByStatus[$color] ?? 0;
        }
        $response['total_units_count'] = array_sum($response);
        return $response;
    }
}
