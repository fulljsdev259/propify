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
        $response =  [
            'id' => $model->id,
            'name' => $model->name,
            'email' => $model->email,
            'phone' => $model->phone,
            'avatar' => $model->avatar,
            'avatar_variations' => $model->avatar_variations
        ];

        if ($model->relationExists('settings')) {
            $response['settings'] = $model->settings->toArray();
        }

        if ($model->relationExists('roles')) {
            $response['roles'] = $model->roles->toArray();
        }

        return $response;
    }
}
