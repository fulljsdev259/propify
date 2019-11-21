<?php

namespace App\Http\Controllers\API;

use App\Criteria\Common\RequestCriteria;
use App\Criteria\Workflow\FilterByQuarterCriteria;
use App\Http\Controllers\AppBaseController;
use App\Http\Requests\API\Workflow\CreateRequest;
use App\Http\Requests\API\Workflow\DeleteRequest;
use App\Http\Requests\API\Workflow\ListRequest;
use App\Http\Requests\API\Workflow\UpdateRequest;
use App\Http\Requests\API\Workflow\ViewRequest;
use App\Models\Workflow;
use App\Repositories\WorkflowRepository;
use App\Transformers\WorkflowTransformer;
use Illuminate\Http\Response;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;

/**
 * Class WorkflowController
 * @package App\Http\Controllers\API
 */
class WorkflowAPIController extends AppBaseController
{
    /** @var  WorkflowRepository */
    private $workflowRepository;

    /**
     * WorkflowAPIController constructor.
     * @param WorkflowRepository $workflowRepository
     */
    public function __construct(WorkflowRepository $workflowRepository) {
        $this->workflowRepository = $workflowRepository;
    }

    /**
     * @SWG\Get(
     *      path="/workflows",
     *      summary="Get a listing of the Workflows.",
     *      tags={"Workflow"},
     *      description="Get all Workflows",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="quarter_id",
     *          in="query",
     *          type="integer",
     *          description="filter Workflow by quarter",
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
     *                  type="array",
     *                  @SWG\Items(ref="#/definitions/Workflow")
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
        $this->workflowRepository->pushCriteria(new RequestCriteria($request));
        $this->workflowRepository->pushCriteria(new FilterByQuarterCriteria($request));
        $this->workflowRepository->pushCriteria(new LimitOffsetCriteria($request));

        $getAll = $request->get('get_all', false);
        if ($getAll) {
            $workflows = $this->workflowRepository->get();
            $response = (new WorkflowTransformer())->transformCollection($workflows);
            return $this->sendResponse($response, 'Workflows retrieved successfully');
        }

        $perPage = $request->get('per_page', env('APP_PAGINATE', 10));

        $workflows = $this->workflowRepository->paginate($perPage);
        $response = (new WorkflowTransformer)->transformPaginator($workflows);
        return $this->sendResponse($response, 'Workflows retrieved successfully');
    }

    /**
     * @SWG\Post(
     *      path="/workflows",
     *      summary="Store a newly created Workflow in storage",
     *      tags={"Workflow"},
     *      description="Store Workflow",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="Workflow that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/Workflow")
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
     *                  ref="#/definitions/Workflow"
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
     */
    public function store(CreateRequest $request)
    {
        $input = $request->all();

        try {
            $workflow = $this->workflowRepository->create($input);
        } catch (\Exception $e) {
            return $this->sendError(__('models.quarter.workflow.errors.create') . $e->getMessage());
        }

        $workflow->load(['quarter']);
        $response = (new WorkflowTransformer)->transform($workflow);
        return $this->sendResponse($response, __('models.quarter.workflow.saved'));
    }

    /**
     * @SWG\Get(
     *      path="/workflows/{id}",
     *      summary="Display the specified Workflow",
     *      tags={"Workflow"},
     *      description="Get Workflow",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Workflow",
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
     *                  ref="#/definitions/Workflow"
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
        /** @var Workflow $workflow */
        $workflow = $this->workflowRepository->findWithoutFail($id);
        if (empty($workflow)) {
            return $this->sendError(__('models.quarter.workflow.errors.not_found'));
        }

        $workflow->load(['quarter']);
        $response = (new WorkflowTransformer)->transform($workflow);
        return $this->sendResponse($response, 'Workflow retrieved successfully');
    }

    /**
     * @SWG\Put(
     *      path="/workflows/{id}",
     *      summary="Update the specified Workflow in storage",
     *      tags={"Workflow"},
     *      description="Update Workflow",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Workflow",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="Workflow that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/Workflow")
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
     *                  ref="#/definitions/Workflow"
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
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function update($id, UpdateRequest $request)
    {
        $input = $request->all();
        /** @var Workflow $workflow */
        $workflow = $this->workflowRepository->findWithoutFail($id);
        if (empty($workflow)) {
            return $this->sendError(__('models.quarter.workflow.errors.not_found'));
        }

        try {
            $workflow = $this->workflowRepository->update($input, $id);
        } catch (\Exception $e) {
            return $this->sendError(__('models.quarter.workflow.errors.update') . $e->getMessage());
        }

        $workflow->load(['quarter']);
        $response = (new WorkflowTransformer)->transform($workflow);
        return $this->sendResponse($response, __('models.quarter.workflow.saved'));
    }

    /**
     * @SWG\Delete(
     *      path="/workflows/{id}",
     *      summary="Remove the specified Workflow from storage",
     *      tags={"Workflow"},
     *      description="Delete Workflow",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Workflow",
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
        /** @var Workflow $workflow */
        $workflow = $this->workflowRepository->findWithoutFail($id);

        if (empty($workflow)) {
            return $this->sendError(__('models.quarter.workflow.errors.not_found'));
        }

        $workflow->delete();

        return $this->sendResponse($id, __('models.quarter.workflow.deleted'));
    }
}
