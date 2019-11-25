<?php

namespace App\Http\Controllers\API;

use App\Criteria\Common\RequestCriteria;
use App\Criteria\User\FilterByAdminCriteria;
use App\Criteria\User\FilterByExcludeAssigneesCriteria;
use App\Criteria\User\FilterByRolesCriteria;
use App\Criteria\User\WhereCriteria;
use App\Http\Controllers\AppBaseController;
use App\Http\Requests\API\User\ChangePasswordRequest;
use App\Http\Requests\API\User\CheckEmailRequest;
use App\Http\Requests\API\User\CreateRequest;
use App\Http\Requests\API\User\DeleteRequest;
use App\Http\Requests\API\User\ListRequest;
use App\Http\Requests\API\User\ShowRequest;
use App\Http\Requests\API\User\UpdateLoggedInRequest;
use App\Http\Requests\API\User\UpdateRequest;
use App\Http\Requests\API\User\UploadImageRequest;
use App\Models\Audit;
use App\Models\AuditableModel;
use App\Models\Building;
use App\Models\Relation;
use App\Models\ServiceProvider;
use App\Models\Settings;
use App\Models\User;
use App\Repositories\UserRepository;
use App\Transformers\ResidentTransformer;
use App\Transformers\UserTransformer;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;

/**
 * Class UserController
 * @package App\Http\Controllers\API
 */
class UserAPIController extends AppBaseController
{
    /** @var  UserRepository */
    private $userRepository;

    /**
     * UserAPIController constructor.
     * @param UserRepository $userRepo
     */
    public function __construct(UserRepository $userRepo)
    {
        $this->userRepository = $userRepo;
    }

    /**
     * @SWG\Get(
     *      path="/users",
     *      summary="Get a listing of the Users.",
     *      tags={"User"},
     *      description="Get all Users",
     *      produces={"application/json"},
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  type="array",
     *                  @SWG\Items(ref="#/definitions/User")
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     *
     * @param ListRequest $request
     * @return mixed
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function index(ListRequest $request)
    {
        $this->userRepository->pushCriteria(new RequestCriteria($request));
        $this->userRepository->pushCriteria(new FilterByRolesCriteria($request));
        $this->userRepository->pushCriteria(new FilterByAdminCriteria($request));
        $this->userRepository->pushCriteria(new FilterByExcludeAssigneesCriteria($request));
        $this->userRepository->pushCriteria(new LimitOffsetCriteria($request));

        $getAll = $request->get('get_all', false);
        if ($getAll) {
            if ($request->get_role) {
                $this->userRepository->with('roles');
            }
            $users = $this->userRepository->get();
            if ($request->function) {
                $serviceProviders = ServiceProvider::whereIn('user_id', $users->pluck('id'))->get(['id', 'category', 'user_id']);
                $response = [];
                foreach ($users as $user) {
                    $singleData = $user->toArray();
                    $serviceProvider = $serviceProviders->where('user_id', $user->id)->first();
                    if ($serviceProvider) {
                        $category = ServiceProvider::Category[$serviceProvider->category] ?? '';
                        $singleData['function'] = $category;
                    } else {
                        $singleData['function'] =  $user->roles->first()->name ?? 'unknown role'; //unknown role must be not happen
                    }
                    $response[] = $singleData;
                }
            } else {
                $response = $users->toArray();
            }
            return $this->sendResponse($response, 'Users retrieved successfully');
        }

        $perPage = $request->get('per_page', env('APP_PAGINATE', 10));
        $users = $this->userRepository->with('roles')->paginate($perPage);
        return $this->sendResponse($users->toArray(), 'Users retrieved successfully');
    }

    /**
     * @param ListRequest $request
     * @return mixed
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function allAdmins(ListRequest $request)
    {
        $request->merge([
            'get_admins' => true,
            'get_all' => true,
            'get_role' => true,
        ]);
        return $this->index($request);
    }

    /**
     * @SWG\Get(
     *      path="/users/requestManagers",
     *      summary="Get a listing of the requestManagers Users.",
     *      tags={"User"},
     *      description="Get all requestManagers Users",
     *      produces={"application/json"},
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  type="array",
     *                  @SWG\Items(ref="#/definitions/User")
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     *
     * @param ListRequest $request
     * @return mixed
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function requestManagers(ListRequest $request)
    {
        $request->request->set('roles', ['administrator', 'manager']);
        $this->userRepository->pushCriteria(new RequestCriteria($request));
        $this->userRepository->pushCriteria(new FilterByRolesCriteria($request));
        $this->userRepository->pushCriteria(new LimitOffsetCriteria($request));

        $perPage = $request->get('per_page', env('APP_PAGINATE', 10));
        $users = $this->userRepository
            ->with('roles')
            ->scope('allRequestStatusCount')
            ->paginate($perPage);

        return $this->sendResponse($users->toArray(), 'Users retrieved successfully');
    }

    /**
     * @SWG\Post(
     *      path="/users",
     *      summary="Store a newly created User in storage",
     *      tags={"User"},
     *      description="Store User",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="User that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/User")
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  ref="#/definitions/User"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     *
     * @param CreateRequest $request
     * @return mixed
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function store(CreateRequest $request)
    {
        $input = $request->all();

        $users = $this->userRepository->create($input);
        $users->load('settings')->load('roles');

        return $this->sendResponse($users->toArray(), __('models.user.saved'));
    }

    /**
     * @SWG\Get(
     *      path="/users/{id}",
     *      summary="Display the specified User",
     *      tags={"User"},
     *      description="Get User",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of User",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  ref="#/definitions/User"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     *
     * @param $id
     * @param ShowRequest $request
     * @return mixed
     */
    public function show($id, ShowRequest $request)
    {
        /** @var User $user */
        $user = $this->userRepository->findWithoutFail($id);
        if (empty($user)) {
            return $this->sendError(__('models.user.errors.not_found'));
        }

        $user->load('settings')->load('roles');
        return $this->sendResponse($user->toArray(), 'User retrieved successfully');
    }

    /**
     * @SWG\Get(
     *      path="/users/me",
     *      summary="Display the Logged In User",
     *      tags={"User"},
     *      description="Get Logged In User",
     *      produces={"application/json"},
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  ref="#/definitions/User"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     *
     * @param Request $request
     * @return mixed
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function showLoggedIn(Request $request)
    {
        /** @var User $user */
        $user = $request->user();
        $user->load([
            'settings',
            'roles.perms',
            'resident' => function ($q) {
                $q->with([
                    'relations' => function ($q) {
                        $q->with('building.address', 'unit', 'media');
                    },
                ]);
            },
            'propertyManager:id,user_id',
            'serviceProvider:id,user_id'
			
        ]);
        if ($user->property_manager) {
            $user->property_manager_id = $user->property_manager->id;
        }

        if ($user->service_provider) {
            $user->service_provider_id = $user->service_provider->id;
        }

        unset($user->service_provider);
        unset($user->property_manager);
        $user->unread_notifications_count = $user->unreadNotifications()->count();
        $resident = $user->resident;

        if ($resident) {
            unset($user->resident);
            $resident->load(['relations' => function($q) {
                $q->where('status' , Relation::StatusActive)
                    ->with('building.address', 'unit');
            }]);

            $contactEnable = (bool) $this->getResidentContactEnable($resident);
            $resident = (new ResidentTransformer())->transform($resident);

            $buildingIds = collect($resident['relations'])->pluck('building_id');
            $buildings = Building::select('id')
                ->whereIn('id', $buildingIds)
                ->with('property_managers:property_managers.id')
                ->with([
                    'relations' => function ($q) use ($resident) {
                        $q->where('status', Relation::StatusActive)
                            ->where('resident_id', '!=', $resident)
                            ->select('building_id', 'resident_id');
                    }
                ])
                ->get();

            $resident['contact_enable'] = $contactEnable;
            $resident['neighbour_count'] = $buildings->pluck('relations.*.resident_id')->collapse()->unique()->count();;
            $resident['property_manager_count'] = $buildings->pluck('property_managers.*.id')->collapse()->unique()->count();
            $user->setAttribute('resident', $resident);
        }

        return $this->sendResponse($user->toArray(), __('general.login_success'));
    }

    /**
     * @SWG\Put(
     *      path="/users/{id}",
     *      summary="Update the specified User in storage",
     *      tags={"User"},
     *      description="Update User",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of User",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="User that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/User")
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  ref="#/definitions/User"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     *
     * @param int $id
     * @param UpdateRequest $request
     * @return mixed
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function update(int $id, UpdateRequest $request)
    {
        $input = $request->all();

        /** @var User $user */
        $user = $this->userRepository->findWithoutFail($id);
        if (empty($user)) {
            return $this->sendError(__('models.user.errors.not_found'));
        }

        // image upload
        $fileData = base64_decode($request->get('image_upload', ''));
        if ($fileData) {
            try {
                $input['avatar'] = $this->userRepository->uploadImage($fileData, $user, $request->merge_in_audit);
            } catch (\Exception $e) {
                return $this->sendError(__('models.user.errors.image_upload') . $e->getMessage());
            }
        }

        $user = $this->userRepository->updateExistingUser($user, $input);

        $user->load('settings')->load('roles');

        $response = (new UserTransformer)->transform($user);
        return $this->sendResponse($response, __('models.user.saved'));
    }

    /**
     * @SWG\Put(
     *      path="/users/me",
     *      summary="Update the Logged In UserSettings in storage",
     *      tags={"User"},
     *      description="Update the Logged In UserSettings",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="UserSettings that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/User")
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  ref="#/definitions/User"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     *
     * @param UpdateLoggedInRequest $request
     * @return mixed
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function updateLoggedIn(UpdateLoggedInRequest $request)
    {
        /** @var User $user */
        $user = $request->user();
        $input = $request->all();

        if (isset($input['password_old']) && !Hash::check($input['password_old'], $user->password)) {
            return $this->sendError(__('models.user.errors.incorrect_password'));
        }

        // image upload
        $fileData = base64_decode($request->get('image_upload', ''));
        if ($fileData) {
            try {
                $input['avatar'] = $this->userRepository->uploadImage($fileData, $user, $request->merge_in_audit);
            } catch (\Exception $e) {
                return $this->sendError(__('models.user.errors.image_upload') . $e->getMessage());
            }
        }

        $user = $this->userRepository->updateExistingUser($user, $input);

        $user->load('settings')->load('roles');
        $response = (new UserTransformer)->transform($user);
        return $this->sendResponse($response, __('models.user.saved'));
    }

    /**
     * @SWG\Put(
     *      path="/users/me/change_password",
     *      summary="Change password for Logged In User",
     *      tags={"User"},
     *      description="Change password for Logged In User",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="UserSettings that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/User")
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  ref="#/definitions/User"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     *
     * @param ChangePasswordRequest $request
     * @return mixed
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function changePassword(ChangePasswordRequest $request)
    {
        $input = $request->all();
        $id = $request->user()->id;

        /** @var User $user */
        $user = $this->userRepository->findWithoutFail($id);
        if (empty($user)) {
            return $this->sendError(__('models.user.errors.not_found'));
        }

        if (!isset($input['password_old']) || !Hash::check($input['password_old'], $user->password)) {
            return $this->sendError(__('models.user.errors.incorrect_password'));
        }

        $user = $this->userRepository->with(['settings'])->update($input, $id);

        $response = (new UserTransformer)->transform($user);
        return $this->sendResponse($response, __('models.user.saved'));
    }

    /**
     * @SWG\Put(
     *      path="/users/{id}/upload_image",
     *      summary="Change profile image for selected User",
     *      tags={"User"},
     *      description="Change profile image for selected User",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="UserSettings that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/User")
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  ref="#/definitions/User"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     *
     * @param int $id
     * @param UploadImageRequest $request
     * @return mixed
     * @throws \OwenIt\Auditing\Exceptions\AuditingException
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function uploadImage(int $id, UploadImageRequest $request)
    {
        $input = $request->all();
        /** @var User $user */
        $user = $this->userRepository->findWithoutFail($id);
        if (empty($user)) {
            return $this->sendError(__('models.user.errors.not_found'));
        }

        // image upload
        $fileData = base64_decode($request->get('image_upload', ''));
        if ($fileData) {
            $mergeInAudit = $this->getMergedAudit($user, $request);
            try {
                $input['avatar'] = $this->userRepository->uploadImage($fileData, $user, $mergeInAudit);
            } catch (\Exception $e) {                
                return $this->sendError(__('models.user.errors.image_upload') . $e->getMessage());
            }
        }

        User::disableAuditing();
        $user = $this->userRepository->with(['settings'])->update($input, $id);
        User::enableAuditing();

        $response = (new UserTransformer)->transform($user);
        return $this->sendResponse($response, __('models.user.saved'));
    }

    /**
     * @param User $user
     * @param $request
     * @return Audit|null
     * @throws \OwenIt\Auditing\Exceptions\AuditingException
     */
    protected function getMergedAudit(User $user, $request)
    {
        if ($request->merge_in_audit) {
            return $request->merge_in_audit;
        }

        if ($user->resident) {
            return $user->resident->makeNewAudit(AuditableModel::EventAvatarUploaded);
        }

        if ($user->service_provider) {
            return $user->service_provider->makeNewAudit(AuditableModel::EventAvatarUploaded);
        }

        if ($user->property_manager) {
            return $user->property_manager->makeNewAudit(AuditableModel::EventAvatarUploaded);
        }

        return null;
    }

    /**
     * @SWG\Put(
     *      path="/users/me/upload_image",
     *      summary="Change profile image for Logged In User",
     *      tags={"User"},
     *      description="Change profile image for Logged In User",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="UserSettings that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/User")
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  ref="#/definitions/User"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     *
     * @param UploadImageRequest $request
     * @return mixed
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function uploadImageLoggedIn(UploadImageRequest $request)
    {
        $input = $request->all();
        $id = $request->user()->id;

        /** @var User $user */
        $user = $this->userRepository->findWithoutFail($id);
        if (empty($user)) {
            return $this->sendError(__('models.user.errors.not_found'));
        }

        // image upload
        $fileData = base64_decode($request->get('image_upload', ''));
        if ($fileData) {
            try {
                $input['avatar'] = $this->userRepository->uploadImage($fileData, $user, $request->merge_in_audit);
            } catch (\Exception $e) {
                return $this->sendError(__('models.user.errors.image_upload') . $e->getMessage());
            }
        }

        $user = $this->userRepository->with(['settings'])->update($input, $id);

        $response = (new UserTransformer)->transform($user);
        return $this->sendResponse($response, __('models.user.saved'));
    }

    /**
     * @SWG\Delete(
     *      path="/users/{id}",
     *      summary="Remove the specified User from storage",
     *      tags={"User"},
     *      description="Delete User",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of User",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  type="string"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     *
     * @param $id
     * @param DeleteRequest $request
     * @return mixed
     */
    public function destroy($id, DeleteRequest $request)
    {
        /** @var User $user */
        $user = $this->userRepository->findWithoutFail($id);
        if (empty($user)) {
            return $this->sendError(__('models.user.errors.not_found'));
        }

        $user->forceDelete();
        return $this->sendResponse($id, __('models.user.deleted'));
    }

    /**
     * @param DeleteRequest $request
     * @return mixed
     */
    public function destroyWithIds(DeleteRequest $request){
        $ids = $request->get('ids');

        try{
            User::destroy($ids);
        } catch (\Exception $e) {
            return $this->sendError(__('models.user.errors.deleted') . $e->getMessage());
        }

        return $this->sendResponse($ids, __('models.user.deleted'));
    }

    /**
     * @param CheckEmailRequest $request
     * @return mixed
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function checkEmail(CheckEmailRequest $request)
    {
        $email = $request->email;
        if (empty($email)) {
            return $this->sendError(__('models.user.errors.email_missing'));
        }

        $this->userRepository->pushCriteria(new WhereCriteria('email', $email));
        $isExists = $this->userRepository->exists();
        if ($isExists) {
            return $this->sendError(__('models.user.errors.email_already_exists', ['email' => $email]));
        }

        return $this->sendResponse($email, __('models.user.errors.email_not_exists', ['email' => $email]));

    }

    /**
     * @param $resident
     * @return bool
     */
    protected function getResidentContactEnable($resident)
    {
        $default = true; // @TODO relation related
//        $building = $resident->building; // always null
        $building = null;
        if ( ! $building || Building::ContactEnablesBasedSettings == $building->contact_enable) {
            $settings = Settings::first('contact_enable');
            return $settings->contact_enable ?? $default;
        }


        return Building::ContactEnablesShow == $building->contact_enable;
    }
}
