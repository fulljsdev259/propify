<?php

namespace App\Transformers;

use App\Models\PropertyManager;

/**
 * Class PropertyManagerSimpleTransformer.
 *
 * @package namespace App\Transformers;
 */
class PropertyManagerSimpleTransformer extends BaseTransformer
{
    /**
     * Transform the PropertyManager entity.
     *
     * @param PropertyManager $model
     *
     * @return array
     */
    public function transform(PropertyManager $model)
    {
        $response = $this->getAttributesIfExists($model, [
            'id',
            'type',
            'property_manager_format',
        ]);

        $response = $this->includeRelationIfExists($model, $response, [
            'user' => UserTransformer::class,
        ]);

        return $response;
    }
}
