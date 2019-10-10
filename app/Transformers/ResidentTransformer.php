<?php

namespace App\Transformers;

use App\Models\Building;
use App\Models\RentContract;
use App\Models\Resident;

/**
 * Class ResidentTransformer.
 *
 * @package namespace App\Transformers;
 */
class ResidentTransformer extends BaseTransformer
{

    /**
     * @param Resident $model
     * @return array
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function transform(Resident $model)
    {
        $response = [
            'id' => $model->id,
            'title' => $model->title,
            'company' => $model->company,
            'first_name' => $model->first_name,
            'last_name' => $model->last_name,
            'birth_date' => $model->birth_date ? $model->birth_date->format('Y-m-d') : null,
            'birth_date_formatted' => $model->birth_date ? $model->birth_date->format('d.m.Y') : null,
            'mobile_phone' => $model->mobile_phone,
            'private_phone' => $model->private_phone,
            'work_phone' => $model->work_phone,
            'status' => $model->status,
            'tenant_format' => $model->resident_format, // @TODO delete
            'resident_format' => $model->resident_format,
            'nation' => $model->nation,
            'type' => $model->type,
        ];

        if ($model->relationExists('settings')) {
            $response['settings'] = $model->settings;
        }

        if ($model->relationExists('default_rent_contract')) {
            $response['default_rent_contract'] = (new RentContractTransformer())->transform($model->default_rent_contract);
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
        if ($model->rent_contracts || $model->relationExists('rent_contracts')) { // @TODO delete reloading
            $response['rent_contracts'] = (new RentContractTransformer())->transformCollection($model->rent_contracts);

            if (!empty($response['rent_contracts'][0]['building'])) {
                $response['building'] = $response['rent_contracts'][0]['building'];
            }

            if (!empty($response['rent_contracts'][0]['unit'])) {
                $response['unit'] = $response['rent_contracts'][0]['unit'];
            }

            if (!empty($response['rent_contracts'][0]['media'])) {
                $response['media'] = $response['rent_contracts'][0]['media'];
            }

            if (!empty($response['rent_contracts'][0]['address'])) {
                $response['address'] = $response['rent_contracts'][0]['address'];
            }

            $allCount = $model->rent_contracts->count();
            $activeCount = $model->rent_contracts->where('status', RentContract::StatusActive)->count();

            $response['active_rent_contracts_count'] = $activeCount;
            $response['inactive_rent_contracts_count'] = $allCount - $activeCount;
            $response['total_rent_contracts_count'] = $allCount;
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
