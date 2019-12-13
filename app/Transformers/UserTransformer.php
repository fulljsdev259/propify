<?php

namespace App\Transformers;

use App\Models\PropertyManager;
use App\Models\ServiceProvider;
use App\Models\Settings;
use App\Models\User;

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

    public function transformAdmins($users, $request)
    {
        if ($request->show_company_name ||  $request->function || $request->edit_details) {
            $users->load('service_provider:id,company_name,category,user_id', 'property_manager:id,user_id');
        }

        return $users->map(function ($user) use ($request) {
            $response = $user->toArray();
            if ($request->function) {
                $response['function'] = $this->getRoleFormatted($user);
            }

            if ($request->show_company_name) {
                $response['company_name'] = $this->getCompanyName($user);
            }

            if ($request->edit_details) {
                $response['edit_id'] = $user->property_manager->id ?? $user->service_provider->id ?? null;
                $response['type'] = get_morph_type_of($user->property_manager ? PropertyManager::class : ServiceProvider::class);
            }

            unset($response['property_manager']);
            unset($response['service_provider']);
            return $response;
        });
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
