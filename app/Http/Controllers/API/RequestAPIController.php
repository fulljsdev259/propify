<?php

namespace App\Http\Controllers\API;

use App\Criteria\Common\RequestCriteria;
use App\Criteria\Common\WhereInCriteria;
use App\Criteria\Request\FilterByInternalFieldsCriteria;
use App\Criteria\Request\FilterByPermissionsCriteria;
use App\Criteria\Request\FilterByRelatedFieldsCriteria;
use App\Criteria\Request\FilterCreatorCriteria;
use App\Criteria\Request\FilterNotAssignedCriteria;
use App\Criteria\Request\FilterPendingCriteria;
use App\Criteria\Request\FilterPublicCriteria;
use App\Http\Controllers\AppBaseController;
use App\Http\Requests\API\Request\MassAssignUsersRequest;
use App\Http\Requests\API\Request\AssignRequest;
use App\Http\Requests\API\Request\ChangePriorityRequest;
use App\Http\Requests\API\Request\ChangeStatusRequest;
use App\Http\Requests\API\Request\CreateRequest;
use App\Http\Requests\API\Request\DeleteRequest;
use App\Http\Requests\API\Request\ListRequest;
use App\Http\Requests\API\Request\MassEditRequest;
use App\Http\Requests\API\Request\NotifyProviderRequest;
use App\Http\Requests\API\Request\SeeRequestsCount;
use App\Http\Requests\API\Request\UnAssignRequest;
use App\Http\Requests\API\Request\UpdateRequest;
use App\Http\Requests\API\Request\ViewRequest;
use App\Http\Requests\API\Resident\DownloadPdfRequest;
use App\Jobs\Notify\NotifyRequestProvider;
use App\Mails\SentEmailPdf;
use App\Models\PropertyManager;
use App\Models\ServiceProvider;
use App\Models\Request;
use App\Models\RequestAssignee;
use App\Models\User;
use App\Repositories\PropertyManagerRepository;
use App\Repositories\ServiceProviderRepository;
use App\Repositories\RequestRepository;
use App\Repositories\SettingsRepository;
use App\Repositories\TagRepository;
use App\Repositories\TemplateRepository;
use App\Repositories\UserRepository;
use App\Transformers\RequestAssigneeTransformer;
use App\Transformers\RequestTransformer;
use App\Transformers\TagTransformer;
use App\Transformers\TemplateTransformer;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Auth;
use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Mail;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;

/**
 * Class RequestAPIController
 * @package App\Http\Controllers\API
 */
class RequestAPIController extends AppBaseController
{
    /** @var  SettingsRepository */
    private $settingsRepository;

    /** @var  RequestRepository */
    private $requestRepository;

    /**
     * RequestAPIController constructor.
     * @param RequestRepository $requestRepository
     */
    public function __construct(RequestRepository $requestRepository, SettingsRepository $settingsRepo)
    {
        $this->settingsRepository = $settingsRepo;
        $this->requestRepository = $requestRepository;
    }

    /**
     * @SWG\Get(
     *      path="/requests",
     *      summary="Get a listing of the Requests.",
     *      tags={"Request"},
     *      description="Get all Requests",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="service_provider_id",
     *          in="query",
     *          type="integer",
     *          description=" Filter by ServiceProvider",
     *          required=false,
     *      ),
     *     @SWG\Parameter(
     *          name="property_manager_id",
     *          in="query",
     *          type="integer",
     *          description=" Filter by Property Manager",
     *          required=false,
     *      ),
     *     @SWG\Parameter(
     *          name="category_id",
     *          in="query",
     *          type="integer",
     *          description=" Filter by Category",
     *          required=false,
     *      ),
     *
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
     * @param ListRequest $listRequest
     * @return mixed
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function index(ListRequest $listRequest)
    {
        $this->requestRepository->pushCriteria(new RequestCriteria($listRequest));
        $this->requestRepository->pushCriteria(new LimitOffsetCriteria($listRequest));
        $this->requestRepository->pushCriteria(new FilterByPermissionsCriteria($listRequest));
        $this->requestRepository->pushCriteria(new FilterByInternalFieldsCriteria($listRequest));
        $this->requestRepository->pushCriteria(new FilterPublicCriteria($listRequest));
        $this->requestRepository->pushCriteria(new FilterByRelatedFieldsCriteria($listRequest));
        $this->requestRepository->pushCriteria(new FilterPendingCriteria($listRequest));
        $this->requestRepository->pushCriteria(new FilterNotAssignedCriteria($listRequest));
        $this->requestRepository->pushCriteria(new FilterCreatorCriteria($listRequest));

        $getAll = $listRequest->get('get_all', false);
        if ($getAll) {
            $requests = $this->requestRepository->get();
            $response = (new RequestTransformer)->transformCollection($requests);
            return $this->sendResponse($response, 'Requests retrieved successfully');
        }
        $perPage = $listRequest->get('per_page', env('APP_PAGINATE', 10));
        $requests = $this->requestRepository
            ->with([
                'media',
                'resident.user',
                'resident.relations' => function ($q) {
                    $q->with('quarter.address', 'unit');
                },
                'relation' => function ($q) {
                    $q->with('quarter.address', 'unit');
                },
                'comments.user',
                'assignees' => function ($q) {
                    // TODO make more optional query for find each type only one assignee
                    $q->select('id', 'type', 'user_id', 'request_id')
                        ->with([
                            'user' => function ($q) {
                                $q->select('users.id', 'users.avatar', 'users.name')
                                    ->with('roles:roles.id,name');
                            }
                        ])
//                        ->groupBy('request_assignees.type')
//                        ->groupBy('request_assignees.request_id')
                        ->orderBy('request_assignees.id', 'desc');
                },
                'creator'
            ])->paginate($perPage);

        $requests->getCollection()->loadCount('allComments');
        $response = (new RequestTransformer)->transformPaginator($requests);
        return $this->sendResponse($response, 'Service Requests retrieved successfully');
    }

    /**
     * @SWG\Post(
     *      path="/requests",
     *      summary="Store a newly created Request in storage",
     *      tags={"Request"},
     *      description="Store Request",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="Request that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/Request")
     *      ),
     *     @SWG\Parameter(
     *          name="media",
     *          in="body",
     *          description="Save media. You can pass media paramater
                                As string(base64) like **media=base64_string**,
                                as array of string(base64) like **media = [base64_string1, base64_string2, base64_string3, ...etc]**
                                or as array of array(media => string(base64)) like media = **[[media => base64_string1], [media => base64_string2], ...etc]**",
     *          required=false,
     *          @SWG\Schema(
     *              @SWG\Property(
     *                  property="media",
     *                  description="id",
     *                  type="integer",
     *                  format="int32"
     *               ),
     *
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
     *                  ref="#/definitions/Request"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     *
     * @param CreateRequest $createRequest
     * @return mixed
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     * @throws \OwenIt\Auditing\Exceptions\AuditingException
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function store(CreateRequest $createRequest)
    {
        $input = $createRequest->all();
//        $input['internal_priority'] = $input['internal_priority'] ?? $input['priority'];
        $request = $this->requestRepository->create($input);
        $request->load([
            'media',
            'resident.user',
            'resident.relations' => function ($q) {
                $q->with('quarter.address', 'unit');
            },
            'relation'  => function ($q) {
                $q->with('quarter.address', 'unit');
            },
            'comments.user',
            'creator'
        ]);
        $response = (new RequestTransformer)->transform($request);
        return $this->sendResponse($response, __('models.request.saved'));
    }

    /**
     * @SWG\Get(
     *      path="/requests/{id}",
     *      summary="Display the specified Request",
     *      tags={"Request"},
     *      description="Get Request",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Request",
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
     *                  ref="#/definitions/Request"
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
     * @param ViewRequest $viewRequest
     * @return mixed
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function show($id, ViewRequest $viewRequest)
    {
        /** @var Request $request */
        $request = $this->requestRepository->findWithoutFail($id);

        if (empty($request)) {
            return $this->sendError(__('models.request.errors.not_found'));
        }

        $request->load([
            'media',
            'resident.user',
            'comments.user',
            'resident.relations' => function ($q) {
                $q->with('quarter.address', 'unit');
            },
            'relation' => function ($q) {
                $q->with('quarter.address', 'unit');
            },
            'assignees' => function ($q) {
                // TODO make more optional query for find each type only one assignee
                $q->select('id', 'type', 'user_id', 'request_id')
                    ->with([
                        'user' => function ($q) {
                            $q->select('users.id', 'users.avatar', 'users.name')
                                ->with('roles:roles.id,name');
                        }
                    ])
//                        ->groupBy('request_assignees.type')
//                        ->groupBy('request_assignees.request_id')
                    ->orderBy('request_assignees.id', 'desc');
            },
            'creator'
        ]);
        $response = (new RequestTransformer)->transform($request);
        return $this->sendResponse($response, 'Service Request retrieved successfully');
    }

    /**
     * @SWG\Put(
     *      path="/requests/{id}",
     *      summary="Update the specified Request in storage",
     *      tags={"Request"},
     *      description="Update Request",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Request",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="Request that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/Request")
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
     *                  ref="#/definitions/Request"
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
     * @param UpdateRequest $updateRequest
     * @return mixed
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function update($id, UpdateRequest $updateRequest)
    {
        $input = $updateRequest->only(Request::Fillable);
        /** @var Request $request */
        $request = $this->requestRepository->findWithoutFail($id);
        if (empty($request)) {
            return $this->sendError(__('models.request.errors.not_found'));
        }

        $oldStatus = $request->status;
        if (!$this->requestRepository->checkStatusPermission($input, $oldStatus)) {
            return $this->sendError(__('models.request.errors.not_allowed_change_status'));
        }
        $input['category_id'] = $input['category_id'] ?? $updateRequest->category; // @TODO delete
        $updatedRequest = $this->requestRepository->updateExisting($request, $input);

        $updatedRequest->load([
            'media',
            'resident.user',
            'relation' => function ($q) {
                $q->with('quarter.address', 'unit');
            },
            'resident.relations' => function ($q) {
                $q->with('quarter.address', 'unit');
            },
            'comments.user',
            'creator'
        ]);
        $response = (new RequestTransformer)->transform($updatedRequest);
        return $this->sendResponse($response, __('models.request.saved'));
    }

    /**
     * @SWG\Put(
     *      path="/requests/massedit",
     *      summary="Update many Request in storage",
     *      tags={"Request"},
     *      description="Update Request",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="request_ids",
     *          description="ids of Request",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="property_manager_ids",
     *          description="ids of Property Managere",
     *          type="integer",
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="service_provider_ids",
     *          description="ids of service providers",
     *          type="integer",
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="status",
     *          description="status",
     *          type="integer",
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
     *                  ref="#/definitions/Request"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     *
     * @param MassEditRequest $massEditRequest
     * @return mixed
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function massEdit(MassEditRequest $massEditRequest,DownloadPdfRequest $r)
    {
        //get in MassEditRequest validation class
        $requests = $massEditRequest->requests;
        $managers = $massEditRequest->managers;
        $sentEmail = $massEditRequest->send_email_service_provider;
        
        if ($managers) {
            $this->requestRepository->massAssign($requests, 'managers', $managers, $sentEmail);
        }

        //get in MassEditRequest validation class
        $providers = $massEditRequest->providers;
        if ($providers) {
            $this->requestRepository->massAssign($requests, 'providers', $providers, $sentEmail);
        }

        if ($sentEmail && $providers->isNotEmpty() && $requests->isNotEmpty()) {

            $users = $providers->load('user')->pluck('user');
            $settings = $this->settingsRepository->first();

            $data = [];
            $requests->load([
                'media',
                'resident',
                'relation'
            ]);
            foreach ($requests as $request) {
                /** @var $request Request */
                $data['datas'][] = $request->getDownloadAllPdfData();
            }

            // $request is income in foreach
            $request->setDownloadAllPdf($data, $settings);
            $pdfName = $request->pdfFileName();

            if (!\Storage::disk('request_downloads')->exists($pdfName)) {
                return $this->sendError('Request file not found!');
            }

            foreach ($users as $user) {
                $mail = (new SentEmailPdf($user, $pdfName));//->onQueue('high');
                Mail::to($user->email)->send($mail);
			}
   
		}

        $status = $massEditRequest->get('status');
        if ($status) {
            $this->requestRepository->massUpdateAttribute($requests, ['status' => $status]);
        }

        $response = [];
        foreach ($requests as $request) {
            $request->load([
                'media',
                'resident.user',
                'relation' => function ($q) {
                    $q->with('quarter.address', 'unit');
                },
                'resident.relations' => function ($q) {
                    $q->with('quarter.address', 'unit');
                },
                'comments.user',
                'creator'
            ]);
            $response[] = (new RequestTransformer)->transform($request);
        }

        return $this->sendResponse($response, __('models.request.saved'));
    }

    /**
     * @SWG\Post(
     *      path="/requests/{id}/changeStatus",
     *      summary="Change status on Request",
     *      tags={"Request"},
     *      description="Change status on Request",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="Request that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/Request")
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
     *                  ref="#/definitions/Request"
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
     * @param ChangeStatusRequest $changeStatusRequest
     * @return mixed
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function changeStatus(int $id, ChangeStatusRequest $changeStatusRequest)
    {
        /** @var Request $request */
        $request = $this->requestRepository->findWithoutFail($id);
        if (empty($request)) {
            return $this->sendError(__('models.request.errors.not_found'));
        }

        $input = ['status' => $changeStatusRequest->get('status', '')];

        if (!$this->requestRepository->checkStatusPermission($input, $request->status)) {
            return $this->sendError(__('models.request.errors.not_allowed_change_status'));
        }

        $request = $this->requestRepository->updateExisting($request, $input);
        $response = (new RequestTransformer)->transform($request);
        return $this->sendResponse($response, __('models.request.status_changed'));
    }

    /**
     * @SWG\Post(
     *      path="/requests/{id}/changePriority",
     *      summary="Change status on Request",
     *      tags={"Request"},
     *      description="Change priority on Request",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="Request that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/Request")
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
     *                  ref="#/definitions/Request"
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
     * @param ChangePriorityRequest $changePriorityRequest
     * @return mixed
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
//    public function changePriority(int $id, ChangePriorityRequest $changePriorityRequest)
//    {
//        /** @var Request $request */
//        $request = $this->requestRepository->findWithoutFail($id);
//        if (empty($request)) {
//            return $this->sendError(__('models.request.errors.not_found'));
//        }
//
//        $input = [
//            'priority' => $changePriorityRequest->get('priority', '')
//        ];
//
//        $request = $this->requestRepository->update($input, $id);
//
//        $response = (new RequestTransformer)->transform($request);
//        return $this->sendResponse($response, __('models.request.priority_changed'));
//    }

    /**
     * @SWG\Delete(
     *      path="/requests/{id}",
     *      summary="Remove the specified Request from storage",
     *      tags={"Request"},
     *      description="Delete Request",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Request",
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
     * @param DeleteRequest $deleteRequest
     * @return mixed
     * @throws Exception
     */
    public function destroy($id, DeleteRequest $deleteRequest)
    {
        /** @var Request $request */
        $request = $this->requestRepository->findWithoutFail($id);
        if (empty($request)) {
            return $this->sendError(__('models.request.errors.not_found'));
        }

        $request->delete();

        return $this->sendResponse($id, __('models.request.deleted'));
    }

    /**
     * @param DeleteRequest $deleteRequest
     * @return mixed
     */
    public function destroyWithIds(DeleteRequest $deleteRequest){
        $ids = $deleteRequest->get('ids');
        try{
            Request::destroy($ids);
        }
        catch (\Exception $e) {
            return $this->sendError(__('models.request.errors.deleted') . $e->getMessage());
        }
        return $this->sendResponse($ids, __('models.request.deleted'));
    }

    /**
     * @SWG\Post(
     *      path="/requests/{id}/notify",
     *      summary="Notify the provided service provider",
     *      tags={"Request"},
     *      description="Notify the provided service provider",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Request",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="provider_id",
     *          in="body",
     *          description="ServiceProvider id",
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
     * @param int $id
     * @param NotifyProviderRequest $notifyProviderRequest
     * @param ServiceProviderRepository $serviceProviderRepository
     * @param PropertyManagerRepository $propertyManagerRepository
     * @return mixed
     */
    public function notifyProvider(
        int $id,
        NotifyProviderRequest $notifyProviderRequest,
        ServiceProviderRepository $serviceProviderRepository,
        PropertyManagerRepository $propertyManagerRepository
    )
    {
        $request = $this->requestRepository->findWithoutFail($id);
        if (empty($request)) {
            return $this->sendError(__('models.request.errors.not_found'));
        }

        $providerId = $notifyProviderRequest->service_provider_id;
        $serviceProvider = $serviceProviderRepository->findWithoutFail($providerId);
        if (empty($serviceProvider)) {
            return $this->sendError(__('models.request.errors.provider_not_found'));
        }

        $managerId = $notifyProviderRequest->property_manager_id;

        $propertyManager = $managerId ? $propertyManagerRepository->with('user:email,id')->find($managerId) : null;
        $mailDetails = $notifyProviderRequest->only(['title', 'to', 'cc', 'bcc', 'body']);
        dispatch_now(new  NotifyRequestProvider($request, $serviceProvider, $propertyManager, $mailDetails));

        return $this->sendResponse($request, __('models.request.mail.success'));
    }



    /**
     * @SWG\Get(
     *      path="/requests/{id}/tags",
     *      summary="Get a listing of the Request tags.",
     *      tags={"Request", "Tag"},
     *      description="Get a listing of the Request tags.",
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
     *                  @SWG\Items(ref="#/definitions/Tag")
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
    public function getTags(int $id, ViewRequest $request)
    {
        $sr = $this->requestRepository->findWithoutFail($id);
        if (empty($sr)) {
            return $this->sendError(__('models.request.errors.not_found'));
        }

        $perPage = $request->get('per_page', env('APP_PAGINATE', 10));
        $tags = $sr->tags()->paginate($perPage);

        $response = (new TagTransformer())->transformPaginator($tags) ;
        return $this->sendResponse($response, 'Tags retrieved successfully');
    }

    /**
     * @SWG\Post(
     *      path="/requests/{id}/tags/{tid}",
     *      summary="Assign the tag to the request",
     *      tags={"Request", "Tag"},
     *      description="Assign the tag to the request",
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
     *                  ref="#/definitions/Request"
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
     * @param int $tid
     * @param TagRepository $tRepo
     * @param AssignRequest $r
     * @return mixed
     */
    public function assignTag(int $id, int $tid, TagRepository $tRepo, AssignRequest $r)
    {
        $sr = $this->requestRepository->findWithoutFail($id);
        if (empty($sr)) {
            return $this->sendError(__('models.request.errors.not_found'));
        }

        $tag = $tRepo->findWithoutFail($tid);
        if (empty($tag)) {
            return $this->sendError(__('models.request.errors.tag_not_found'));
        }

        $sr->tags()->sync($tag, false);
        $sr->touch();
        $sr->load([
            'media',
            'resident.user',
            'comments.user',
            'tags'
        ]);

        return $this->sendResponse($sr, __('general.attached.tag'));
    }

    /**
     * @SWG\Post(
     *      path="/requests/{id}/tags",
     *      summary="Assign many tags to the request",
     *      tags={"Request", "Tag"},
     *      description="Assign many tags to the request",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="tag_ids",
     *          description="ids of existing tags. Use or comma separated or array",
     *          type="integer",
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="tags",
     *          description="name of tags. Use or comma separated or array. If Tag name is not exists that case must be create new tag",
     *          type="integer",
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
     *                  ref="#/definitions/Request"
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
     * @param TagRepository $tRepo
     * @param AssignRequest $r
     * @return mixed
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function assignManyTags(int $id, TagRepository $tRepo, AssignRequest $r)
    {
        $sr = $this->requestRepository->findWithoutFail($id);
        if (empty($sr)) {
            return $this->sendError(__('models.request.errors.not_found'));
        }

        $tagIds = $r->tag_ids ?? [];

        if (!empty($r->tag_ids)) {
            $tagIds = is_array($r->tag_ids) ? $r->tag_ids : explode(',', $r->tag_ids);
            $tagIds = $tRepo->findWhereIn('id', $tagIds, ['id'])->pluck('id')->all();
        }

        if (!empty($r->tags)) {
            // check tag exist, if not create it and then get ids
            $tagNameList = is_array($r->tags) ? $r->tags : explode(',', $r->tags);
            $tagNameList = array_unique($tagNameList);
            $tags = $tRepo->findWhereIn('name', $tagNameList, ['id', 'name']);
            $tagIds = array_merge($tagIds, $tags->pluck('id')->all());
            $existingTags = $tags->pluck('name')->all();
            $notExistingTags = array_diff($tagNameList, $existingTags);

            foreach ($notExistingTags as $tagName) {
                $newTag = $tRepo->create(['name' => $tagName]);
                $tagIds[] = $newTag->id;
            }
        }

        if ($tagIds) {
            $sr->tags()->sync($tagIds, false);
            $sr->touch();
        }

        $sr->load([
            'media',
            'resident.user',
            'comments.user',
            'tags'
        ]);

        return $this->sendResponse($sr, __('general.attached.tag'));
    }

    /**
     * @SWG\Delete(
     *      path="/requests/{id}/tags",
     *      summary="Un assign many tags from the request",
     *      tags={"Request", "Tag"},
     *      description="Un assign many tags from the request",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="tag_ids",
     *          description="ids of existing tags. Use or comma separated or array",
     *          type="integer",
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="tags",
     *          description="name of tags. Use or comma separated or array. If Tag name is not exists that case must be create new tag",
     *          type="integer",
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
     *                  ref="#/definitions/Request"
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
     * @param TagRepository $tRepo
     * @param UnAssignRequest $r
     * @return mixed
     */
    public function unassignManyTags(int $id, TagRepository $tRepo, UnAssignRequest $r)
    {
        $sr = $this->requestRepository->findWithoutFail($id);
        if (empty($sr)) {
            return $this->sendError(__('models.request.errors.not_found'));
        }

        $tagIds = $r->tag_ids ?? [];

        if (!empty($r->tag_ids)) {
            $tagIds = is_array($r->tag_ids) ? $r->tag_ids : explode(',', $r->tag_ids);
            $tagIds = $tRepo->findWhereIn('id', $tagIds, ['id'])->pluck('id')->all();
        }

        if (!empty($r->tags)) {
            // check tag exist, if not create it and then get ids
            $tagNameList = is_array($r->tags) ? $r->tags : explode(',', $r->tags);
            $tagNameList = array_unique($tagNameList);
            $tags = $tRepo->findWhereIn('name', $tagNameList, ['id']);
            $tagIds = array_merge($tagIds, $tags->pluck('id')->all());
        }

        if ($tagIds) {
            $sr->tags()->detach($tagIds);
            $sr->touch();
        }

        $sr->load([
            'media',
            'resident.user',
            'comments.user',
            'tags'
        ]);

        return $this->sendResponse($sr, __('general.detached.tag'));
    }

    /**
     * @SWG\Delete(
     *      path="/requests/{id}/tags/{tid}",
     *      summary="Unassign single tag from the request",
     *      tags={"Request", "Tag"},
     *      description="Unassign single tag from the request",
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
     *                  ref="#/definitions/Request"
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
     * @param int $tid
     * @param TagRepository $tRepo
     * @param UnAssignRequest $r
     * @return mixed
     */
    public function unassignTag(int $id, int $tid, TagRepository $tRepo, UnAssignRequest $r)
    {
        $sr = $this->requestRepository->findWithoutFail($id);
        if (empty($sr)) {
            return $this->sendError(__('models.request.errors.not_found'));
        }

        $tag = $tRepo->findWithoutFail($tid);
        if (empty($tag)) {
            return $this->sendError(__('models.request.errors.tag_not_found'));
        }

        $sr->tags()->detach($tag);
        $sr->touch();
        $sr->load([
            'media',
            'resident.user',
            'comments.user',
            'tags'
        ]);

        return $this->sendResponse($sr, __('general.detached.tag'));
    }

    /**
     * @SWG\Get(
     *      path="/requests/{id}/assignees",
     *      summary="Get a listing of the Request assignees.",
     *      tags={"Request"},
     *      description="Get a listing of the Request assignees.",
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
     *                  @SWG\Items(ref="#/definitions/RequestAssignee")
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
     * @param ViewRequest $viewRequest
     * @return mixed
     */
    public function getAssignees(int $id, ViewRequest $viewRequest)
    {
        // @TODO permissions
        $request = $this->requestRepository->findWithoutFail($id);
        if (empty($request)) {
            return $this->sendError(__('models.request.errors.not_found'));
        }

        $type = $viewRequest->type;
        $perPage = $viewRequest->get('per_page', env('APP_PAGINATE', 10));
        return $this->getAssigneesResponse($request, $type, $perPage);
    }

    /**
     * @param int $id
     * @param MassAssignUsersRequest $massAssignUsersRequest
     * @return mixed
     * @throws \Exception
     */
    public function massAssignUsers(int $id, MassAssignUsersRequest $massAssignUsersRequest)
    {
        /** @var Request $request */
        $request = $this->requestRepository->findWithoutFail($id);
        if (empty($request)) {
            return $this->sendError(__('models.request.errors.not_found'));
        }

        $data  = $massAssignUsersRequest->data;
        $assigneeData = collect();
        $type = RequestAssignee::TypeCompetent; // for avoid error
        foreach ($data as $single) {
            $type = $single['type'] ?? RequestAssignee::TypeCompetent; // @TODO delete
            $newAssignee = $this->assignSingleUserToRequest($id, $single['user_id'], $type);
            $assigneeData->push($newAssignee);
        }

        $request->newMassAssignmentAudit($assigneeData);
        $perPage = $massAssignUsersRequest->get('per_page', env('APP_PAGINATE', 10));

        return $this->getAssigneesResponse($request, $type, $perPage);
    }

    /**
     * @param $request
     * @param $type
     * @param $perPage
     * @return mixed
     */
    protected function getAssigneesResponse($request, $type, $perPage)
    {
        $assignees = $request->assignees()
            ->when($type, function ($q) use ($type) {
                $q->where('type', $type);
            })
            ->latest()
            ->orderBy('id', 'desc')
            ->paginate($perPage);
        $assignees->load([
            'user' => function ($q) {
                $q->select('id', 'name', 'email', 'avatar')
                    ->with([
                        'service_provider:id,user_id,company_name,category,first_name,last_name',
                        'property_manager:id,user_id,type,first_name,last_name',
                        'roles:id,name'
                    ]);
            }
        ]);
        $response = (new RequestAssigneeTransformer())->transformPaginator($assignees) ;
        return $this->sendResponse($response, 'Assignees retrieved successfully');
    }

    /**
     * @param $requestId
     * @param $userId
     * @param $role
     * @return RequestAssignee|\Illuminate\Database\Eloquent\Model|mixed
     */
    protected function assignSingleUserToRequest($requestId, $userId, $type)
    {
        $user = User::find($userId);
        if (empty($user)) {
            return $this->sendError(__('models.user.errors.not_found'));
        }

        if ($user->resident) {
            return $this->sendError(__('general.invalid_operation'));
        }

        return RequestAssignee::updateOrCreate([
            'request_id' => $requestId,
            'user_id' => $userId,
            'type' => $type,
        ], [
            'created_at' => now()
        ]);
    }

    /**
     * @SWG\Delete(
     *      path="/requests-assignees/{requests_assignee_id}",
     *      summary="Unassign the provider,user or manager to the request",
     *      tags={"Request", "User", "PropertyManager", "ServiceProvider"},
     *      description="Unassign the provider,user or manager to the request",
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
     * @param UnAssignRequest $unAssignRequest
     * @return mixed
     * @throws Exception
     */
    public function deleteRequestAssignee(int $id, UnAssignRequest $unAssignRequest)
    {
        $requestAssignee = RequestAssignee::find($id);
        if (empty($requestAssignee)) {
            // @TODO fix message
            return $this->sendError(__('models.request.errors.not_found'));
        }
        $request = Request::find($requestAssignee->request_id);
        if ($request) {
            $request->touch();
        }

        $user = User::find($requestAssignee->user_id);
        $user->touch();
        $requestAssignee->delete();

        return $this->sendResponse($id, __('general.detached.user'));
    }

    /**
     * @SWG\Get(
     *      path="/requests/{id}/comunicationTemplates",
     *      summary="Display the list of Comunication templates filled with request data",
     *      tags={"Request"},
     *      description="Get CommunicationTemplates",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Request",
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
     *
     *
     *              @SWG\Property(
     *                  property="data",
     *                  ref="#/definitions/Request"
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
     * @param TemplateRepository $tempRepo
     * @param ViewRequest $viewRequest
     * @return mixed
     */
    public function getCommunicationTemplates($id, TemplateRepository $tempRepo, ViewRequest $viewRequest)
    {
        /** @var Request $request */
        $request = $this->requestRepository->findWithoutFail($id);
        if (empty($request)) {
            return $this->sendError(__('models.request.errors.not_found'));
        }

        $request->load([
            'media', 'resident.user', 'relation.unit.building',
        ]);
        $this->tmpSetUnit($request);
        $templates = $tempRepo->getParsedCommunicationTemplates($request, Auth::user());
        $response = (new TemplateTransformer)->transformCollection($templates);

        return $this->sendResponse($response, 'Communication Templates retrieved successfully');
    }

    /**
     * @SWG\Get(
     *      path="/requests/{id}/serviceComunicationTemplates",
     *      summary="Display the list of Service Comunication templates filled with request data",
     *      tags={"Request"},
     *      description="Get Service Communication Templates",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Request",
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
     *
     *
     *              @SWG\Property(
     *                  property="data",
     *                  ref="#/definitions/Request"
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
     * @param TemplateRepository $tempRepo
     * @param ViewRequest $viewRequest
     * @return mixed
     */
    public function getServiceCommunicationTemplates($id, TemplateRepository $tempRepo, ViewRequest $viewRequest)
    {
        /** @var Request $request */
        $request = $this->requestRepository->findWithoutFail($id);
        if (empty($request)) {
            return $this->sendError(__('models.request.errors.not_found'));
        }

        $request->load([
            'media', 'resident.user', 'relation.unit.building',
        ]);
        $this->tmpSetUnit($request);

        $templates = $tempRepo->getParsedServiceCommunicationTemplates($request, Auth::user());

        $response = (new TemplateTransformer)->transformCollection($templates);
        return $this->sendResponse($response, 'Service Communication Templates retrieved successfully');
    }

    /**
     * @SWG\Get(
     *      path="/requests/{id}/serviceemailTemplates",
     *      summary="Display the list of Service Email templates filled with request data",
     *      tags={"Request"},
     *      description="Get Service Email Templates",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Request",
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
     *
     *
     *              @SWG\Property(
     *                  property="data",
     *                  ref="#/definitions/Request"
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
     * @param TemplateRepository $tempRepo
     * @param ViewRequest $viewRequest
     * @return mixed
     */
    public function getServiceEmailTemplates($id, TemplateRepository $tempRepo, ViewRequest $viewRequest)
    {
        /** @var Request $request */
        $request = $this->requestRepository->findWithoutFail($id);
        if (empty($request)) {
            return $this->sendError(__('models.request.errors.not_found'));
        }

        $request->load([
            'media', 'resident.user', 'relation.unit.building',
        ]);
        $this->tmpSetUnit($request);

        $templates = $tempRepo->getParsedServiceEmailTemplates($request, Auth::user());

        $response = (new TemplateTransformer)->transformCollection($templates);
        return $this->sendResponse($response, 'Service Email Templates retrieved successfully');
    }

    protected function tmpSetUnit(Request $request)
    {
        if (!empty($request->relation->unit)) {
            $request->setRelation('unit', $request->relation->unit);
        } else {
            $request->setRelation('unit', null);
        }
    }

    /**
     * @SWG\Get(
     *      path="/requestsCounts",
     *      summary="get recuests count",
     *      tags={"Request"},
     *      description="Get request count when logged as admin and property manager",
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
     *                  type="object",
     *                  @SWG\Property(
     *                      property="all_request_count",
     *                      type="number",
     *                  ),
     *                  @SWG\Property(
     *                      property="all_unassigned_request_count",
     *                      type="number",
     *                  ),
     *                  @SWG\Property(
     *                      property="all_pending_request_count",
     *                      type="number",
     *                  ),
     *                  @SWG\Property(
     *                      property="my_request_count",
     *                      type="number",
     *                      description="This key exists when logged as property manager"
     *                  ),
     *                  @SWG\Property(
     *                      property="my_pending_request_count",
     *                      type="number",
     *                      description="This key exists when logged as property manager"
     *                  ),
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
     * @param SeeRequestsCount $seeRequestsCount
     * @return mixed
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function requestsCounts(SeeRequestsCount $seeRequestsCount)
    {
        $requestCount = $this->requestRepository->count();

        $this->requestRepository->resetCriteria();
        $this->requestRepository->doesntHave('assignees');
        $notAssignedRequestsCount = $this->requestRepository->count();

        $pendingStatues = Request::PendingStatuses;

        $this->requestRepository->resetCriteria();
        $this->requestRepository->pushCriteria(new WhereInCriteria('status', $pendingStatues));
        $allPendingCount = $this->requestRepository->count();

        $response = [
            'all_request_count' => $requestCount,
            'all_unassigned_request_count' => $notAssignedRequestsCount,
            'all_pending_request_count' => $allPendingCount
        ];

        $user = $seeRequestsCount->user();
        $this->requestRepository->resetCriteria();
        $this->requestRepository->whereHas('users', function ($q) use ($user) {
            $q->where('user_id', $user->id);
        });
        $response['my_request_count'] = $this->requestRepository->count();

        $this->requestRepository->resetCriteria();
        $this->requestRepository->whereHas('users', function ($q) use ($user) {
            $q->where('user_id', $user->id);
        });
        $this->requestRepository->pushCriteria(new WhereInCriteria('status', Request::PendingStatuses));
        $response['my_pending_request_count'] = $this->requestRepository->count();
        return $this->sendResponse($response, 'Request countes');
    }

    /**
     * @param Request $request
     * @return string
     */
    protected function getPdfName(Request $request)
    {
        $settings = $this->settingsRepository->first();
        $request->setDownloadPdf($settings);
        $pdfName = $request->pdfFileName();

        return $pdfName ;
    }

    /**
     * @param DownloadPdfRequest $r
     * @param $id
     * @return mixed
     */
    public function downloadPdf(DownloadPdfRequest $r, $id){

        $r = $this->requestRepository->findWithoutFail($id);

        if (empty($r)) {
            return $this->sendError(__('models.request.errors.not_found'));
        }

        $pdfName = $this->getPdfName($r);
        if (!\Storage::disk('request_downloads')->exists($pdfName)) {
            return $this->sendError('Request file not found!');
        }
        
        return \Storage::disk('request_downloads')->download($pdfName, $pdfName);
    }

}
