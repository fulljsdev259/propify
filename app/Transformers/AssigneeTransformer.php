<?php

namespace App\Transformers;

use App\Models\RequestAssignee;
use App\Models\User;

/**
 * Class RequestTransformer
 *
 * @package namespace App\Transformers;
 */
class AssigneeTransformer extends BaseTransformer
{
    /**
     * Transform the ServiceProvider entity.
     *
     * @param $model
     *
     * @return array
     */
    public function transformAssignee($model)
    {
        if ($model->related) {
            $user = new User(['avatar' => $model->related->avatar]);
            $user->id = $model->related->user_id;
            $avatar = $user->avatar;
        } else {
            $avatar = 'incorrect relation';
        }

        $response = [
            'id' => $model->id,
            'edit_id' => $model->assignee_id,
            'type' => $model->assignee_type,
            'email' => $model->related ? $model->related->email : 'incorrect relation',
            'name' => $model->related ? $model->related->name : 'incorrect relation',
            'avatar' => $avatar,
            'role' => $model->related ? $model->related->role : 'incorrect relation'
        ];

        return $response;
    }
}
