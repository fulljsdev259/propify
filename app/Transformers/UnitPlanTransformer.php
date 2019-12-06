<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Models\UnitPlan;

/**
 * Class UnitPlanTransformer.
 *
 * @package namespace App\Transformers;
 */
class UnitPlanTransformer extends BaseTransformer
{
    /**
     * Transform the UnitPlan entity.
     *
     * @param \App\Models\UnitPlan $model
     *
     * @return array
     */
    public function transform(UnitPlan $model)
    {
        $response = [
            'id' => (int)$model->id,
            'name' => $model->name,
            'description' => $model->description,
            'primary' => (boolean)$model->primary,
            'unit_id' => (int)$model->unit_id,
            'created_at' => $model->created_at,
        ];

        if ($model->relationLoaded('media')) {
            $response['media'] = (new MediaTransformer())->transformCollection($model->media);
        }

        return $response;
    }
}
