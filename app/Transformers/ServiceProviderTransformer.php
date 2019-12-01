<?php

namespace App\Transformers;

use App\Models\ServiceProvider;
use Illuminate\Database\Eloquent\Collection;

/**
 * Class ServiceProviderTransformer
 *
 * @package namespace App\Transformers;
 */
class ServiceProviderTransformer extends BaseTransformer
{
    /**
     * Transform the ServiceProvider entity.
     *
     * @param \App\Models\ServiceProvider $model
     *
     * @param ServiceProvider $model
     * @return array
     */
    public function transform(ServiceProvider $model)
    {
        $response = [
            'id' => $model->id,
            'category' => $model->category,
            'type' => $model->type,
            'status' => $model->status,
            'title' => $model->title,
            'first_name' => $model->first_name,
            'last_name' => $model->last_name,
            'company_name' => $model->company_name,
            'mobile_phone' => $model->mobile_phone,
            'name' => $model->company_name, // @TODO delete
            'email' => $model->email,
            'phone' => $model->phone,
            'service_provider_format' => $model->service_provider_format,
        ];

        $relationCountAttributes = [
            'buildings_count',
            'requests_count',
            'solved_requests_count',
            'pending_requests_count',
        ];

        $attributes = $model->getAttributes();
        foreach ($relationCountAttributes as $attribute) {
            if (key_exists($attribute, $attributes)) {
                $response[$attribute] = $attributes[$attribute];
            }
        }

        $withCount = $model->getStatusRelationCounts();
        $response = array_merge($response, $withCount);

        if ($model->relationExists('user')) {
            $response['user'] = (new UserTransformer)->transform($model->user);
        }
        if ($model->relationExists('settings')) {
            $response['settings'] = $model->settings;
        }

        if ($model->relationExists('address')) {
            $response['address'] = (new AddressTransformer)->transform($model->address);
        }

        if ($model->relationExists('quarters')) {
            $response['quarters'] = (new QuarterTransformer)->transformCollection($model->quarters);
            $response['internal_quarter_ids'] = collect($response['quarters'])->pluck('internal_quarter_id')->unique()->all();
        }
        if ($model->relationExists('buildings')) {
            $response['buildings'] = (new BuildingTransformer)->transformCollection($model->buildings);
        }

        return $response;
    }

    /**
     * Transform the collection.
     *
     * @param \Collection $collection
     *
     * @return array
     */
    public function transformByCategoryCollection(Collection $collection)
    {
        foreach ($collection as $col) {
            $col->user_id = 0;
            $col->address_id = 0;
        }

        $services = $this->transformCollection($collection);
        $response = [];
        foreach ($services as $service) {
            if (!isset($response[$service['category']])) {
                $response[$service['category']] = [];
            }
            $response[$service['category']][] = $service;
        }

        return $response;
    }
}

