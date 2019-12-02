<?php

namespace App\Transformers;

use App\Models\Relation;

/**
 * Class RelationTransformer.
 *
 * @package namespace App\Transformers;
 */
class RelationTransformer extends BaseTransformer
{
    /**
     * @param Relation $model
     * @return array
     */
    public function transform(Relation $model)
    {
        $response = [
            'id' => $model->id,
            'resident_id' => $model->resident_id,
            'unit_id' => $model->unit_id,
            'quarter_id' => $model->quarter_id,
            'type' => $model->type,
            'status' => $model->status,
            'relation_format' => $model->relation_format,
            'deposit_type' => $model->deposit_type,
            'deposit_status' => $model->deposit_status,
            'deposit_amount' => $model->deposit_amount,
            'start_date' => $model->start_date,
            'end_date' => $model->end_date,
            'monthly_rent_net' => $model->monthly_rent_net,
            'monthly_rent_gross' => $model->monthly_rent_gross,
            'monthly_maintenance' => $model->monthly_maintenance,
        ];
        $response['garant'] = 0;

        if (! is_null($model->requests_count)) {
            $response['requests_count'] = $model->requests_count;
        }

        if ($model->start_date) {
            $response['start_date'] = $model->start_date->format('Y-m-d');
        }

        if ($model->end_date) {
            $response['end_date'] = $model->end_date->format('Y-m-d');
        }

        if ($model->relationExists('resident')) {
            $response['resident'] = (new ResidentTransformer())->transform($model->resident);
        }

        if ($model->relationExists('requests')) {
            $response['requests'] = (new RequestTransformer())->transform($model->requests);
        }

        if ($model->relationExists('quarter')) {
            $response['quarter'] = (new QuarterTransformer())->transform($model->quarter);

            if ($model->quarter->relationExists('address')) {
                $response['address'] = (new AddressTransformer)->transform($model->quarter->address);
                unset($response['quarter']['address']);
            }
        }

        if ($model->relationExists('unit')) {
            $response['unit'] = (new UnitTransformer)->transform($model->unit);
            if ($model->unit->relationExists('building')) {
                $response['building'] = (new BuildingTransformer)->transform($model->unit->building);

                if ($model->unit->building->relationExists('address')) {
                    $response['address'] = (new AddressTransformer)->transform($model->unit->building->address);
                    unset($response['building']['address']);
                }

            }
        }

        if ($model->relationExists('media')) {
            $response['media'] = (new MediaTransformer)->transformCollection($model->media);
        }

//        if ($model->relationExists('garant_residents')) {
//            $response['residents'] = $model->garant_residents->map(function ($item) {
//                return $item->only('id', 'first_name', 'last_name');
//            });
//        }

        return $response;
    }
}
