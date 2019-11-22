<?php

namespace App\Http\Controllers;

use App\Models\PropertyManager;
use App\Models\ServiceProvider;
use App\Models\Settings;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use InfyOm\Generator\Utils\ResponseUtil;

/**
 * @SWG\Swagger(
 *   basePath="/api/v1",
 *   @SWG\Info(
 *     title="Propify API",
 *     version="1.0.0",
 *   )
 * )
 * This class should be parent class for other API controllers
 * Class AppBaseController
 */
/**
 *   @SWG\SecurityScheme(
 *     securityDefinition="passport-swaggervel_auth",
 *     description="OAuth2 grant provided by Laravel Passport",
 *     type="oauth2",
 *     authorizationUrl="/oauth/authorize",
 *     tokenUrl="/oauth/token",
 *     flow="accessCode",
 *     scopes={
 *       *
 *     }
 *   ),
 */
class AppBaseController extends Controller
{
    /**
     * @param $result
     * @param $message
     * @return mixed
     */
    public function sendResponse($result, $message)
    {
        return Response::json(ResponseUtil::makeResponse($message, $result));
    }

    /**
     * @param $error
     * @param int $code
     * @return mixed
     */
    public function sendError($error, $code = 404)
    {
        return Response::json(ResponseUtil::makeError($error), $code);
    }

    /**
     * get assignees related users
     *
     * @param $assignees
     * @return mixed
     */
    public function getAssignedUsersBy($assignees)
    {
        $userType = get_morph_type_of(User::class);
        $userIds = $assignees->where('assignee_type', $userType)->pluck('assignee_id');
        $raw = DB::raw('(select `roles`.name from roles inner join role_user on roles.id = role_user.role_id where role_user.user_id = users.id) as role');
        $users = User::select('id', 'name', 'email',$raw)
            ->whereIn('id', $userIds)
            ->get();

        return [$userType => $users];
    }

    /**
     * get assignees related property managers
     *
     * @param $assignees
     * @return mixed
     */
    public function getAssignedManagersBy($assignees)
    {
        $companyName = Settings::value('name');
        $managerType = get_morph_type_of(PropertyManager::class);
        $managerIds = $assignees->where('assignee_type', $managerType)->pluck('assignee_id');
        $raw = DB::raw('(select email from users where users.id = property_managers.user_id) as email,
                (select avatar from users where users.id = property_managers.user_id) as avatar, 
                Concat(first_name, " ", last_name) as name, 
                (select `roles`.name from roles inner join role_user on roles.id = role_user.role_id where role_user.user_id = property_managers.user_id) as role');
        $managers = PropertyManager::select('id', 'user_id', $raw)
            ->whereIn('id', $managerIds)
            ->get();
        $managers->map(function ($manager) use ($companyName) {
            $manager->company_name = $companyName;
        });

        return [$managerType => $managers];
    }

    /**
     * Get assignees related service providers
     *
     * @param $assignees
     * @return mixed
     */
    public function getAssignedProvidersBy($assignees)
    {
        $providerType = get_morph_type_of(ServiceProvider::class);
        $providerIds = $assignees->where('assignee_type', $providerType)->pluck('assignee_id');
        $raw = DB::raw('(select avatar from users where users.id = service_providers.user_id) as avatar, 
                (select `roles`.name from roles inner join role_user on roles.id = role_user.role_id where role_user.user_id = service_providers.user_id) as role');
        $providers = ServiceProvider::select('id', 'email', 'company_name', 'category', DB::raw('Concat(first_name, " ", last_name) as name'), 'user_id', $raw)
            ->whereIn('id', $providerIds)
            ->get();

        return [ $providerType => $providers];
    }

    /**
     * Get assignees related item details
     *
     * @param $assignees
     * @param $classes
     * @return mixed
     */
    public function getAssigneesRelated($assignees, $classes)
    {
        $data = [];

        if (in_array(PropertyManager::class, $classes)) {
            $data += $this->getAssignedManagersBy($assignees);
        }

        if (in_array(User::class, $classes)) {
            $data += $this->getAssignedUsersBy($assignees);
        }

        if (in_array(ServiceProvider::class, $classes)) {
            $data += $this->getAssignedProvidersBy($assignees);
        }

        foreach ($assignees as $index => $assignee) {
            $related = null;
            if (key_exists($assignee->assignee_type, $data)) {
                $items = $data[$assignee->assignee_type];
                $related = $items->where('id', $assignee->assignee_id)->first();
            }

            $assignee->related = $related;
        }
        return $assignees;
    }

}
