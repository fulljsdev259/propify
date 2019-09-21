<?php

namespace App\Transformers;

use App\Models\Tenant;

/**
 * Class TenantTransformer.
 *
 * @package namespace App\Transformers;
 */
class TenantTransformer extends BaseTransformer
{

    /**
     * @param Tenant $model
     * @return array
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function transform(Tenant $model)
    {
        $response = [
            'id' => $model->id,
            'title' => $model->title,
            'company' => $model->company,
            'first_name' => $model->first_name,
            'last_name' => $model->last_name,
            'birth_date' => $model->birth_date->format('Y-m-d'),
            'birth_date_formatted' => $model->birth_date->format('d.m.Y'),
            'mobile_phone' => $model->mobile_phone,
            'private_phone' => $model->private_phone,
            'work_phone' => $model->work_phone,
            'status' => $model->status,
            'tenant_format' => $model->tenant_format,
            'nation' => $model->nation,
        ];

        if ($model->relationExists('settings')) {
            $response['settings'] = $model->settings;
        }

        if ($model->relationExists('user')) {
            $response['user'] = (new UserTransformer)->transform($model->user);
        }
        if ($model->rent_start) {
            $response['rent_start'] = $model->rent_start->format('Y-m-d');
        }
        if ($model->rent_end) {
            $response['rent_end'] = $model->rent_end->format('Y-m-d');
        }

        $response['media'] = [];
        if ($model->relationExists('rent_contracts')) {
            $response['rent_contracts'] = (new RentContractTransformer())->transformCollection($model->rent_contracts);
        }

        if ($model->relationExists('building')) {
            $response['building'] = (new BuildingTransformer)->transform($model->building);
        }

        if ($model->relationExists('unit')) {
            $response['unit'] = (new UnitTransformer)->transform($model->unit);
        }

        if ($model->relationExists('media')) {
            $response['media'] = (new MediaTransformer)->transformCollection($model->media);
        }

        if ($model->relationExists('address')) {
            $response['address'] = (new AddressTransformer)->transform($model->address);
        }

        return $response;
    }

    /**
     * Transform Request to Address entity.
     *
     * @param array $input
     *
     * @return array
     */
    public function transformRequest(array $input)
    {
        if (!isset($input['user'])) {
            $input['user'] = [];
            return $input;
        }

        if (!isset($input['user']['password'])) {
           $input['user']['password'] = str_random(8);
        }

        $input['user']['name'] = sprintf('%s %s', $input['first_name'], $input['last_name']);

        return $input;
    }

    /**
     * Include Address
     *
     * @param Building $building
     *
     * @return \League\Fractal\Resource\Item
     */
    public function includeAddress(Building $building)
    {
        $address = $building->address;

        return $this->item($address, new AddressTransformer);
    }
}
