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
        $response = $this->getAttributesIfExists($model, [
            'id',
            'resident_id',
            'unit_id',
            'quarter_id',
            'type',
            'status',
            'relation_format',
            'deposit_type',
            'deposit_status',
            'deposit_amount',
            'start_date',
            'end_date',
            'monthly_rent_net',
            'monthly_rent_gross',
            'monthly_maintenance',
            'requests_count'
        ]);

        $response['garant'] = 0;

        if ($model->start_date) {
            $response['start_date'] = $model->start_date->format('Y-m-d');
        }

        if ($model->end_date) {
            $response['end_date'] = $model->end_date->format('Y-m-d');
        }

        $response = $this->includeRelationIfExists($model, $response, [
            'resident' => ResidentTransformer::class,
            'requests' => RequestTransformer::class,
            'media' => MediaTransformer::class,
        ]);

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

//        if ($model->relationExists('garant_residents')) {
//            $response['residents'] = $model->garant_residents->map(function ($item) {
//                return $item->only('id', 'first_name', 'last_name');
//            });
//        }

        return $response;
    }
}
