<?php

namespace App\Transformers;

use App\Models\UserFilter;

/**
 * Class UserFilterTransformer.
 *
 * @package namespace App\Transformers;
 */
class UserFilterTransformer extends BaseTransformer
{
    /**
     * Transform the UserFilter entity.
     *
     * @param \App\Models\UserFilter $model
     *
     * @return array
     */
    public function transform(UserFilter $model)
    {
        $response = $this->getAttributesIfExists($model, [
            'id',
            'user_id',
            'title',
            'menu',
            'options_url',
            'fields_data'
        ]);


        if ($model->relationExists('user')) {
            $response['user'] = (new UserTransformer())->transform($model->user);
        }

        return $response;
    }
}
