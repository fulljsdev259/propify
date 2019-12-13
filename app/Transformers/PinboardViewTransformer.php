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
        $response = $this->getAttributesIfExists($model, [
            'id',
            'views',
            'created_at',
            'updated_at',
            'resident_id',
        ]);

        $response = $this->includeRelationIfExists($model, $response, [
            'resident' => ResidentTransformer::class,
        ]);

        return $response;
    }
}
