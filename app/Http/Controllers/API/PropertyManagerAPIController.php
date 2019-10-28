<?php

namespace App\Http\Controllers\API;

use App\Criteria\Common\RequestCriteria;
use App\Criteria\PropertyManager\FilterByRelatedFieldsCriteria;
use App\Criteria\PropertyManager\FilterByTypeCriteria;
use App\Criteria\PropertyManager\HasRequestCriteria;
use App\Http\Controllers\AppBaseController;
use App\Http\Requests\API\PropertyManager\AssignRequest;
use App\Http\Requests\API\PropertyManager\BatchDeleteRequest;
use App\Http\Requests\API\PropertyManager\CreateRequest;
use App\Http\Requests\API\PropertyManager\DeleteRequest;
use App\Http\Requests\API\PropertyManager\ListRequest;
use App\Http\Requests\API\PropertyManager\UnAssignRequest;
use App\Http\Requests\API\PropertyManager\UpdateRequest;
use App\Http\Requests\API\PropertyManager\ViewRequest;
use App\Models\AuditableModel;
use App\Models\PropertyManager;
use App\Models\Role;
use App\Models\User;
use App\Repositories\BuildingRepository;
use App\Repositories\QuarterRepository;
use App\Repositories\PropertyManagerRepository;
use App\Repositories\UserRepository;
use App\Transformers\PropertyManagerTransformer;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Illuminate\Support\Facades\Validator;

/**
 * Class PropertyManagerController
 * @package App\Http\Controllers\API
 */
class PropertyManagerAPIController extends AppBaseController
{
    /** @var  PropertyManagerRepository */
    private $propertyManagerRepository;

    /** @var  UserRepository */
    private $userRepository;

    /**
     * PropertyManagerAPIController constructor.
     * @param PropertyManagerRepository $propertyManagerRepo
     * @param UserRepository $userRepo
     */
    public function __construct(PropertyManagerRepository $propertyManagerRepo, UserRepository $userRepo)
    {
        $this->propertyManagerRepository = $propertyManagerRepo;
        $this->userRepository = $userRepo;
    }

    /**
     * @SWG\Get(
     *      path="/propertyManagers",
     *      summary="Get a listing of the PropertyManagers.",
     *      tags={"PropertyManager"},
     *      description="Get all PropertyManagers",
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
     *                  @SWG\Items(ref="#/definitions/PropertyManager")
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
        $this->propertyManagerRepository->pushCriteria(new RequestCriteria($request, 'concat(first_name, " ", last_name)'));
        $this->propertyManagerRepository->pushCriteria(new LimitOffsetCriteria($request));
        $this->propertyManagerRepository->pushCriteria(new FilterByRelatedFieldsCriteria($request));
        $this->propertyManagerRepository->pushCriteria(new FilterByTypeCriteria($request));

        $hasRequest = $request->get('has_req', false);
        if ($hasRequest) {
            $this->propertyManagerRepository->pushCriteria(new HasRequestCriteria());
        }

        $getAll = $request->get('get_all', false);
        $this->propertyManagerRepository->with('user')->scope('allRequestStatusCount');

        if ($getAll) {
            $propertyManagers = $this->propertyManagerRepository->get();
            $response = (new PropertyManagerTransformer)->transformCollection($propertyManagers);
            return $this->sendResponse($response, 'Property Managers retrieved successfully');
        }

        $perPage = $request->get('per_page', env('APP_PAGINATE', 10));
        $propertyManagers = $this->propertyManagerRepository->paginate($perPage);
        $response = (new PropertyManagerTransformer)->transformPaginator($propertyManagers);
        return $this->sendResponse($response, 'Property Managers retrieved successfully');
    }

    /**
     * @SWG\Post(
     *      path="/propertyManagers",
     *      summary="Store a newly created PropertyManager in storage",
     *      tags={"PropertyManager"},
     *      description="Store PropertyManager",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="PropertyManager that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/PropertyManager")
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
     *                  ref="#/definitions/PropertyManager"
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
        $input['user']['role'] = (PropertyManager::TypeAdministrator == $request->type) ? 'administrator' : 'manager';

        $input['user']['name'] = sprintf('%s %s', $input['first_name'], $input['last_name']);
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
        } catch (\Exception $e) {
            return $this->sendError(__('models.property_manager.errors.create') . $e->getMessage());
        }

        $input['user_id'] = $user->id;
        try {
            $propertyManager = $this->propertyManagerRepository->create($input);
        } catch (\Exception $e) {
            return $this->sendError(__('models.property_manager.errors.create') . $e->getMessage());
        }

        $propertyManager->load('buildings', 'quarters');
        $propertyManager->setRelation('user', $user);
        $response = (new PropertyManagerTransformer)->transform($propertyManager);
        return $this->sendResponse($response, __('models.property_manager.saved'));
    }

    /**
     * @SWG\Get(
     *      path="/propertyManagers/{id}",
     *      summary="Display the specified PropertyManager",
     *      tags={"PropertyManager"},
     *      description="Get PropertyManager",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of PropertyManager",
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
     *                  ref="#/definitions/PropertyManager"
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
     */
    public function show($id, ViewRequest $r)
    {
        /** @var PropertyManager $propertyManager */
        $propertyManager = $this->propertyManagerRepository->find($id);
        $propertyManager->load('settings');

        if (empty($propertyManager)) {
            return $this->sendError(__('models.property_manager.errors.not_found'));
        }

        $propertyManager->load(['user', 'buildings', 'quarters'])
            ->loadCount('requests', 'solvedRequests', 'pendingRequests', 'buildings');
        $response = (new PropertyManagerTransformer)->transform($propertyManager);
        return $this->sendResponse($response, 'Property Manager retrieved successfully');
    }

    /**
     * @SWG\Put(
     *      path="/propertyManagers/{id}",
     *      summary="Update the specified PropertyManager in storage",
     *      tags={"PropertyManager"},
     *      description="Update PropertyManager",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of PropertyManager",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="PropertyManager that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/PropertyManager")
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
     *                  ref="#/definitions/PropertyManager"
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
     * @throws \Exception
     */
    public function update($id, UpdateRequest $request)
    {
        $input = $request->all();
        /** @var PropertyManager $propertyManager */
        $propertyManager = $this->propertyManagerRepository->find($id);
        if (empty($propertyManager)) {
            return $this->sendError(__('models.property_manager.errors.not_found'));
        }

        if (isset($input['user'])) {
            $input['user']['name'] = sprintf('%s %s', $input['first_name'], $input['last_name']);;
            if (empty($input['user']['password'])) {
                unset($input['user']['password']);
            }
            if (empty($input['user']['password_confirmation'])) {
                unset($input['user']['password_confirmation']);
            }
            $validator = Validator::make($input['user'], User::$rulesUpdate);
            if ($validator->fails()) {
                return $this->sendError($validator->errors());
            }
        }

        $input['user']['settings'] = Arr::pull($input, 'settings', []);
        try {
            User::disableAuditing();
            $user = $this->userRepository->update($input['user'], $propertyManager->user_id);
            if (isset($input['type']) && $input['type'] != $propertyManager->type) {
                $role = get_type_correspond_role($input['type']);
                $user->roles()->sync($role);
            }
            User::enableAuditing();
        } catch (\Exception $e) {
            return $this->sendError(__('models.property_manager.errors.update') . $e->getMessage());
        }

        try {
            $propertyManager = $this->propertyManagerRepository->updateExisting($propertyManager, $input);
            $propertyManager->addDataInAudit(AuditableModel::MergeInMainData, $user);
        } catch (\Exception $e) {
            return $this->sendError(__('models.property_manager.errors.update') . $e->getMessage());
        }

        $propertyManager->load('user', 'buildings', 'quarters', 'settings');
        $response = (new PropertyManagerTransformer)->transform($propertyManager);
        return $this->sendResponse($response, __('models.property_manager.saved'));
    }

    /**
     * @SWG\Delete(
     *      path="/propertyManagers/{id}",
     *      summary="Remove the specified PropertyManager from storage",
     *      tags={"PropertyManager"},
     *      description="Delete PropertyManager",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of PropertyManager",
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
     * @param DeleteRequest $r
     * @return mixed
     * @throws \Exception
     */
    public function destroy($id, DeleteRequest $r)
    {
        /** @var PropertyManager $propertyManager */
        $propertyManager = $this->propertyManagerRepository->find($id);
        if (empty($propertyManager)) {
            return $this->sendError(__('models.property_manager.errors.not_found'));
        }

        $propertyManager->delete();

        return $this->sendResponse($id, __('models.property_manager.deleted'));
    }

    /**
     * @SWG\Post(
     *      path="/propertyManagers/{id}/quarters/{did}",
     *      summary="Assign the provided quarter to the property manager",
     *      tags={"PropertyManager"},
     *      description="Assign the provided quarter to the property manager",
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
     *                  ref="#/definitions/PropertyManager"
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
     * @param QuarterRepository $quarterRepository
     * @param AssignRequest $r
     * @return mixed
     */
    public function assignQuarter(int $id, int $did, QuarterRepository $quarterRepository, AssignRequest $r)
    {
        $propertyManager = $this->propertyManagerRepository->findWithoutFail($id);
        if (empty($propertyManager)) {
            return $this->sendError(__('models.property_manager.errors.not_found'));
        }
        $quarter = $quarterRepository->findWithoutFail($did);
        if (empty($quarter)) {
            return $this->sendError(__('models.property_manager.errors.quarter_not_found'));
        }

        $propertyManager->quarters()->sync([$quarter->id => ['created_at' => now()]], false);
        $propertyManager->load('quarters', 'buildings');

        return $this->sendResponse($propertyManager, __('general.attached.quarter'));
    }

    /**
     * @SWG\Delete(
     *      path="/propertyManagers/{id}/quarters/{did}",
     *      summary="Unassign the provided quarter from the property manager",
     *      tags={"PropertyManager"},
     *      description="Unassign the provided quarter from the property manager",
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
     *                  ref="#/definitions/PropertyManager"
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
     * @param QuarterRepository $quarterRepository
     * @param UnAssignRequest $r
     * @return mixed
     */
    public function unassignQuarter(int $id, int $did, QuarterRepository $quarterRepository, UnAssignRequest $r)
    {
        $propertyManager = $this->propertyManagerRepository->findWithoutFail($id);
        if (empty($propertyManager)) {
            return $this->sendError(__('models.property_manager.errors.not_found'));
        }

        $quarter = $quarterRepository->findWithoutFail($did);
        if (empty($quarter)) {
            return $this->sendError(__('models.property_manager.errors.quarter_not_found'));
        }

        $propertyManager->quarters()->detach($quarter);
        $propertyManager->load('quarters', 'buildings');

        return $this->sendResponse($propertyManager, __('general.detached.quarter'));
    }

    /**
     * @SWG\Post(
     *      path="/propertyManagers/{id}/buildings/{bid}",
     *      summary="Assign the provided building to the property manager",
     *      tags={"PropertyManager"},
     *      description="Assign the provided building to the property manager",
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
     *                  ref="#/definitions/PropertyManager"
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
     * @param BuildingRepository $buildingRepository
     * @param AssignRequest $r
     * @return mixed
     */
    public function assignBuilding(int $id, int $bid, BuildingRepository $buildingRepository, AssignRequest $r)
    {
        $propertyManager = $this->propertyManagerRepository->findWithoutFail($id);
        if (empty($propertyManager)) {
            return $this->sendError(__('models.property_manager.errors.not_found'));
        }

        $building = $buildingRepository->findWithoutFail($bid);
        if (empty($building)) {
            return $this->sendError(__('models.property_manager.errors.building_not_found'));
        }

        // @TODO discuss with
        if ($building->quarter_id && $propertyManager->quarters()->where('quarter_id', 5)->exists()) {
            return $this->sendError(__('models.property_manager.errors.building_already_assign'));
        }

        $propertyManager->buildings()->sync([$building->id => ['created_at' => now()]], false);
        $propertyManager->load('quarters', 'buildings');

        return $this->sendResponse($propertyManager, __('general.attached.building'));
    }

    /**
     * @SWG\Delete(
     *      path="/propertyManagers/{id}/buildings/{bid}",
     *      summary="Unassign the provided building from the property manager",
     *      tags={"PropertyManager"},
     *      description="Unassign the provided building from the property manager",
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
     *                  ref="#/definitions/PropertyManager"
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
     * @param BuildingRepository $buildingRepository
     * @param UnAssignRequest $r
     * @return mixed
     */
    public function unassignBuilding(int $id, int $bid, BuildingRepository $buildingRepository, UnAssignRequest $r)
    {
        $propertyManager = $this->propertyManagerRepository->findWithoutFail($id);
        if (empty($propertyManager)) {
            return $this->sendError(__('models.property_manager.errors.not_found'));
        }

        $building = $buildingRepository->findWithoutFail($bid);
        if (empty($building)) {
            return $this->sendError(__('models.property_manager.errors.building_not_found'));
        }

        $propertyManager->buildings()->detach($building);
        $propertyManager->load('quarters', 'buildings');

        return $this->sendResponse($propertyManager, __('general.detached.building'));
    }

    /**
     * @SWG\Delete(
     *      path="/propertyManagers/batchDelete",
     *      summary="Remove batch PropertyManager from storage",
     *      tags={"PropertyManager"},
     *      description="Delete PropertyManager",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of PropertyManager",
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
     * @param BatchDeleteRequest $request
     * @return mixed
     */
    public function batchDelete(BatchDeleteRequest $request)
    {
        $managerIds = $request->get('managerIds', []);
        $assignee = $request->get('assignee', 0);
        if (in_array($assignee, $managerIds)) {
            return $this->sendError(__('models.property_manager.errors.building_assign_deleted_property_manager'));
        }

        $response = [
            'success' => 0,
            'fail' => 0
        ];

        $buildingIds = [];
        foreach ($managerIds as $managerId) {
            try {
                /** @var PropertyManager $propertyManager */
                $propertyManager = $this->propertyManagerRepository->find($managerId);
                if (empty($propertyManager)) {
                    return $this->sendError(__('models.property_manager.errors.not_found'));
                }

                $assignedBuildingIds = $propertyManager->buildings()->pluck('buildings.id')->toArray();
                $buildingIds = array_merge($buildingIds, $assignedBuildingIds);

                $this->propertyManagerRepository->delete($managerId);
            } catch (\Exception $e) {
                $response['fail']++;
                continue;
            }
            $response['success']++;
        }

        if ($assignee) {
            $propertyManager = $this->propertyManagerRepository->find($assignee);
            if (empty($propertyManager)) {
                return $this->sendError(__('models.property_manager.errors.not_found'));
            }
            $propertyManager->buildings()->sync($buildingIds, false);
        }

        return $this->sendResponse($response, __('models.property_manager.deleted'));
    }

    /**
     * @SWG\Get(
     *      path="/requests/{id}/assignments",
     *      summary="Get a listing of the ServiceProvider assigned buildings and quarters.",
     *      tags={"Request"},
     *      description="Get a listing of the ServiceProvider assigned buildings and quarters.",
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
     *                  @SWG\Items(ref="#/definitions/Request")
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
     * @return mixed
     */
    public function getAssignments(int $id, ViewRequest $request)
    {
        /** @var PropertyManager $propertyManager */
        $propertyManager = $this->propertyManagerRepository->find($id);
        if (empty($propertyManager)) {
            return $this->sendError(__('models.property_manager.errors.not_found'));
        }

        $perPage = $request->get('per_page', env('APP_PAGINATE', 10));
        $assignments = $this->propertyManagerRepository->assignments($propertyManager)->paginate($perPage);
        return $this->sendResponse($assignments, 'Assignments retrieved successfully');
    }

    /**
     * @param Request $request
     * @return mixed
     *  // @TODO ROLE RELATED
     */
    public function getIDsAssignmentsCount(Request $request)
    {
        /** @var PropertyManager $propertyManager */
        $propertyManagerArray = $this->propertyManagerRepository->find($request->post('ids'));
        if (empty($propertyManagerArray)) {
            return $this->sendError(__('models.property_manager.errors.not_found'));
        }
        $assignments = $this->propertyManagerRepository->assignmentsWithIds($propertyManagerArray->pluck('id')->all())->count();
        return $this->sendResponse($assignments, 'Assignments retrieved successfully');
    }
}
