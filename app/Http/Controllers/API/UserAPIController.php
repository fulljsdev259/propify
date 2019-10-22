<?php

namespace App\Http\Controllers\API;

use App\Criteria\Common\RequestCriteria;
use App\Criteria\User\FilterByRolesCriteria;
use App\Criteria\User\WhereCriteria;
use App\Http\Controllers\AppBaseController;
use App\Http\Requests\API\User\ChangePasswordRequest;
use App\Http\Requests\API\User\CreateRequest;
use App\Http\Requests\API\User\DeleteRequest;
use App\Http\Requests\API\User\ListRequest;
use App\Http\Requests\API\User\ShowRequest;
use App\Http\Requests\API\User\UpdateLoggedInRequest;
use App\Http\Requests\API\User\UpdateRequest;
use App\Http\Requests\API\User\UploadImageRequest;
use App\Models\Building;
use App\Models\Settings;
use App\Models\User;
use App\Repositories\UserRepository;
use App\Transformers\MediaTransformer;
use App\Transformers\UserTransformer;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
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
        $this->userRepository->pushCriteria(new LimitOffsetCriteria($request));

        $getAll = $request->get('get_all', false);
        if ($getAll) {
            $users = $this->userRepository->get();
            return $this->sendResponse($users->toArray(), 'Users retrieved successfully');
        }

        $perPage = $request->get('per_page', env('APP_PAGINATE', 10));
        $users = $this->userRepository->with('roles')->paginate($perPage);
        return $this->sendResponse($users->toArray(), 'Users retrieved successfully');
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
                    'contracts' => function ($q) {
                        $q->with('building.address', 'unit', 'media');
                    },
                ]);
            },
            'propertyManager:id,user_id',
            'serviceProvider:id,user_id'
        ]);
        if ($user->propertyManager) {
            $user->property_manager_id = $user->propertyManager->id;
        }

        if ($user->serviceProvider) {
            $user->service_privider_id = $user->serviceProvider->id;
        }

        unset($user->serviceProvider);
        unset($user->propertyManager);
        $user->unread_notifications_count = $user->unreadNotifications()->count();
        $resident = $user->resident;

        if ($resident) {
            $resident->contact_enable = (bool) $this->getResidentContactEnable($resident);
            $resident->contracts->each(function ($contract) {
                if ($contract->relationExists('media')) {
                    $contract->setRelation('media', collect((new MediaTransformer)->transformCollection($contract->media)));
                } else {
                    $contract->media = [];
                }
            });
        }

        return $this->sendResponse($user->toArray(), 'User retrieved successfully');
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
                $input['avatar'] = $this->userRepository->uploadImage($fileData, $user);
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
                $input['avatar'] = $this->userRepository->uploadImage($fileData, $user);
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
            try {
                $input['avatar'] = $this->userRepository->uploadImage($fileData, $user);
            } catch (\Exception $e) {                
                return $this->sendError(__('models.user.errors.image_upload') . $e->getMessage());
            }
        }

        $user = $this->userRepository->with(['settings'])->update($input, $id);

        $response = (new UserTransformer)->transform($user);
        return $this->sendResponse($response, __('models.user.saved'));
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
                $input['avatar'] = $this->userRepository->uploadImage($fileData, $user);
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
     * @param CreateRequest $request
     * @return mixed
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function checkEmail(CreateRequest $request)
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
        $default = true; // @TODO contract related
//        $building = $resident->building; // always null
        $building = null;
        if ( ! $building || Building::ContactEnablesBasedSettings == $building->contact_enable) {
            $settings = Settings::first('contact_enable');
            return $settings->contact_enable ?? $default;
        }


        return Building::ContactEnablesShow == $building->contact_enable;
    }
}
