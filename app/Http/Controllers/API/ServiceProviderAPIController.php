<?php

namespace App\Http\Controllers\API;

use App\Criteria\Common\RequestCriteria;
use App\Criteria\ServiceProvider\FilterByCategoryCriteria;
use App\Criteria\Common\FilterByManyBuildingCriteria;
use App\Criteria\Common\FilterByLanguageCriteria;
use App\Criteria\Common\FilterByPinboardCriteria;
use App\Criteria\Common\HasRequestCriteria;
use App\Criteria\Common\FilterByStateCriteria;
use App\Http\Controllers\AppBaseController;
use App\Http\Requests\API\ServiceProvider\AssignRequest;
use App\Http\Requests\API\ServiceProvider\CreateRequest;
use App\Http\Requests\API\ServiceProvider\DeleteRequest;
use App\Http\Requests\API\ServiceProvider\ListRequest;
use App\Http\Requests\API\ServiceProvider\ViewRequest;
use App\Http\Requests\API\ServiceProvider\UnAssignRequest;
use App\Http\Requests\API\ServiceProvider\UpdateRequest;
use App\Models\ServiceProvider;
use App\Models\User;
use App\Repositories\AddressRepository;
use App\Repositories\QuarterRepository;
use App\Repositories\BuildingRepository;
use App\Repositories\ServiceProviderRepository;
use App\Repositories\UserRepository;
use App\Transformers\ServiceProviderTransformer;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;

/**
 * Class ServiceProviderController
 * @package App\Http\Controllers\API
 */
class ServiceProviderAPIController extends AppBaseController
{
    /** @var  ServiceProviderRepository */
    private $serviceProviderRepository;

    /**
     * @var AddressRepository
     */
    private $addressRepository;

    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * ServiceProviderAPIController constructor.
     * @param ServiceProviderRepository $serviceProviderRepo
     * @param AddressRepository $addressRepo
     * @param UserRepository $userRepo
     */
    public function __construct(ServiceProviderRepository $serviceProviderRepo, AddressRepository $addressRepo, UserRepository $userRepo)
    {
        $this->serviceProviderRepository = $serviceProviderRepo;
        $this->addressRepository = $addressRepo;
        $this->userRepository = $userRepo;
    }

    /**
     * @SWG\Get(
     *      path="/service",
     *      summary="Get a listing of the ServiceProviders.",
     *      tags={"ServiceProvider"},
     *      description="Get all ServiceProviders",
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
     *                  @SWG\Items(ref="#/definitions/ServiceProvider")
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
     * @return Response
     * @throws /Exception
     */
    public function index(ListRequest $request)
    {
        $this->serviceProviderRepository->pushCriteria(new RequestCriteria($request));
        $this->serviceProviderRepository->pushCriteria(new LimitOffsetCriteria($request));
        $this->serviceProviderRepository->pushCriteria(new FilterByPinboardCriteria($request));
        $this->serviceProviderRepository->pushCriteria(new FilterByLanguageCriteria($request));
        $this->serviceProviderRepository->pushCriteria(new FilterByCategoryCriteria($request));
        $this->serviceProviderRepository->pushCriteria(new FilterByStateCriteria($request));
        $this->serviceProviderRepository->pushCriteria(new FilterByManyBuildingCriteria($request));

        $getAll = $request->get('get_all', false);

        $hasRequest = $request->get('has_req', false);
        if ($hasRequest) {
            $this->serviceProviderRepository->pushCriteria(new HasRequestCriteria());
        }

        if ($getAll) {
            $this->serviceProviderRepository->withCount('requests')->scope('allRequestStatusCount');

            $serviceProviders = $this->serviceProviderRepository->with([
                'user',
            ])->get();
            
            return $this->sendResponse($serviceProviders->toArray(), 'Service Providers successfully');
        }

        $perPage = $request->get('per_page', env('APP_PAGINATE', 10));
        $this->serviceProviderRepository->with([
            'user', 'address:id,country_id,state_id,city,street,zip'
        ]);

        $this->serviceProviderRepository->withCount('requests')->scope('allRequestStatusCount');
        $serviceProviders = $this->serviceProviderRepository->paginate($perPage);

        if ($request->show_category_name) {
            foreach ($serviceProviders as $provider) {
                $provider->setAppends(['category_name_en', 'category_name_de', 'category_name_fr', 'category_name_it']);
            }
        }

        return $this->sendResponse($serviceProviders->toArray(), 'Service Providers retrieved successfully');
    }

    /**
     * @SWG\Get(
     *      path="/service/category",
     *      summary="Get a listing of the ServiceProviders by category.",
     *      tags={"ServiceProvider"},
     *      description="Get all ServiceProviders  by category",
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
     *                  @SWG\Items(ref="#/definitions/ServiceProvider")
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
    public function indexByCategory(ListRequest $request)
    {
        $this->serviceProviderRepository->pushCriteria(new RequestCriteria($request));
        $this->serviceProviderRepository->pushCriteria(new LimitOffsetCriteria($request));

        $serviceProviders = $this->serviceProviderRepository->get();

        $response = (new ServiceProviderTransformer)->transformByCategoryCollection($serviceProviders);

        return $this->sendResponse($response, 'Service Providers retrieved successfully');
    }

    /**
     * @SWG\Post(
     *      path="/service",
     *      summary="Store a newly created ServiceProvider in storage",
     *      tags={"ServiceProvider"},
     *      description="Store ServiceProvider",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="ServiceProvider that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/ServiceProvider")
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
     *                  ref="#/definitions/ServiceProvider"
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
     */
    public function store(CreateRequest $request)
    {
        $input = $request->all();

        if (!isset($input['user']['email'])) {
            $input['user']['email'] = $input['email'];
        }

        if (!isset($input['user']['name'])) {
            $input['user']['name'] = $input['name'];
        }

        if (!isset($input['user']['phone'])) {
            $input['user']['phone'] = $input['phone'];
        }
        $input['user']['role'] = 'provider';

        $validator = Validator::make($input['user'], User::$rules);
        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }

        if (isset($input['settings'])) {
            $input['user']['settings'] = Arr::pull($input, 'settings');
        }

        try {
            User::disableAuditing();
            $user = $this->userRepository->create($input['user']);
            User::enableAuditing();
        } catch (Exception $e) {
            return $this->sendError(__('models.service.errors.create') . $e->getMessage());
        }
        $input['user_id'] = $user->id;

        try {
            unset($input['address']['house_num']);
            $address = $this->addressRepository->create($input['address']);
        } catch (Exception $e) {
            return $this->sendError(__('models.service.errors.create') . $e->getMessage());
        }
        $input['address_id'] = $address->id;

        try {
            $serviceProvider = $this->serviceProviderRepository->create($input);
        } catch (Exception $e) {
            return $this->sendError(__('models.service.errors.create') . $e->getMessage());
        }

        $serviceProvider->load(['user', 'address:id,country_id,state_id,city,street,zip']);
        $response = (new ServiceProviderTransformer)->transform($serviceProvider);

        return $this->sendResponse($response, __('models.service.saved'));
    }

    /**
     * @SWG\Get(
     *      path="/service/{id}",
     *      summary="Display the specified ServiceProvider",
     *      tags={"ServiceProvider"},
     *      description="Get ServiceProvider",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ServiceProvider",
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
     *                  ref="#/definitions/ServiceProvider"
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
     * @param ViewRequest $request
     * @return mixed
     */
    public function show($id, ViewRequest $request)
    {
        /** @var ServiceProvider $serviceProvider */
        $serviceProvider = $this->serviceProviderRepository->findWithoutFail($id);
        if (empty($serviceProvider)) {
            return $this->sendError(__('models.service.errors.not_found'));
        }

        $serviceProvider->load(['user', 'address:id,country_id,state_id,city,street,zip', 'settings'])
            ->loadCount('requests',  'solvedRequests', 'pendingRequests', 'buildings');
        $response = (new ServiceProviderTransformer)->transform($serviceProvider);

        return $this->sendResponse($response, 'Service Provider retrieved successfully');
    }

    /**
     * @SWG\Put(
     *      path="/service/{id}",
     *      summary="Update the specified ServiceProvider in storage",
     *      tags={"ServiceProvider"},
     *      description="Update ServiceProvider",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ServiceProvider",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="ServiceProvider that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/ServiceProvider")
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
     *                  ref="#/definitions/ServiceProvider"
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
     * @param UpdateRequest $request
     * @return mixed
     */
    public function update($id, UpdateRequest $request)
    {
        $input = $request->all();
        /** @var ServiceProvider $serviceProvider */
        $serviceProvider = $this->serviceProviderRepository->findWithoutFail($id);
        if (empty($serviceProvider)) {
            return $this->sendError(__('models.service.errors.not_found'));
        }

        if (isset($input['user'])) {
            $input['user']['email'] = $input['email'];
            $input['user']['name'] = $input['name'];

            $validator = Validator::make($input['user'], User::$rulesUpdate);
            if ($validator->fails()) {
                return $this->sendError($validator->errors());
            }
        }

        $input['user']['settings'] = Arr::pull($input, 'settings', []);
        try {
            User::disableAuditing();
            $this->userRepository->update($input['user'], $serviceProvider->user_id);
            User::enableAuditing();
        } catch (Exception $e) {
            return $this->sendError(__('models.service.errors.update') . $e->getMessage());
        }

        if (isset($input['address'])) {
            try {
                unset($input['address']['house_num']);
                $this->addressRepository->update($input['address'], $serviceProvider->address_id);
            } catch (Exception $e) {
                return $this->sendError(__('models.service.errors.update') . $e->getMessage());
            }
        }

        try {
            $serviceProvider = $this->serviceProviderRepository->updateExisting($serviceProvider, $input);
        } catch (Exception $e) {
            return $this->sendError(__('models.service.errors.update') . $e->getMessage());
        }

        $serviceProvider->load(['user', 'address:id,country_id,state_id,city,street,zip', 'settings']);
        $response = (new ServiceProviderTransformer)->transform($serviceProvider);

        return $this->sendResponse($response, __('models.service.saved'));
    }

    /**
     * @SWG\Delete(
     *      path="/service/{id}",
     *      summary="Remove the specified ServiceProvider from storage",
     *      tags={"ServiceProvider"},
     *      description="Delete ServiceProvider",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ServiceProvider",
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
        /** @var ServiceProvider $serviceProvider */
        $serviceProvider = $this->serviceProviderRepository->findWithoutFail($id);
        if (empty($serviceProvider)) {
            return $this->sendError(__('models.service.errors.not_found'));
        }

        try {
            // @TODO fix DRY maybe using deleted model event
            if ($serviceProvider->user()->exists()) {
                $serviceProvider->user->delete();
            }
            if ($serviceProvider->address()->exists()) {
                $serviceProvider->address->delete();
            }
            $serviceProvider->delete();
        } catch (Exception $e) {
            return $this->sendError(__('models.service.errors.deleted') . $e->getMessage());
        }

        return $this->sendResponse($id, __('models.service.deleted'));
    }

    /**
     * @param DeleteRequest $request
     * @return mixed
     */
    public function destroyWithIds(DeleteRequest $request){
        $ids = $request->get('ids');
        try{
            foreach($ids as $id){
                $serviceProvider = $this->serviceProviderRepository->findWithoutFail($id); // @TODO bug source code
                if ($serviceProvider->user()->exists()) {
                    $serviceProvider->user->delete();
                }
                if ($serviceProvider->address()->exists()) {
                    $serviceProvider->address->delete();
                }
                $serviceProvider->delete();
            }            
        }
        catch (\Exception $e) {
            return $this->sendError(__('models.service.errors.deleted') . $e->getMessage());
        }
        return $this->sendResponse($ids, __('models.service.deleted'));
    }

    /**
     * @SWG\Post(
     *      path="/services/{id}/quarters/{did}",
     *      summary="Assign the provided quarter to the service provider",
     *      tags={"ServiceProvider"},
     *      description="Assign the provided quarter to the service provider",
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
     *                  ref="#/definitions/ServiceProvider"
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
     * @param int $did
     * @param QuarterRepository $qRepo
     * @param AssignRequest $r
     * @return mixed
     */
    public function assignQuarter(int $id, int $did, QuarterRepository $qRepo, AssignRequest $r)
    {
        $sp = $this->serviceProviderRepository->findWithoutFail($id);
        if (empty($sp)) {
            return $this->sendError(__('models.service.errors.not_found'));
        }
        $d = $qRepo->findWithoutFail($did);
        if (empty($d)) {
            return $this->sendError(__('models.service.errors.quarter_not_found'));
        }

        $sp->quarters()->sync($d, false);
        $sp->load('user', 'address:id,country_id,state_id,city,street,zip', 'quarters', 'buildings');
        $ret = (new ServiceProviderTransformer)->transform($sp);

        return $this->sendResponse($ret, __('general.attached.quarter'));
    }

    /**
     * @SWG\Delete(
     *      path="/services/{id}/quarters/{did}",
     *      summary="Unassign the provided quarter from the service provider",
     *      tags={"ServiceProvider"},
     *      description="Unassign the provided quarter from the service provider",
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
     *                  ref="#/definitions/ServiceProvider"
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
     * @param int $did
     * @param QuarterRepository $qRepo
     * @param UnAssignRequest $r
     * @return mixed
     */
    public function unassignQuarter(int $id, int $did, QuarterRepository $qRepo, UnAssignRequest $r)
    {
        $sp = $this->serviceProviderRepository->findWithoutFail($id);
        if (empty($sp)) {
            return $this->sendError(__('models.service.errors.not_found'));
        }
        $d = $qRepo->findWithoutFail($did);
        if (empty($d)) {
            return $this->sendError(__('models.service.errors.quarter_not_found'));
        }

        $sp->quarters()->detach($d);
        $sp->load('user', 'address:id,country_id,state_id,city,street,zip', 'quarters', 'buildings');
        $ret = (new ServiceProviderTransformer)->transform($sp);

        return $this->sendResponse($ret, __('general.detached.quarter'));
    }

    /**
     * @SWG\Post(
     *      path="/services/{id}/buildings/{bid}",
     *      summary="Assign the provided building to the service provider",
     *      tags={"ServiceProvider"},
     *      description="Assign the provided building to the service provider",
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
     *                  ref="#/definitions/ServiceProvider"
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
     * @param int $bid
     * @param BuildingRepository $bRepo
     * @param AssignRequest $r
     * @return mixed
     */
    public function assignBuilding(int $id, int $bid, BuildingRepository $bRepo, AssignRequest $r)
    {
        $sp = $this->serviceProviderRepository->findWithoutFail($id);
        if (empty($sp)) {
            return $this->sendError(__('models.service.errors.not_found'));
        }
        $b = $bRepo->findWithoutFail($bid);
        if (empty($b)) {
            return $this->sendError(__('models.service.errors.building_not_found'));
        }
        if ($b->quarter) {
            if ($sp->quarters->contains($b->quarter)) {
                return $this->sendError(__('models.service.errors.building_already_assign'));
            }
        }

        $sp->buildings()->sync($b, false);
        $sp->load('user', 'address:id,country_id,state_id,city,street,zip', 'quarters', 'buildings');
        $ret = (new ServiceProviderTransformer)->transform($sp);

        return $this->sendResponse($ret, __('general.attached.building'));
    }

    /**
     * @SWG\Delete(
     *      path="/services/{id}/buildings/{bid}",
     *      summary="Unassign the provided building from the service provider",
     *      tags={"ServiceProvider"},
     *      description="Unassign the provided building from the service provider",
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
     *                  ref="#/definitions/ServiceProvider"
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
     * @param int $bid
     * @param BuildingRepository $bRepo
     * @param UnAssignRequest $r
     * @return mixed
     */
    public function unassignBuilding(int $id, int $bid, BuildingRepository $bRepo, UnAssignRequest $r)
    {
        $sp = $this->serviceProviderRepository->findWithoutFail($id);
        if (empty($sp)) {
            return $this->sendError(__('models.service.errors.not_found'));
        }
        $b = $bRepo->findWithoutFail($bid);
        if (empty($b)) {
            return $this->sendError(__('models.service.errors.building_not_found'));
        }

        $sp->buildings()->detach($b);
        $sp->load('user', 'address:id,country_id,state_id,city,street,zip', 'quarters', 'buildings');
        $ret = (new ServiceProviderTransformer)->transform($sp);

        return $this->sendResponse($ret, __('general.detached.building'));
    }

    /**
     * @SWG\Get(
     *      path="/services/{id}/locations",
     *      summary="Get a listing of the service provider locations.",
     *      tags={"ServiceProvider"},
     *      description="Get a listing of the service provider locations.",
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
     *                  @SWG\Items(ref="#/definitions/ServiceProvider")
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
     * @param Request $request
     * @return mixed
     */
    public function getLocations(int $id, ViewRequest $request)
    {
        $sp = $this->serviceProviderRepository->findWithoutFail($id);
        if (empty($sp)) {
            return $this->sendError(__('models.service.errors.not_found'));
        }

        $perPage = $request->get('per_page', env('APP_PAGINATE', 10));
        $locations = $this->serviceProviderRepository->locations($sp)->paginate($perPage);
        return $this->sendResponse($locations, 'Locations retrieved successfully');
    }
}
