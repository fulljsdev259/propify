<?php

namespace App\Http\Controllers\API;

use App\Criteria\Building\FilterByRelatedFieldsCriteria;
use App\Criteria\Common\HasRequestCriteria;
use App\Criteria\Common\RequestCriteria;
use App\Http\Controllers\AppBaseController;
use App\Http\Requests\API\Building\BatchAssignManagers;
use App\Http\Requests\API\Building\BatchAssignUsers;
use App\Http\Requests\API\Building\CreateRequest;
use App\Http\Requests\API\Building\UnAssignRequest;
use App\Http\Requests\API\Building\DeleteRequest;
use App\Http\Requests\API\Building\ListRequest;
use App\Http\Requests\API\Building\UpdateRequest;
use App\Http\Requests\API\Building\ViewRequest;
use App\Models\Address;
use App\Models\AuditableModel;
use App\Models\Building;
use App\Models\BuildingAssignee;
use App\Models\PropertyManager;
use App\Models\User;
use App\Repositories\AddressRepository;
use App\Repositories\BuildingRepository;
use App\Repositories\PropertyManagerRepository;
use App\Repositories\UserRepository;
use App\Repositories\UnitRepository;
use App\Repositories\RequestRepository;
use App\Transformers\BuildingAssigneeTransformer;
use App\Transformers\BuildingTransformer;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Spatie\Geocoder\Geocoder;
use Illuminate\Support\Facades\Validator;

/**
 * Class BuildingController
 * @package App\Http\Controllers\API
 */
class BuildingAPIController extends AppBaseController
{
    /** @var  BuildingRepository */
    private $buildingRepository;

    /** @var  AddressRepository */
    private $addressRepository;

    /** @var  UserRepository */
    private $userRepository;

    /** @var  propertyManagerRepository */
    private $propertyManagerRepository;

    /** @var  UnitRepository */
    private $unitRepository;

    /** @var  RequestRepository */
    private $requestRepository;

    /**
     * BuildingAPIController constructor.
     * @param BuildingRepository $buildingRepo
     * @param AddressRepository $addrRepo
     * @param UserRepository $ur
     * @param PropertyManagerRepository $pm
     * @param UnitRepository $un
     * @param RequestRepository $sr
     */
    public function __construct(
        BuildingRepository $buildingRepo,
        AddressRepository $addrRepo,
        UserRepository $ur,
        PropertyManagerRepository $pm,
        UnitRepository $un,
        RequestRepository $sr
    )
    {
        $this->buildingRepository = $buildingRepo;
        $this->addressRepository = $addrRepo;
        $this->userRepository = $ur;
        $this->propertyManagerRepository = $pm;
        $this->unitRepository = $un;
        $this->requestRepository = $sr;
    }

    /**
     * @SWG\Get(
     *      path="/buildings",
     *      summary="Get a listing of the Buildings.",
     *      tags={"Building"},
     *      description="Get all Buildings",
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
     *                  @SWG\Items(ref="#/definitions/Building")
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
     * @return Response
     * @throws /Exception
     */
    public function index(ListRequest $request)
    {
        $request->merge(['model' => 'buildings']);
        $this->buildingRepository->pushCriteria(new RequestCriteria($request));
        $this->buildingRepository->pushCriteria(new FilterByRelatedFieldsCriteria($request));
        $this->buildingRepository->pushCriteria(new LimitOffsetCriteria($request));

        $hasRequest = $request->get('has_req', false);
        if ($hasRequest) {
            $this->buildingRepository->pushCriteria(new HasRequestCriteria());
        }

        $getAll = $request->get('get_all', false);
        if ($getAll) {
            $buildings = $this->buildingRepository->with('address.state')->get();
            return $this->sendResponse($buildings->toArray(), 'Buildings retrieved successfully');
        }

        $perPage = $request->get('per_page', env('APP_PAGINATE', 10));
        $buildings = $this->buildingRepository->with([
                'address.state',
                'serviceProviders',
                'contracts' => function ($q) {
                    $q->with('building.address', 'unit', 'resident.user');
                },
                'propertyManagers',
                'lastPropertyManagers.user',
                'users'
            ])->withCount([
                'units',
                'propertyManagers',
                'users'
            ])
            ->scope('allRequestStatusCount')
            ->paginate($perPage);

        $response = (new BuildingTransformer)->transformPaginator($buildings);
        return $this->sendResponse($response, 'Buildings retrieved successfully');
    }

    /**
     * @SWG\Get(
     *      path="/buildings/latest",
     *      summary="Get latest buildings 5 Residents",
     *      tags={"Building"},
     *      description="Get a latest 5(limit) Buildings",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="limit",
     *          in="query",
     *          description="How many buildings get",
     *          type="integer",
     *          default=5
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean",
     *                  example="true"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  type="array",
     *                  @SWG\Items(
     *                      @SWG\Property(
     *                          property="id",
     *                          type="integer",
     *                      ),
     *                      @SWG\Property(
     *                          property="name",
     *                          type="string"
     *                      ),
     *                      @SWG\Property(
     *                          property="units_count",
     *                          type="integer"
     *                      ),
     *                      @SWG\Property(
     *                          property="residents_count",
     *                          type="integer"
     *                      )
     *                  )
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
     * @return Response
     * @throws \Exception
     */
    public function latest(ListRequest $request)
    {
        $limit = $request->get('limit', 5);
        $model = $this->buildingRepository->getModel();
        $buildings = $model->select(['id', 'name'])->orderByDesc('id')->limit($limit)->withCount([
            'units',
            'contracts' => function ($q) {
                $q->with('building.address', 'unit', 'resident.user');
            },
        ])->get();
        return $this->sendResponse($buildings->toArray(), 'Buildings retrieved successfully');
    }

    /**
     * @param ListRequest $r
     * @return mixed
     */
    public function map(ListRequest $r)
    {
        $model = $this->buildingRepository->getModel();
        $columns = [
            'id',
            'address_id',
            'name',
            'latitude',
            'longitude'
        ];

        $buildings = $model->select($columns)->with([
                'address' => function ($q) {
                    $q->select('id', 'country_id', 'state_id', 'city', 'street', 'house_num', 'zip')
                        ->with(['state', 'country']);
                },
            ])->withCount([
                'units',
                'propertyManagers',
                'contracts' => function ($q) {
                    $q->with('building.address', 'unit', 'resident.user');
                },
                'users'
            ])->allRequestStatusCount()
            ->get();
        return $this->sendResponse($buildings->toArray(), 'Buildings retrieved successfully');
    }

    /**
     * @SWG\Post(
     *      path="/buildings",
     *      summary="Store a newly created Building in storage",
     *      tags={"Building"},
     *      description="Store Building",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="Building that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/Building")
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
     *                  ref="#/definitions/Building"
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
     * @return Response
     * @throws /Exception
     */
    public function store(CreateRequest $request)
    {
        $input = $request->only((new Building)->getFillable());
        $input['service_providers'] = $request->get('service_providers');

        $addressInput = $request->get('address');
        $validator = Validator::make($addressInput, Address::$rules);
        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }

        $address = $this->addressRepository->create($addressInput);
        $input['address_id'] = $address->id;

        $geoData = $this->getGeoDataByAddress($address);
        $input = array_merge($input, $geoData);
        $building = $this->buildingRepository->create($input);
        $floorData = $request->get('floor', []);
        $building = $this->buildingRepository->saveManyUnit($building, $floorData, $address->house_num);

        $building->load([
            'contracts' => function ($q) {
                $q->with('building.address', 'unit', 'resident.user');
            },
        ]);
        if ($building && isset($address)) {
            $building->addDataInAudit('address', $address);
        }
        $response = (new BuildingTransformer)->transform($building);

        return $this->sendResponse($response, __('models.building.saved'));
    }

    /**
     * @SWG\Get(
     *      path="/buildings/{id}",
     *      summary="Display the specified Building",
     *      tags={"Building"},
     *      description="Get Building",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Building",
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
     *                  ref="#/definitions/Building"
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
     * @param ViewRequest $r
     * @return mixed
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function show($id, ViewRequest $r)
    {
        /** @var Building $building */
        $building = $this->buildingRepository->findWithoutFail($id);
        if (empty($building)) {
            return $this->sendError(__('models.building.errors.not_found'));
        }

        $building
            ->load([
                'address.state',
                'serviceProviders',
                'contracts' => function ($q) {
                    $q->with('building.address', 'unit', 'resident.user');
                },
                'propertyManagers',
                'lastPropertyManagers.user',
                'media',
                'quarter',
                'users'
            ]);
        $response = (new BuildingTransformer)->transform($building);
        $response['media_category'] = \ConstFileCategories::MediaCategories; // @TODO delete

        return $this->sendResponse($response, 'Building retrieved successfully');
    }

    /**
     * @SWG\Put(
     *      path="/buildings/{id}",
     *      summary="Update the specified Building in storage",
     *      tags={"Building"},
     *      description="Update Building",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Building",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="Building that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/Building")
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
     *                  ref="#/definitions/Building"
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
     * @return Response
     * @throws /Exception
     */
    public function update(int $id, UpdateRequest $request)
    {
        /** @var Building $building */
        $building = $this->buildingRepository->findWithoutFail($id);
        if (empty($building)) {
            return $this->sendError(__('models.building.errors.not_found'));
        }

        $input = $request->only((new Building)->getFillable());
        $input['service_providers'] = $request->get('service_providers');

        $addressInput = $request->get('address');
        if ($addressInput) {
            $validator = Validator::make($addressInput, Address::$rules);
            if ($validator->fails()) {
                return $this->sendError($validator->errors());
            }
            $address = $this->addressRepository->update($addressInput, $building->address_id);

            $locationRelated = ['street', 'house_num', 'zip', 'city'];
            $changes = array_keys($address->getChanges());
            if (array_intersect($locationRelated, $changes)) {

                $geoData = $this->getGeoDataByAddress($address);
                $input = array_merge($input, $geoData);
            }
            $input['address_id'] = $address->id;
        }

        if (isset($input['latitude']) && $input['latitude'] == $building->latitude) {
            unset($input['latitude']);
        }

        if (isset($input['longitude']) && $input['longitude'] == $building->latitude) {
            unset($input['longitude']);
        }

        $building = $this->buildingRepository->update($input, $id);
        $floorData = $request->get('floor', []);
        $building = $this->buildingRepository->saveManyUnit($building, $floorData, $address->house_num);
        if ($building && isset($address)) {
            $building->addDataInAudit('address', $address, AuditableModel::UpdateOrCreate);
        }

        $building->load([
            'address.state',
            'media',
            'serviceProviders',
            'contracts' => function ($q) {
                $q->with('building.address', 'unit', 'resident.user');
            },
        ]);
        $response = (new BuildingTransformer)->transform($building);
        return $this->sendResponse($response, __('models.building.saved'));
    }

    /**
     * @SWG\Delete(
     *      path="/buildings/{id}",
     *      summary="Remove the specified Building from storage",
     *      tags={"Building"},
     *      description="Delete Building",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Building",
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
     * @param int $id
     * @param DeleteRequest $r
     * @return Response
     */
    public function destroy($id, DeleteRequest $r)
    {
        /** @var Building $building */
        $building = $this->buildingRepository->findWithoutFail($id);
        if (empty($building)) {
            return $this->sendError(__('models.building.errors.not_found'));
        }

        try {
            $this->buildingRepository->delete($building->id);
        } catch (\Exception $e) {
            return $this->sendError(__('models.building.errors.deleted') . $e->getMessage());
        }

        return $this->sendResponse($id, __('models.building.deleted'));
    }

    /**
     * @param DeleteRequest $request
     * @return mixed
     */
    public function destroyWithIds(DeleteRequest $request)
    {
        /** @var Building $building */        
        $buildings = $this->buildingRepository->findWithoutFail($request->get('ids'));
        if (empty($buildings)) {
            return $this->sendError(__('models.building.errors.not_found'));
        }        
        
        try {
            foreach($buildings as $building) {
                $this->buildingRepository->delete($building->id);
            }
            $units = $this->unitRepository->getUnitsIdwithBuildingIds($buildings->pluck('id'));

            if($request->get('is_requests')) {
                $this->requestRepository->deleteRequesetWithUnitIds($units->pluck('id'));
            }            

            if($request->get('is_units')) {                
                $this->unitRepository->deleteUnitWithBuilding($buildings->pluck('id'));                
            }            

            return $this->sendResponse('', __('models.building.deleted'));

        } catch (\Exception $e) {
            return $this->sendError(__('models.building.errors.deleted') . $e->getMessage());
        }
    }

    /**
     * @param Request $request
     * @return mixed
     * // @TODO ROLE RELATED
     */
    public function checkUnitRequest(Request $request)
    {
        $buildings = $this->buildingRepository->findWithoutFail($request->get('ids'));
        if (empty($buildings)) {
            return $this->sendError(__('models.building.errors.not_found'));
        }

        try {
            // @TODO check I think this code is not properly made
            $units = $this->unitRepository->getUnitsIdwithBuildingIds($buildings->pluck('id'));
            $returnValue = [
                'isUnitExist' => false,
                'isRequestExist' => false,
            ];

            if(count($units) > 0) {
                $request['isUnitExist'] = true;
            }

            if($this->requestRepository->getRequestCountWithUnitIds($units->pluck('id')) > 0){
                $request['isRequestExist'] = true;
            }

            $returnValue = -1;
            if($request['isUnitExist'] && $request['isRequestExist'])
                $returnValue = 2;
            else if($request['isUnitExist'] && !$request['isRequestExist'])
                $returnValue = 0;
            else if(!$request['isUnitExist'] && $request['isRequestExist'])
                $returnValue = 1;

            return $this->sendResponse($returnValue, __('models.building.deleted'));

        } catch (\Exception $e) {
            return $this->sendError(__('models.building.errors.deleted') . $e->getMessage());
        }
    }

    /**
     * @SWG\Delete(
     *      path="/buildings/{pinboard_id}/service/{service_id}",
     *      summary="Remove the specified Service from storage",
     *      tags={"Building"},
     *      description="Delete Service",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Service",
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
     * @param int $id
     * @param int $service_id
     * @param UnAssignRequest $r
     * @return Response
     */
    public function serviceRemove(int $id, int $service_id, UnAssignRequest $r)
    {
        /** @var Building $building */
        $building = $this->buildingRepository->findWithoutFail($id);
        if (empty($building)) {
            return $this->sendError(__('models.building.errors.not_found'));
        }

        try {
            $building->serviceProviders()->detach($service_id);
        } catch (\Exception $e) {
            return $this->sendError(__('models.building.errors.provider_deleted') . $e->getMessage());
        }

        return $this->sendResponse($id, __('models.building.service.deleted'));
    }

    /**
     * @SWG\Get(
     *      path="/buildings/{id}/assignees",
     *      summary="Get a listing of the Building assignees.",
     *      tags={"Building"},
     *      description="Get a listing of the Building assignees.",
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
     *                  @SWG\Items(ref="#/definitions/BuildingAssignee")
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
     * @param ViewRequest $request
     * @return Response
     */
    public function getAssignees(int $id, ViewRequest $request)
    {
        // @TODO permissions
        $building = $this->buildingRepository->findWithoutFail($id);
        if (empty($building)) {
            return $this->sendError(__('models.building.errors.not_found'));
        }

        $perPage = $request->get('per_page', env('APP_PAGINATE', 10));
        $assignees = $building->assignees()->paginate($perPage);
        $assignees = $this->getAssigneesRelated($assignees, [PropertyManager::class, User::class]);

        $response = (new BuildingAssigneeTransformer())->transformPaginator($assignees) ;
        return $this->sendResponse($response, 'Assignees retrieved successfully');
    }

    /**
     * @SWG\Post(
     *      path="/buildings/{id}/propertyManagers",
     *      summary="Assign the provided propertyManagers to the Building",
     *      tags={"Building"},
     *      description=" <a href='http://dev.propify.ch/api/docs#/Building/pinboard_buildings__id__managers'>http://dev.propify.ch/api/docs#/Building/pinboard_buildings__id__managers</a>",
     *      produces={"application/json"},
     *      deprecated=true,
     *      @SWG\Parameter(
     *          name="managerIds",
     *          description="ids of managers",
     *          type="array",
     *          required=true,
     *          in="query",
     *          @SWG\Items(
     *              type="integer"
     *          )
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
     *                  ref="#/definitions/Building"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     *
     * @SWG\Post(
     *      path="/buildings/{id}/managers",
     *      summary="Assign the provided managers to the Building",
     *      tags={"Building"},
     *      description="Assign the provided managers to the Building",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="managerIds",
     *          description="ids of managers",
     *          type="array",
     *          required=true,
     *          in="query",
     *          @SWG\Items(
     *              type="integer"
     *          )
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
     *                  ref="#/definitions/Building"
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
     * @param BatchAssignManagers $request
     * @return Response
     */
    public function assignManagers(int $id, BatchAssignManagers $request)
    {
        /** @var Building $building */
        $building = $this->buildingRepository->findWithoutFail($id);
        if (empty($building)) {
            return $this->sendError(__('models.building.errors.not_found'));
        }

        $managerIds = $request->get('managersIds') ?? $request->get('managerIds');
        try {
            $currentManagers = $building->propertyManagers()
                ->whereIn('property_managers.id', $managerIds)
                ->pluck('property_managers.id')
                ->toArray();

            $newManagers = array_diff($managerIds, $currentManagers);
            $attachData  = [];
            foreach ($newManagers as $manager) {
                $attachData[$manager] = ['created_at' => now()];
            }
            $building->propertyManagers()->syncWithoutDetaching($attachData);
        } catch (\Exception $e) {
            return $this->sendError( __('models.building.errors.manager_assigned') . $e->getMessage());
        }

        $building->load([
            'address.state',
            'media',
            'serviceProviders',
            'propertyManagers',
            'lastPropertyManagers.user',
            'users'
        ]);
        $response = (new BuildingTransformer)->transform($building);
        return $this->sendResponse($response, __('models.building.managers_assigned'));
    }

    /**
     * @SWG\Post(
     *      path="/buildings/{id}/users",
     *      summary="Assign the provided users to the Building",
     *      tags={"Building"},
     *      description="Assign the provided users(administrator, super-administrator) to the Building",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="userIds",
     *          description="ids of users",
     *          type="array",
     *          required=true,
     *          in="query",
     *          @SWG\Items(
     *              type="integer"
     *          )
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
     *                  ref="#/definitions/Building"
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
     * @param BatchAssignUsers $request
     * @return Response
     */
    public function assignUsers(int $id, BatchAssignUsers $request)
    {
        /** @var Building $building */
        $building = $this->buildingRepository->findWithoutFail($id);
        if (empty($building)) {
            return $this->sendError(__('models.building.errors.not_found'));
        }

        $userIds = $request->get('userIds');
        try {
            $currentUsers = $building->users()
                ->whereIn('users.id', $userIds)
                ->pluck('users.id')
                ->toArray();

            $newUsers = array_diff($userIds, $currentUsers);
            $attachData  = [];
            foreach ($newUsers as $userId) {
                $attachData[$userId] = ['created_at' => now()];
            }
            $building->users()->syncWithoutDetaching($attachData);
        } catch (\Exception $e) {
            return $this->sendError( __('models.building.errors.user_assigned') . $e->getMessage());
        }

        $building->load([
            'address.state',
            'media',
            'serviceProviders',
            'propertyManagers',
            'lastPropertyManagers.user',
            'users'
        ]);
        $response = (new BuildingTransformer)->transform($building);
        return $this->sendResponse($response, __('models.building.user_assigned'));
    }

    /**
     * @SWG\Delete(
     *      path="/buildings-assignees/{buildings_assignee_id}",
     *      summary="Unassign the user or manager to the building",
     *      tags={"Building", "User", "PropertyManager"},
     *      description="Unassign the user or manager to the request",
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
     *                  type="integer",
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
     * @param UnAssignRequest $request
     * @return mixed
     * @throws \Exception
     */
    public function deleteBuildingAssignee(int $id, UnAssignRequest $request)
    {
        $buildingAssignee = BuildingAssignee::find($id);
        if (empty($buildingAssignee)) {
            // @TODO fix message
            return $this->sendError(__('models.building.errors.not_found'));
        }
        $buildingAssignee->delete();

        return $this->sendResponse($id, __('general.detached.' . $buildingAssignee->assignee_type));
    }

    /**
     * @SWG\Delete(
     *      path="/buildings/{building_id}/propertyManagers/{manager_id}",
     *      summary="Unassign the provided property managerfrom the building ",
     *      tags={"Building"},
     *      deprecated=true,
     *      description="<a href='http://dev.propify.ch/api/docs#/Building/delete_buildings_assignees__buildings_assignee_id_'>http://dev.propify.ch/api/docs#/Building/delete_buildings_assignees__buildings_assignee_id_</a>",
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
     *                  ref="#/definitions/Building"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     *
     * @param int $building_id
     * @param int $manager_id
     * @param UnAssignRequest $r
     * @return mixed
     * @throws \Exception
     */
    public function unAssignPropertyManager(int $building_id, int $manager_id, UnAssignRequest $r)
    {
        $assigneeId = BuildingAssignee::where([
                'building_id' => $building_id,
                'assignee_id' => $manager_id,
                'assignee_type' => get_morph_type_of(PropertyManager::class)
            ])->value('id');
        return $this->deleteBuildingAssignee($assigneeId, $r);
    }

    /**
     * @param $address
     * @return array
     */
    protected function getGeoDataByAddress($address)
    {
        $_address = sprintf('%s %s, %s %s', $address->street, $address->house_num, $address->zip, $address->city);
        $client = new \GuzzleHttp\Client();
        $geocoder = new Geocoder($client);
        $geocoder->setApiKey(config('geocoder.key'));

        try {
            $response = $geocoder->getCoordinatesForAddress($_address);
        } catch (\Exception $exception) {
            $response = [
                'lat' => 0,
                'lng' => 0,
            ];
        }
        return [
            'longitude' => $response['lng'],
            'latitude' => $response['lat']
        ];
    }
}
