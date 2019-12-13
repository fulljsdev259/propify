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
        $response = $this->getAttributesIfExists($model, [
            'id',
            'name',
            'unit_id',
            'created_at',
        ]);

        $response = $this->includeRelationIfExists($model, $response, [
            'media' => MediaTransformer::class
        ]);

        return $response;
    }
}
