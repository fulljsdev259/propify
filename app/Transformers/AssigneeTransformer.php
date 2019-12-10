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
            $response = [
                'id' => $model->id,
                'edit_id' => $model->assignee_id, // @TODO correct need or not
                'type' => $model->assignee_type, // @TODO correct need or not
                'email' => $model->related->email,
                'name' => $model->related->name,
                'company_name' => $model->related->company_name,
                'avatar' => $avatar,
                'sent_email' => $model->sent_email ? true : false,
                'role' => $model->related->role,
                'function' => $this->getRoleFormatted($model)
            ];

            return $response;

        }

        $response = [
            'id' => $model->id,
            'edit_id' => $model->assignee_id, // @TODO correct need or not
            'type' => $model->assignee_type, // @TODO correct need or not
            'email' =>  'User deleted',
            'name' => 'User deleted',
            'company_name' => 'User deleted',
            'avatar' => 'User deleted',
            'sent_email' => $model->sent_email ? true : false,
            'role' => 'User deleted',
            'function' => $this->getRoleFormatted($model)
        ];

        return $response;
    }

    protected function getRoleFormatted($model)
    {
        $related = $model->related;
        if (empty($related)) {
            return 'User deleted';
        }

        if ($related->service_provider) {
            return $related->category ?? '';
        }

        if ($related->service_provider->property_manager) {
            return 'manager';
        }


        return 'administrator';
    }
}
