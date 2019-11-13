<?php

namespace App\Transformers;

use App\Models\Quarter;

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
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
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
            $response['has_email_receptionists'] = $model->has_email_receptionists;
        }

        if ($model->relationExists('address')) {
            $response['address'] = (new AddressTransformer)->transform($model->address);
        }

        if ($model->relationExists('buildings')) {
            $response['buildings'] = (new BuildingTransformer())->transformCollection($model->buildings);
            $response['contracts'] = collect($response['buildings'])->pluck('contracts')->collapse();
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
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function transformWIthStatistics(Quarter $model)
    {
        $response = $this->transform($model);
        $buildings = $model->buildings;
        $units = $buildings->pluck('units')->collapse();
        $occupiedUnits = $units->filter(function ($unit) {
            return $unit->contracts->isNotEmpty();
        });

        $response['buildings_count'] = $buildings->count();
        $response['active_residents_count'] = $units->pluck('contracts.*.resident_id')->collapse()->unique()->count();
        $response['total_units_count'] = $units->count();
        $response['occupied_units_count'] = $occupiedUnits->count();
        $response['free_units_count'] = $units->count() - $occupiedUnits->count();

        if ($model->relationExists('media')) {
            $response['media'] = (new MediaTransformer())->transformCollection($model->media);
        }

        return $response;
    }
}
