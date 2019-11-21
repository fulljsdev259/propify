<?php

namespace App\Transformers;

use App\Models\User;
use Illuminate\Support\Str;

/**
 * Class UserTransformer.
 *
 * @package namespace App\Transformers;
 */
class UserTransformer extends BaseTransformer
{
    /**
     * Transform the User entity.
     *
     * @param \App\Models\User $model
     *
     * @return array
     */
    public function transform(User $model)
    {
        $response = $this->getAttributesIfExists($model, [
            'id',
            'name',
            'email',
            'phone',
            'avatar',
            'avatar_variations'
        ]);

        if ($model->relationExists('settings')) {
            $response['settings'] = $model->settings->toArray();
        }

        if ($model->relationExists('roles')) {
            $response['roles'] = $model->roles->toArray();
        }

        return $response;
    }
}
