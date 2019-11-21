<?php

namespace App\Transformers;

use App\Models\Quarter;
use App\Models\Relation;
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
        $response = $this->getAttributesIfExists($model, [
            'id',
            'unit_format',
            'type',
            'name',
            'description',
            'building_id',
            'quarter_id',
            'floor',
            'monthly_rent_net',
            'monthly_rent_gross',
            'monthly_maintenance',
            'room_no',
            'basement',
            'attic',
            'sq_meter',
        ]);

        // @TODO maybe delete
        $response['residents'] = [];
        $response['media'] = [];

        $relationsStatusCount = $model->getRelationStatusCounts();
        $requestsStatusCount = $model->getStatusRelationCounts();
        $response = array_merge($response, $relationsStatusCount, $requestsStatusCount);


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
        } else {
            $quarter = Quarter::find($response['quarter_id']);
            if ($quarter) {
                $response['internal_quarter_id'] = $quarter->internal_quarter_id;
                $response['quarter'] = (new QuarterTransformer())->transform($quarter);
            }
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
     * @param Unit $model
     * @return array
     */
    public function transformForIndex(Unit $model)
    {
        $response = $this->transform($model);
        $relations = collect($response['relations'] ?? []);
        $latestRelation = $relations->sortByDesc('start_date')->first();
        $response['status'] = $latestRelation['status'] ?? Relation::StatusInActive;
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
