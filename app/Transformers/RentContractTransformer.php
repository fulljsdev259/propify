<?php

namespace App\Transformers;

use App\Models\RentContract;

/**
 * Class RentContractTransformer.
 *
 * @package namespace App\Transformers;
 */
class RentContractTransformer extends BaseTransformer
{
    /**
     * @param RentContract $model
     * @return array
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function transform(RentContract $model)
    {
        $response = [
            'id' => $model->id,
            'tenant_id' => $model->tenant_id,
            'building_id' => $model->building_id,
            'unit_id' => $model->unit_id,
            'type' => $model->type,
            'duration' => $model->duration,
            'status' => $model->status,
            'rent_contract_format' => $model->rent_contract_format,
            'deposit_type' => $model->deposit_type,
            'deposit_status' => $model->deposit_status,
            'deposit_amount' => $model->deposit_amount,
            'start_date' => $model->start_date,
            'end_date' => $model->end_date,
            'monthly_rent_net' => $model->monthly_rent_net,
            'monthly_rent_gross' => $model->monthly_rent_gross,
            'monthly_maintenance' => $model->monthly_maintenance,
        ];

        if ($model->start_date) {
            $response['start_date'] = $model->start_date->format('Y-m-d');
        }

        if ($model->end_date) {
            $response['end_date'] = $model->end_date->format('Y-m-d');
        }

        if ($model->relationExists('tenant')) {
            $response['tenant'] = (new TenantTransformer())->transform($model->tenant);
        }

        if ($model->relationExists('building')) {
            $response['building'] = (new BuildingTransformer)->transform($model->building);

            if ($model->building->relationExists('address')) {
                $response['address'] = (new AddressTransformer)->transform($model->building->address);
                unset($response['building']['address']);
            }
        }

        if ($model->relationExists('unit')) {
            $response['unit'] = (new UnitTransformer)->transform($model->unit);
        }

        if ($model->relationExists('media')) {
            $response['media'] = (new MediaTransformer)->transformCollection($model->media);
        }

        return $response;
    }
}
