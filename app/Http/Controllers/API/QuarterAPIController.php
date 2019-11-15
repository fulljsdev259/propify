<?php

namespace App\Http\Controllers\API;

use App\Criteria\Quarter\FilterByStateCriteria;
use App\Http\Controllers\AppBaseController;
use App\Http\Requests\API\Quarter\AssigneeListRequest;
use App\Http\Requests\API\Quarter\BatchAssignManagers;
use App\Http\Requests\API\Quarter\BatchAssignUsers;
use App\Http\Requests\API\Quarter\CreateRequest;
use App\Http\Requests\API\Quarter\EmailReceptionistRequest;
use App\Http\Requests\API\Quarter\UnAssignRequest;
use App\Http\Requests\API\Quarter\UpdateRequest;
use App\Http\Requests\API\Quarter\ListRequest;
use App\Http\Requests\API\Quarter\ViewRequest;
use App\Http\Requests\API\Quarter\DeleteRequest;
use App\Models\Address;
use App\Models\AuditableModel;
use App\Models\EmailReceptionist;
use App\Models\PropertyManager;
use App\Models\Quarter;
use App\Models\QuarterAssignee;
use App\Models\Contract;
use App\Models\ServiceProvider;
use App\Models\User;
use App\Repositories\AddressRepository;
use App\Repositories\QuarterRepository;
use App\Transformers\EmailReceptionistTransformer;
use App\Transformers\QuarterAssigneeTransformer;
use App\Transformers\QuarterTransformer;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use App\Criteria\Common\RequestCriteria;

/**
 * Class QuarterController
 * @package App\Http\Controllers\API
 */
class QuarterAPIController extends AppBaseController
{
    /** @var  QuarterRepository */
    private $quarterRepository;

    /**
     * @var AddressRepository
     */
    private $addressRepository;

    /**
     * QuarterAPIController constructor.
     * @param QuarterRepository $quarterRepository
     * @param AddressRepository $addressRepository
     */
    public function __construct(QuarterRepository $quarterRepository, AddressRepository $addressRepository)
    {
        $this->quarterRepository = $quarterRepository;
        $this->addressRepository = $addressRepository;
    }

    /**
     * @SWG\Get(
     *      path="/quarters",
     *      summary="Get a listing of the Quarters.",
     *      tags={"Quarter"},
     *      description="Get all Quarters",
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
     *                  @SWG\Items(ref="#/definitions/Quarter")
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
        $this->quarterRepository->pushCriteria(new RequestCriteria($request));
        $this->quarterRepository->pushCriteria(new LimitOffsetCriteria($request));
        $this->quarterRepository->pushCriteria(new FilterByStateCriteria($request));

        $getAll = $request->get('get_all', false);
        if ($getAll) {
            $quarters = $this->quarterRepository->get();
            $response = (new QuarterTransformer)->transformCollection($quarters);
            return $this->sendResponse($response, 'Quarters retrieved successfully');
        }

        $perPage = $request->get('per_page', env('APP_PAGINATE', 10));
        $quarters = $this->quarterRepository->with([
                'buildings' => function ($q) {
                    $q->select('id', 'quarter_id')
                        ->with([
                            'units' => function ($q) {
                                $q ->select('id', 'building_id')
                                    ->with([
                                        'contracts' => function ($q) {
                                             $q->where('status', Contract::StatusActive)->select('unit_id', 'resident_id');
                                        }
                                    ]);
                            }]);
                },
                'media'
            ])->paginate($perPage);
        $response = (new QuarterTransformer)->transformPaginator($quarters, 'transformWIthStatistics');
        return $this->sendResponse($response, 'Quarters retrieved successfully');
    }

    /**
     * @SWG\Post(
     *      path="/quarters",
     *      summary="Store a newly created Quarter in storage",
     *      tags={"Quarter"},
     *      description="Store Quarter",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="Quarter that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/Quarter")
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
     *                  ref="#/definitions/Quarter"
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
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     * @throws \OwenIt\Auditing\Exceptions\AuditingException
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function store(CreateRequest $request)
    {
        $input = $request->all();
        $addressInput = $request->get('address');
        DB::beginTransaction();
        if (!empty($addressInput) && is_array($addressInput)) {
            $validator = Validator::make($addressInput, Address::$rules);
            if ($validator->fails()) {
                DB::rollBack();
                return $this->sendError($validator->errors());
            }

            $address = $this->addressRepository->create($addressInput);
            $input['address_id'] = $address->id;
            unset($input['address']);
        }

        $quarter = $this->quarterRepository->create($input);

        if ($quarter) {

            if (isset($address)) {
                $quarter->addDataInAudit(AuditableModel::MergeInMainData, $address);
            }

            DB::commit();
            $quarter->load('address', 'media', 'workflows');
            $quarter->setHasRelation('email_receptionists');
            $response = (new QuarterTransformer)->transform($quarter);

        } else {
            $response = [];
            DB::rollBack();
        }


        return $this->sendResponse($response, __('models.quarter.saved'));
    }

    /**
     * @SWG\Get(
     *      path="/quarters/{id}",
     *      summary="Display the specified Quarter",
     *      tags={"Quarter"},
     *      description="Get Quarter",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Quarter",
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
     *                  ref="#/definitions/Quarter"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     *
     *
     * @param $id
     * @param ViewRequest $r
     * @return mixed
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function show($id, ViewRequest $r)
    {
        /** @var Quarter $quarter */
        $quarter = $this->quarterRepository->with('address')->findWithoutFail($id);
        if (empty($quarter)) {
            return $this->sendError(__('models.quarter.errors.not_found'));
        }
        $quarter->load([
            'media',
            'workflows',
            'buildings' => function ($q) {
                $q->with([
                    'contracts' => function ($q) {
                        $q->with('building.address', 'unit', 'resident.user');
                    },
                ]);
            }
        ]);
        $quarter->setHasRelation('email_receptionists');

        $response = (new QuarterTransformer)->transform($quarter);
        return $this->sendResponse($response, 'Quarter retrieved successfully');
    }

    /**
     * @SWG\Put(
     *      path="/quarters/{id}",
     *      summary="Update the specified Quarter in storage",
     *      tags={"Quarter"},
     *      description="Update Quarter",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Quarter",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="Quarter that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/Quarter")
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
     *                  ref="#/definitions/Quarter"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     *
     *
     * @param $id
     * @param UpdateRequest $request
     * @return mixed
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     * @throws \OwenIt\Auditing\Exceptions\AuditingException
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function update($id, UpdateRequest $request)
    {
        $input = $request->all();

        /** @var Quarter $quarter */
        $quarter = $this->quarterRepository->with('address')->findWithoutFail($id);

        if (empty($quarter)) {
            return $this->sendError(__('models.quarter.errors.not_found'));
        }

        DB::beginTransaction();
        $addressInput = $request->get('address');
        if (empty($addressInput)) {
            if (isset($request->state_id)) {
                $addressInput['state_id'] = $request->state_id;
            }

            if (isset($request->city)) {
                $addressInput['city'] = $request->city;
            }

            if (isset($request->zip)) {
                $addressInput['zip'] = $request->zip;
            }

            if (isset($request->street)) {
                $addressInput['street'] = $request->street;
            }
            if (isset($request->house_num)) {
                $addressInput['house_num'] = $request->house_num;
            }
        }


        if ($addressInput) {
            $validator = Validator::make($addressInput, Address::$rules);
            if ($validator->fails()) {
                DB::rollBack();
                return $this->sendError($validator->errors());
            }

            if ($quarter->address) {
                $address = $this->addressRepository->updateExisting($quarter->address, $addressInput);
            } else {
                $address = $this->addressRepository->create($addressInput);
                $input['address_id'] = $address->id;
            }

            $input['address_id'] = $address->id;
            unset($input['address']);
        }


        $quarter = $this->quarterRepository->updateExisting($quarter, $input);

        if ($quarter) {

            if (isset($address)) {
                $quarter->addDataInAudit(AuditableModel::MergeInMainData, $address, AuditableModel::UpdateOrCreate);
            }

            DB::commit();
            $quarter->load('address', 'media', 'workflows');
            $quarter->setHasRelation('email_receptionists');
            $response = (new QuarterTransformer)->transform($quarter);

        } else {
            DB::rollBack();
            $response = [];
        }

        return $this->sendResponse($response, __('models.quarter.saved'));
    }

    /**
     * @SWG\Delete(
     *      path="/quarters/{id}",
     *      summary="Remove the specified Quarter from storage",
     *      tags={"Quarter"},
     *      description="Delete Quarter",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Quarter",
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
        /** @var Quarter $quarter */
        $quarter = $this->quarterRepository->findWithoutFail($id);

        if (empty($quarter)) {
            return $this->sendError(__('models.quarter.errors.not_found'));
        }

        $quarter->delete();

        return $this->sendResponse($id, __('models.quarter.deleted'));
    }

    /**
     * @param DeleteRequest $request
     * @return mixed
     */
    public function destroyWithIds(DeleteRequest $request){
        $ids = $request->get('ids');
        try{
            Quarter::destroy($ids);
        }
        catch (\Exception $e) {
            return $this->sendError(__('models.quarter.errors.deleted') . $e->getMessage());
        }
        return $this->sendResponse($ids, __('models.quarter.deleted'));
    }

    /**
     * @SWG\Get(
     *      path="/quarters/{id}/assignees",
     *      summary="Get a listing of the Quarter assignees.",
     *      tags={"Quarter"},
     *      description="Get a listing of the Quarter assignees.",
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
     *                  @SWG\Items(ref="#/definitions/QuarterAssignee")
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
    public function getAssignees(int $id, ViewRequest $request)
    {
        // @TODO permissions
        $quarter = $this->quarterRepository->findWithoutFail($id);
        if (empty($quarter)) {
            return $this->sendError(__('models.quarter.errors.not_found'));
        }

        $perPage = $request->get('per_page', env('APP_PAGINATE', 10));
        $assignees = $quarter->assignees()->paginate($perPage);
        $assignees = $this->getAssigneesRelated($assignees, [PropertyManager::class, User::class, ServiceProvider::class]);

        $response = (new QuarterAssigneeTransformer())->transformPaginator($assignees) ;

        return $this->sendResponse($response, 'Assignees retrieved successfully');
    }

    /**
     * @SWG\Post(
     *      path="/quarters/{id}/managers",
     *      summary="Assign the provided propertyManagers to the Quarter",
     *      tags={"Quarter"},
     *      description=" <a href='http://dev.propify.ch/api/docs#/Quarter/pinboard_quarters__id__managers'>http://dev.propify.ch/api/docs#/Quarter/pinboard_quarters__id__managers</a>",
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
     *                  ref="#/definitions/Quarter"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     *
     *
     * @param int $id
     * @param BatchAssignManagers $request
     * @return mixed
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function assignManagers(int $id, BatchAssignManagers $request)
    {
        /** @var Quarter $quarter */
        $quarter = $this->quarterRepository->findWithoutFail($id);
        if (empty($quarter)) {
            return $this->sendError(__('models.quarter.errors.not_found'));
        }

        $managerIds = $request->get('managersIds') ?? $request->get('managerIds');
        try {
            $currentManagers = $quarter->propertyManagers()
                ->whereIn('property_managers.id', $managerIds)
                ->pluck('property_managers.id')
                ->toArray();

            $newManagers = array_diff($managerIds, $currentManagers);
            $attachData  = [];
            foreach ($newManagers as $manager) {
                $attachData[$manager] = ['created_at' => now()];
            }
            $quarter->propertyManagers()->syncWithoutDetaching($attachData);
        } catch (\Exception $e) {
            return $this->sendError( __('models.quarter.errors.manager_assigned') . $e->getMessage());
        }

        $response = (new QuarterTransformer)->transform($quarter);
        return $this->sendResponse($response, __('models.quarter.managers_assigned'));
    }

    /**
     * @SWG\Post(
     *      path="/quarters/{id}/users",
     *      summary="Assign the provided users to the Quarter",
     *      tags={"Quarter"},
     *      description="Assign the provided users(administrator, super-administrator) to the Quarter",
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
     *                  ref="#/definitions/Quarter"
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
     * @return mixed
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function assignUsers(int $id, BatchAssignUsers $request)
    {
        /** @var Quarter $quarter */
        $quarter = $this->quarterRepository->findWithoutFail($id);
        if (empty($quarter)) {
            return $this->sendError(__('models.quarter.errors.not_found'));
        }

        $userId = $request->get('user_id');
        $user = User::find($userId);
        if (empty($user)) {
            return $this->sendError(__('models.user.errors.not_found'));
        }

        $role = $request->role;
        if (in_array($role, ['manager', 'administrator'])) {
            $propertyManagerId = PropertyManager::where('user_id', $user->id)->value('id');
            if (empty($propertyManagerId)) {
                $assigneeId = $userId;
                $assigneeType = get_morph_type_of(User::class);
            } else {
                $assigneeId = $propertyManagerId;
                $assigneeType = get_morph_type_of(PropertyManager::class);
            }
        } else {
            $serviceProviderId = ServiceProvider::where('user_id', $user->id)->value('id');
            if (empty($serviceProviderId)) {
                $assigneeId = $userId;
                $assigneeType = get_morph_type_of(User::class);
            } else {
                $assigneeId = $serviceProviderId;
                $assigneeType = get_morph_type_of(ServiceProvider::class);
            }
        }

        QuarterAssignee::updateOrCreate([
            'quarter_id' => $id,
            'user_id' => $userId,
            'assignee_id' => $assigneeId,
            'assignee_type' => $assigneeType,
        ], [
            'assignment_types' => $request->assignment_types,
            'created_at' => now()
        ]);

        $response = (new QuarterTransformer)->transform($quarter);
        return $this->sendResponse($response, __('general.attached.manager'));
    }

    /**
     * @SWG\Delete(
     *      path="/quarters-assignees/{quarters_assignee_id}",
     *      summary="Unassign the user or manager to the quarter",
     *      tags={"Quarter", "User", "PropertyManager"},
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
    public function deleteQuarterAssignee(int $id, UnAssignRequest $request)
    {
        $quarterAssignee = QuarterAssignee::find($id);
        if (empty($quarterAssignee)) {
            // @TODO fix message
            return $this->sendError(__('models.quarter.errors.not_found'));
        }
        $quarterAssignee->delete();

        return $this->sendResponse($id, __('general.detached.' . $quarterAssignee->assignee_type));
    }

    /**
     * @SWG\Get(
     *      path="/quarters/{id}/email-receptionists",
     *      summary="get quarter email-receptionists",
     *      tags={"Quarter", "EmailReceptionists"},
     *      description="get quarter email-receptionists",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of quarter",
     *          type="integer",
     *          required=true,
     *          in="query",
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
     * @param $quarterId
     * @param EmailReceptionistRequest $emailReceptionistRequest
     * @return mixed
     */
    public function getEmailReceptionists($quarterId, EmailReceptionistRequest $emailReceptionistRequest)
    {
        /** @var Quarter $quarter */
        $quarter = $this->quarterRepository->findWithoutFail($quarterId);
        if (empty($quarter)) {
            return $this->sendError(__('models.quarter.errors.not_found'));
        }
        $quarter->load([
            'email_receptionists:id,category,property_manager_id,model_id',
            'email_receptionists.property_manager:id,first_name,last_name'
        ]);
        $response['email_receptionists'] = (new EmailReceptionistTransformer())->transformEmailReceptionists($quarter->email_receptionists);
        $response['quarter_id'] = $quarterId;
        return $this->sendResponse($response, __('Email Receptionist get successfully'));
    }

    /**
     *  @SWG\Post(
     *      path="/quarters/{id}/email-receptionists",
     *      summary="set quarter email-receptionists",
     *      tags={"Quarter", "EmailReceptionist"},
     *      description="set quarter email-receptionists",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of quarter",
     *          type="integer",
     *          required=true,
     *          in="query",
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
     * @param $quarterId
     * @param EmailReceptionistRequest $emailReceptionistRequest
     * @return mixed
     * @throws \OwenIt\Auditing\Exceptions\AuditingException
     */
    public function storeEmailReceptionists($quarterId, EmailReceptionistRequest $emailReceptionistRequest)
    {
        /** @var Quarter $quarter */
        $quarter = $this->quarterRepository->findWithoutFail($quarterId);
        if (empty($quarter)) {
            return $this->sendError(__('models.quarter.errors.not_found'));
        }

        $modelType = get_morph_type_of(Quarter::class);
        $data = $emailReceptionistRequest->toArray();
        $emailReceptionists = $quarter->email_receptionists()->get(['category', 'id', 'property_manager_id']);
        $needDelete = $emailReceptionists->whereNotIn('category', collect($data)->pluck('category'));

        foreach ($data as $single) {
            if (empty($single['category']) || ! key_exists($single['category'], \App\Models\Request::Category))  {
                continue;
            }

            $category = $single['category'];
            $categoryEmailReceptionists = $emailReceptionists->where('category', $category);

            if (empty($single['property_manager_ids']) || ! is_array($single['property_manager_ids']))  {
                $needDelete = $needDelete->merge($categoryEmailReceptionists);
                continue;
            }

            $deletedEmailReceptionists = $categoryEmailReceptionists->whereNotIn('property_manager_id', $single['property_manager_ids']);
            $needDelete = $needDelete->merge($deletedEmailReceptionists);

            foreach ($single['property_manager_ids'] as $propertyManagerId) {
                if ($categoryEmailReceptionists->contains('property_manager_id', $propertyManagerId)) {
                    continue;
                }
                $savedData = [
                    'category' => $category,
                    'property_manager_id' => $propertyManagerId,
                    'model_type' => $modelType,
                ];
                $new = $quarter->email_receptionists()->create($savedData);
                $categoryEmailReceptionists->push($new);
            }
        }

        //@TODO audit
        foreach ($needDelete as $emailReceptionist) {
            $emailReceptionist->delete();
        }


        $quarter->load([
            'email_receptionists:id,category,property_manager_id,model_id',
            'email_receptionists.property_manager:id,first_name,last_name'
        ]);

        $quarter->auditEmailReceptionists($emailReceptionists, $quarter->email_receptionists);
        $response['email_receptionists'] = (new EmailReceptionistTransformer())->transformEmailReceptionists($quarter->email_receptionists);
        $response['quarter_id'] = $quarterId;

        return $this->sendResponse($response, __('Email Receptionists get successfully'));
    }
}
