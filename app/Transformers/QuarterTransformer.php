<?php

namespace App\Transformers;

use App\Models\Quarter;
use App\Models\Request;

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
        $response = $this->getIfHasInAttributes($model, [
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
            'active_relations_count',
            'inactive_relations_count',
            'canceled_relations_count',
            'has_email_receptionists' // @TODO kill
        ]);

        if (array_keys_exists(['active_relations_count', 'inactive_relations_count', 'canceled_relations_count'], $response)) {
            $response['relations_count'] =  $response['active_relations_count']
                +  $response['inactive_relations_count']
                +  $response['canceled_relations_count'] ;
        }

        if ($model->relationExists('address')) {
            if (array_keys($model->address->getAttributes()) == ['id', 'city']) {
                $response['city'] = $model->address->city;
            } else {
                $response['address'] = (new AddressTransformer)->transform($model->address);
            }
        }

        $response = $this->includeIfHasRelation($model, $response, [
            'buildings' => BuildingTransformer::class,
            'relations' => RelationTransformer::class,
            'media' => MediaTransformer::class,
            'workflows' => WorkflowTransformer::class,
        ]);

        return $response;
    }

    /**
     * @param Quarter $model
     * @return array
     */
    public function transformWithStatistics(Quarter $model)
    {
        $response = $this->transform($model);
        $buildings = $model->buildings;
        $units = $buildings->pluck('units')->collapse();
        $occupiedUnits = $units->filter(function ($unit) {
            return $unit->relations->isNotEmpty();
        });

        $requestsCount = $buildings->pluck('requests')->collapse()->unique()->countBy('status');
        $response['requests_archived_count'] = $requestsCount[Request::StatusArchived] ?? 0;
        $response['requests_assigned_count'] = $requestsCount[Request::StatusAssigned] ?? 0;
        $response['requests_count'] = $requestsCount->sum();
        $response['requests_done_count'] = $requestsCount[Request::StatusDone] ?? 0;
        $response['requests_in_processing_count'] = $requestsCount[Request::StatusInProcessing] ?? 0;
        $response['requests_reactivated_count'] = $requestsCount[Request::StatusReactivated] ?? 0;
        $response['requests_received_count'] = $requestsCount[Request::StatusReceived] ?? 0;

        $response['buildings_count'] = $buildings->count();
        $response['active_residents_count'] = $units->pluck('relations.*.resident_id')->collapse()->unique()->count();
        $response['total_units_count'] = $units->count();
        $response['occupied_units_count'] = $occupiedUnits->count();
        $response['free_units_count'] = $units->count() - $occupiedUnits->count();

        if ($model->relationExists('media')) {
            $response['media'] = (new MediaTransformer())->transformCollection($model->media);
        }

        return $response;
    }
}
