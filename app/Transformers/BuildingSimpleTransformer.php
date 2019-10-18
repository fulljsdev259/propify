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
        $response = [
            'id' => $model->id,
            'building_format' => $model->building_format,
            'name' => $model->name,
            'label' => $model->label,
            'description' => $model->description,
            'floor_nr' => $model->floor_nr,
            'under_floor' => $model->under_floor,
            'basement' => $model->basement,
            'attic' => $model->attic,
            'internal_building_id' => $model->internal_building_id,
            'created_at' => $model->created_at->format('Y-m-d'),
        ];

        if ($model->relationExists('quarter')) {
            $response['quarter'] = (new QuarterTransformer)->transform($model->quarter);
        }

        return $response;
    }
}
