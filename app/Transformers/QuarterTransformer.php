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
        $units = $buildings->pluck('units')->collapse();

        $requestsCount = $buildings->pluck('requests')->collapse()->unique()->countBy('status');
        // @TODO improve for not depend new status
        $response['requests_archived_count'] = $requestsCount[Request::StatusArchived] ?? 0;
        $response['requests_assigned_count'] = $requestsCount[Request::StatusAssigned] ?? 0;
        $response['requests_count'] = $requestsCount->sum();
        $response['requests_done_count'] = $requestsCount[Request::StatusDone] ?? 0;
        $response['requests_in_processing_count'] = $requestsCount[Request::StatusInProcessing] ?? 0;
        $response['requests_reactivated_count'] = $requestsCount[Request::StatusReactivated] ?? 0;
        $response['requests_received_count'] = $requestsCount[Request::StatusReceived] ?? 0;

        $response['buildings_count'] = $buildings->count();
        $response['active_residents_count'] = $units->pluck('relations.*.resident_id')->collapse()->unique()->count();

        if ($model->relationExists('media')) {
            $response['media'] = (new MediaTransformer())->transformCollection($model->media);
        }

        return $response;
    }
}
