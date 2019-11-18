<?php

namespace App\Transformers;

use App\Models\Tag;

/**
 * Class TagTransformer.
 *
 * @package namespace App\Transformers;
 */
class TagTransformer extends BaseTransformer
{
    /**
     * @param Tag $model
     * @return array
     */
    public function transform(Tag $model)
    {
        $response = [
            'id' => $model->id,
            'name' => $model->name,
        ];

        return $response;
    }
}
