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
        $response = [
            'id' => $model->id,
            'name' => $model->name,
            'quarter_format' => $model->quarter_format,
            'internal_quarter_id' => $model->internal_quarter_id,
            'description' => $model->description,
            'count_of_buildings' => $model->count_of_buildings,
            'url' => $model->url,
            'types' => $model->types,
            'assignment_type' => $model->assignment_type,
        ];

        if ($model->hasAttribute('has_email_receptionists')) {
            // @TODO kill
            $response['has_email_receptionists'] = $model->has_email_receptionists;
        }
        if ($model->hasAttribute('count_of_apartments_units')) {
            $response['count_of_apartments_units'] = $model->count_of_apartments_units;
        }

        if ($model->relationExists('address')) {
            if (array_keys($model->address->getAttributes()) == ['id', 'city']) {
                $response['city'] = $model->address->city;
            } else {
                $response['address'] = (new AddressTransformer)->transform($model->address);
            }
        }

        if ($model->relationExists('buildings')) {
            $response['buildings'] = (new BuildingTransformer())->transformCollection($model->buildings);
            $response['relations'] = collect($response['buildings'])->pluck('relations')->collapse();
        }

        if ($model->relationExists('media')) {
            $response['media'] = (new MediaTransformer())->transformCollection($model->media);
        }

        if ($model->relationExists('workflows')) {
            $response['workflows'] = (new WorkflowTransformer())->transformCollection($model->workflows);
        }

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
