<?php

namespace App\Transformers;

use App\Models\Building;
use App\Models\Contract;
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
            'default_contract_id' => $model->default_contract_id,
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
            'resident_format' => $model->resident_format,
            'nation' => $model->nation,
            'type' => $model->type,
        ];

        if ($model->relationExists('settings')) {
            $response['settings'] = $model->settings;
        }

        if ($model->relationExists('default_contract')) {
            $response['default_contract'] = (new ContractTransformer())->transform($model->default_contract);
        }

        if ($model->relationExists('user')) {
            $response['user'] = (new UserTransformer)->transform($model->user);
        }

        $response['media'] = [];
        if ($model->contracts || $model->relationExists('contracts')) { // @TODO delete reloading
            $response['contracts'] = (new ContractTransformer())->transformCollection($model->contracts);

            if (!empty($response['contracts'][0]['building'])) {
                $response['building'] = $response['contracts'][0]['building'];
            }

            if (!empty($response['contracts'][0]['unit'])) {
                $response['unit'] = $response['contracts'][0]['unit'];
            }

            if (!empty($response['contracts'][0]['media'])) {
                $response['media'] = $response['contracts'][0]['media'];
            }

            if (!empty($response['contracts'][0]['address'])) {
                $response['address'] = $response['contracts'][0]['address'];
            }

            $allCount = $model->contracts->count();
            $activeCount = $model->contracts->where('status', Contract::StatusActive)->count();

            $response['active_contracts_count'] = $activeCount;
            $response['inactive_contracts_count'] = $allCount - $activeCount;
            $response['total_contracts_count'] = $allCount;
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
