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
     * @param PinboardView $model
     * @return array
     */
    public function transform(PinboardView $model)
    {
        $ret = [
            'id' => $model->id,
            'views' => $model->views,
            'created_at' => $model->created_at->toDateTimeString(),
            'updated_at' => $model->updated_at->toDateTimeString(),
            'resident_id' => $model->resident_id,
        ];

        if ($model->relationExists('resident')) {
            $ret['resident'] = (new ResidentTransformer())->transform($model->resident);
        }

        return $ret;
    }
}
