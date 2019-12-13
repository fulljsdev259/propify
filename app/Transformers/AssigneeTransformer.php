<?php

namespace App\Transformers;

use App\Models\PropertyManager;
use App\Models\RequestAssignee;
use App\Models\ServiceProvider;
use App\Models\Settings;
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
        $user = $model->user;
        if ($user) {
            $response = [
                'id' => $model->id,
                'edit_id' => $user->property_manager->id ?? $user->service_provider->id ?? null,
                'type' => get_morph_type_of($user->service_provider ? ServiceProvider::class : PropertyManager::class),
                'email' => $user->email,
                'name' => $user->name,
                'company_name' => $this->getCompanyName($user),
                'avatar' => $user->avatar,
                'sent_email' => $model->sent_email ? true : false,
                'role' => $user->roles->first()->name ?? 'unknown',
                'function' => $this->getRoleFormatted($user)
            ];

            return $response;

        }

        $response = [
            'id' => $model->id,
            'edit_id' => null,
            'type' => null,
            'email' =>  'User deleted',
            'name' => 'User deleted',
            'company_name' => 'User deleted',
            'avatar' => 'User deleted',
            'sent_email' => $model->sent_email ? true : false,
            'role' => 'User deleted',
            'function' => 'User deleted',
        ];

        return $response;
    }

    /**
     * @param $user
     * @return mixed
     */
    protected function getCompanyName($user)
    {
       return $user->service_provider->company_name ?? \Cache::remember(
               'company_name',
               60,
                   function () {
                       return Settings::value('name');
                   }
               );
    }

    /**
     * @param $user
     * @return string
     */
    protected function getRoleFormatted($user)
    {
        if ($user->service_provider) {
            return ServiceProvider::Category[$user->service_provider->category] ?? '';
        }
        return $user->roles->first()->name ?? 'unknown role'; //unknown role must be not happen
    }
}
