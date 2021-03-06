<?php

namespace App\Http\Controllers\API;

use App\Criteria\Building\FilterByRelatedFieldsCriteria;
use App\Criteria\Building\FilterByUserRoleCriteria;
use App\Criteria\Building\IncludeForOrderCriteria;
use App\Criteria\Common\HasRequestCriteria;
use App\Criteria\Common\RequestCriteria;
use App\Criteria\Building\FilterByCityCriteria;
use App\Criteria\Building\FilterByTypeCriteria;
use App\Http\Controllers\AppBaseController;
use App\Http\Requests\API\Building\CreateRequest;
use App\Http\Requests\API\Building\MassAssignBuildingsUsersRequest;
use App\Http\Requests\API\Building\MassAssignUsersRequest;
use App\Http\Requests\API\Building\UnAssignRequest;
use App\Http\Requests\API\Building\DeleteRequest;
use App\Http\Requests\API\Building\ListRequest;
use App\Http\Requests\API\Building\UpdateRequest;
use App\Http\Requests\API\Building\ViewRequest;
use App\Models\Address;
use App\Models\AuditableModel;
use App\Models\Building;
use App\Models\BuildingAssignee;
use App\Models\Unit;
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
        if ($request->orderBy == 'internal_quarter_id') {
            $request->merge(['orderBy' => 'quarters|internal_quarter_id']);
        }
        if ($request->orderBy == 'house_num') {
            $request->merge(['orderBy' => 'loc_addresses:address_id|house_num']);
        }

        $this->buildingRepository->pushCriteria(new RequestCriteria($request));
        $this->buildingRepository->pushCriteria(new LimitOffsetCriteria($request));
        $this->buildingRepository->pushCriteria(new FilterByRelatedFieldsCriteria($request));
        $this->buildingRepository->pushCriteria(new FilterByCityCriteria($request));
        $this->buildingRepository->pushCriteria(new FilterByTypeCriteria($request));
        $this->buildingRepository->pushCriteria(new FilterByUserRoleCriteria($request));
        $this->buildingRepository->pushCriteria(new IncludeForOrderCriteria($request));

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
//                'service_providers',
//                'propertyManagers',
//                'users',
                'quarter' => function ($q) {
                    $q->select('id', 'internal_quarter_id')->with([
                        'users' => function ($q) {
                            $q->whereHas('property_manager')->with('roles');
                        }
                    ]);
                },
                'units' => function ($q) {
                    $q->select('id', 'building_id')->with('relations:start_date,status,unit_id')
                        ->allRequestStatusCount();
                }
            ])->withCount([
                'units',
//                'propertyManagers',
//                'users',
                'units as count_of_apartments_units' => function ($q) {
                    $q->where('type', Unit::TypeApartment);
                }
            ])
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
        $buildings = $model->select(['id'])->orderByDesc('id')->limit($limit)->withCount([
            'units',
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
            'latitude',
            'longitude'
        ];

        $buildings = $model->select($columns)->with([
                'address' => function ($q) {
                    $q->select('id', 'country_id', 'state_id', 'city', 'street', 'house_num', 'zip')
                        ->with(['state', 'country']);
                },
                'units' =>  function ($q) {
                    $q->with('relations:unit_id,resident_id');
                }
            ])->withCount([
                'units',
                'propertyManagers',
                'users'
            ])->allRequestStatusCount()
            ->get();

        foreach ($buildings as $building) {
            $relations = $building->units->pluck('relations')->collapse();
            unset($building->relations);
            $building->residents_count = $relations->pluck('resident_id')->unique()->count();
        }

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

        if ($building && isset($address)) {
            $building->addDataInAudit(AuditableModel::MergeInMainData, $address);
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
//                'service_providers',
//                'propertyManagers',
                'media',
                'quarter',
//                'users'
            ]);

        $response = (new BuildingTransformer)->transform($building);
        $response['media_category'] = \ConstantsHelpers::MediaFileCategories; // @TODO delete

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
        if ($building && isset($address)) {
            $building->addDataInAudit(AuditableModel::MergeInMainData, $address, AuditableModel::UpdateOrCreate);
        }

        $building->load([
            'address.state',
            'media',
//            'service_providers',
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

        return $this->getAssigneesResponse($building, $request);
    }

    /**
     * @param int $id
     * @param MassAssignUsersRequest $massAssignUsersRequest
     * @return mixed
     * @throws \Exception
     */
    public function massAssignUsers(int $id, MassAssignUsersRequest $massAssignUsersRequest)
    {
        /** @var Building $building */
        $building = $this->buildingRepository->findWithoutFail($id);
        if (empty($building)) {
            return $this->sendError(__('models.building.errors.not_found'));
        }

        $data  = $massAssignUsersRequest->data;
        $assigneeData = collect();
        foreach ($data as $single) {
            $newAssignee = $this->assignSingleUserToBuilding($id, $single['user_id']);
            $assigneeData->push($newAssignee);
        }

        $building->newMassAssignmentAudit($assigneeData);
        return $this->getAssigneesResponse($building, $massAssignUsersRequest);
    }

    /**
     * @param MassAssignBuildingsUsersRequest $massAssignUsersRequest
     * @return mixed
     * @throws \OwenIt\Auditing\Exceptions\AuditingException
     */
    public function massAssignUsersToBuildings(MassAssignBuildingsUsersRequest $massAssignUsersRequest)
    {
        $buildings = $this->buildingRepository->findWhereIn('id', $massAssignUsersRequest->building_ids, ['id']);
        if ($buildings->isEmpty()) {
            return $this->sendError(__('models.building.errors.not_found'));
        }

        $userIds = $massAssignUsersRequest->user_ids;
        $assigneeData = collect();
        /** @var Building $building */
        foreach ($buildings as $building) {
            foreach ($userIds as $userId) {
                $newAssignee = $this->assignSingleUserToBuilding($building->id, $userId);
                $assigneeData->push($newAssignee);
            }
        }

        return $this->sendResponse($assigneeData->toArray(), $massAssignUsersRequest);
    }

    /**
     * @param $building
     * @param $request
     * @return mixed
     */
    protected function getAssigneesResponse($building, $request)
    {
        $perPage = $request->get('per_page', env('APP_PAGINATE', 10));
        $assignees = $building->assignees()->latest()->orderByDesc('id')->paginate($perPage);
        $assignees->load([
            'user' => function ($q) {
                $q->select('id', 'name', 'email', 'avatar')
                    ->with([
                        'service_provider:id,user_id,company_name,category',
                        'property_manager:id,user_id,type',
                        'roles:id,name'
                    ]);
            }
        ]);
        $response = (new BuildingAssigneeTransformer())->transformPaginator($assignees) ;
        return $this->sendResponse($response, 'Assignees retrieved successfully');
    }

    /**
     * @param $buildingId
     * @param $userId
     * @param $role
     * @return BuildingAssignee|\Illuminate\Database\Eloquent\Model|mixed
     */
    protected function assignSingleUserToBuilding($buildingId, $userId)
    {
        $user = User::find($userId);
        if (empty($user)) {
            return $this->sendError(__('models.user.errors.not_found'));
        }

        if ($user->resident) {
            return $this->sendError(__('general.invalid_operation'));
        }

        return BuildingAssignee::updateOrCreate([
            'building_id' => $buildingId,
            'user_id' => $userId,
        ], [
            'created_at' => now()
        ]);
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
    public function deleteBuildingAssignee($id, UnAssignRequest $request)
    {
        $buildingAssignee = BuildingAssignee::find($id);
        if (empty($buildingAssignee)) {
            // @TODO fix message
            return $this->sendError(__('models.building.errors.not_found'));
        }
        $buildingAssignee->delete();

        return $this->sendResponse($id, __('general.detached.user'));
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
