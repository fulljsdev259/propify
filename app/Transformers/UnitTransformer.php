<?php

namespace App\Transformers;

use App\Models\Building;
use App\Models\Quarter;
use App\Models\Unit;

/**
 * Class UnitTransformer.
 *
 * @package namespace App\Transformers;
 */
class UnitTransformer extends BaseTransformer
{
    /**
     * Transform the Unit entity.
     *
     * @param Unit $model
     * @return array
     */
    public function transform(Unit $model)
    {
        $response = [
            'id' => $model->id,
            'unit_format' => $model->unit_format,
            'type' => $model->type,
            'name' => $model->name,
            'description' => $model->description,
            'building_id' => $model->building_id,
            'quarter_id' => $model->quarter_id,
            'floor' => $model->floor,
            'monthly_rent_net' => $model->monthly_rent_net,
            'monthly_rent_gross' => $model->monthly_rent_gross,
            'monthly_maintenance' => $model->monthly_maintenance,
            'room_no' => $model->room_no,
            'basement' => $model->basement,
            'attic' => $model->attic,
            'sq_meter' => $model->sq_meter,
            'residents' => [],
            'media' => [],
        ];

        $withCount = $model->getStatusRelationCounts();
        $response = array_merge($response, $withCount);


        $attributes = $model->attributesToArray();
        if (key_exists('total_relations_count', $attributes)) {
            $response['total_relations_count'] = $attributes['total_relations_count'];
        }

        if (key_exists('active_relations_count', $attributes)) {
            $response['active_relations_count'] = $attributes['active_relations_count'];
            if (key_exists('total_relations_count', $attributes)) {
                $response['inactive_relations_count'] = $attributes['total_relations_count'] - $attributes['active_relations_count'];
            }
        }

        if ($model->relationExists('building')) {
            $response['building'] = (new BuildingSimpleTransformer)->transform($model->building);
            $response['quarter_id'] = $model->building->quarter_id;
        } elseif(empty($response['quarter_id']) || ! empty($response['building_id'])) {
            $model->load('building:quarter_id,id');
            $response['quarter_id'] = $model->building->quarter_id ?? null;
        }

        if ($model->relationExists('quarter')) {
            $response['quarter'] = (new QuarterTransformer())->transform($model->quarter);
            $response[ 'internal_quarter_id'] = $model->quarter->internal_quarter_id ?? '';
        } else{
            $response[ 'internal_quarter_id'] = Quarter::where('id', $response['quarter_id'] )->value('internal_quarter_id');
        }

        if ($model->relationExists('media')) {
            $response['media'] = (new MediaTransformer())->transformCollection($model->media);
        }

        // @TODO correct is it needed and the it must be by building if exist other case by quarter
        if ($model->relationExists('address')) {
            $response['address'] = (new AddressTransformer)->transform($model->address);
        }

        if ($model->relationExists('relations')) {
            $response['relations'] = (new RelationTransformer())->transformCollection($model->relations);

            $residents = [];
            foreach ($response['relations'] as $relationData) {
                if (!empty($relationData['resident'])) {
                    $residents[] = $relationData['resident'];
                }
            }


            // @TODO delete
            if ($residents) {
                $response['residents'] = $residents;
            }
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
        if (isset($input['address'])) {
            unset($input['address']);
        }

        return $input;
    }
}
