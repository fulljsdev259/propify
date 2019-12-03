<?php

namespace App\Transformers;

use App\Models\PropertyManager;

/**
 * Class PropertyManagerTransformer.
 *
 * @package namespace App\Transformers;
 */
class PropertyManagerTransformer extends BaseTransformer
{
    /**
     * Transform the Building entity.
     *
     * @param PropertyManager $model
     *
     * @return array
     */
    public function transform(PropertyManager $model)
    {
        $response = [
            'id' => $model->id,
            'type' => $model->type,
            'property_manager_format' => $model->property_manager_format,
            'description' => $model->description,
            'mobile_phone' => $model->mobile_phone,
            'title' => $model->title,
            'status' => $model->status,
            'first_name' => $model->first_name,
            'last_name' => $model->last_name,
        ];

        $withCount = $model->getStatusRelationCounts();
        $response = array_merge($response, $withCount);

        if ($model->relationExists('settings')) {
            $response['settings'] = $model->settings;
        }

        if ($model->relationExists('user')) {
            $response['user'] = (new UserTransformer)->transform($model->user);
        }

        if ($model->relationExists('buildings')) {
            $response['buildings'] = (new BuildingTransformer)->transformCollection($model->buildings);
            $response['buildings_count'] = $model->buildings->count();
        }

        if ($model->relationExists('quarters')) {
            $response['quarters'] = (new QuarterTransformer)->transformCollection($model->quarters);
        }

        if ($model->relationExists('quarters')) {
            $response['quarters'] = (new QuarterTransformer)->transformCollection($model->quarters);
            $response['internal_quarter_ids'] = collect($response['quarters'])->pluck('internal_quarter_id')->unique()->all();
        }

        return $this->addAuditIdInResponseIfNeed($model, $response);
    }

    /**
     * Transform the Building entity.
     *
     * @param PropertyManager $model
     *
     * @return array
     */
    public function residentPropertyManagers(PropertyManager $model)
    {
        return [
            'id' => $model->id,
            'first_name' => $model->first_name,
            'last_name' => $model->last_name,
            'email' => $model->user->email ?? '',
            'phone' => $model->user->phone ?? '',
            'avatar' => $model->user->avatar ?? null
        ];
    }
}
