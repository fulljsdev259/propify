<?php

namespace App\Transformers;

use App\Models\Building;

/**
 * Class BuildingSimpleTransformer.
 *
 * @package namespace App\Transformers;
 */
class BuildingSimpleTransformer extends BaseTransformer
{
    /**
     * Transform the Building entity.
     *
     * @param \App\Models\Building $model
     *
     * @return array
     */
    public function transform(Building $model)
    {
        $response = $this->getAttributesIfExists($model, [
            'id',
            'building_format',
            'label',
            'description',
            'floor_nr',
            'under_floor',
            'basement',
            'attic',
            'internal_building_id',
        ]);
        $response['created_at'] = $model->created_at ? $model->created_at->format('Y-m-d') : '';

        $response = $this->includeRelationIfExists($model, $response, [
            'address' => AddressTransformer::class,
            'quarter' => QuarterTransformer::class,
        ]);

        return $response;
    }
}
