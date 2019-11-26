<?php

namespace App\Transformers;

use App\Models\PropertyManager;
use App\Models\RequestAssignee;
use App\Models\ServiceProvider;
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
            'email' => $model->related ? $model->related->email :  $model->assignee_type . ' deleted',
            'name' => $model->related ? $model->related->name : $model->assignee_type . ' deleted',
            'company_name' => $model->related ? $model->related->company_name : $model->assignee_type . ' deleted',
            'avatar' => $avatar,
            'sent_email' => $model->sent_email ? true : false,
            'role' => $model->related ? $model->related->role : $model->assignee_type . ' deleted',
            'function' => $this->getRoleFormatted($model)
        ];

        return $response;
    }

    protected function getRoleFormatted($model)
    {
        $related = $model->related;
        if (empty($related)) {
            return $model->assignee_type . ' deleted';
        }

        if ($model->assignee_type == get_morph_type_of(ServiceProvider::class)) {
            return $related->category ?? '';
        }

        if ($model->assignee_type == get_morph_type_of(PropertyManager::class)) {
            return 'manager';
        }


        return 'administrator';
    }
}
