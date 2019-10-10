<?php

namespace App\Transformers;

use App\Models\PinboardView;

/**
 * Class PinboardViewTransformer.
 *
 * @package namespace App\Transformers;
 */
class PinboardViewTransformer extends BaseTransformer
{
    protected $defaultIncludes = [];

    /**
     * Transform the PinboardViewTransformer entity.
     *
     * @param \App\Models\PinboardView $model
     *
     * @return array
     */
    public function transform(PinboardView $model)
    {
        $ut = new UserTransformer();
        $ret = [
            'id' => $model->id,
            'views' => $model->views,
            'created_at' => $model->created_at->toDateTimeString(),
            'updated_at' => $model->updated_at->toDateTimeString(),
            'tenant_id' => $model->resident_id, // @TODO delete
            'resident_id' => $model->resident_id,
        ];

        if ($model->relationExists('user')) {
            $ret['user'] = $ut->transform($model->user);
        }

        return $ret;
    }
}
