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
        $response = $this->getAttributesIfExists($model, [
            'id',
            'category',
            'type',
            'status',
            'title',
            'first_name',
            'last_name',
            'company_name',
            'mobile_phone',
            'email',
            'phone',
            'service_provider_format',
            'buildings_count',
            'requests_count',
            'solved_requests_count',
            'pending_requests_count',
        ]);

        $withCount = $model->getStatusRelationCounts();
        $response = array_merge($response, $withCount);

        $response = $this->includeRelationIfExists($model, $response, [
            'user' => UserTransformer::class,
            'address' => AddressTransformer::class,
            'buildings' => BuildingTransformer::class,
        ]);

        if ($model->relationExists('settings')) {
            $response['settings'] = $model->settings;
        }

        if ($model->relationExists('quarters')) {
            $response['quarters'] = (new QuarterTransformer)->transformCollection($model->quarters);
            $response['internal_quarter_ids'] = collect($response['quarters'])->pluck('internal_quarter_id')->unique()->all();
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

