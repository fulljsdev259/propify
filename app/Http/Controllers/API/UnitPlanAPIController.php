<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\AppBaseController;
use App\Http\Requests\API\UnitPlan\UpdateRequest;
use App\Http\Requests\API\UnitPlan\CreateRequest;
use App\Repositories\UnitPlanRepository;
use App\Repositories\UnitRepository;
use App\Transformers\UnitPlanTransformer;

class UnitPlanAPIController extends AppBaseController
{
    /**
     * @var UnitPlanRepository
     */
    private $unitPlanRepository;

    /**
     * @var UnitRepository
     */
    private $unitRepository;

    public function __construct(UnitPlanRepository $unitPlanRepository, UnitRepository $unitRepository)
    {
        $this->unitPlanRepository = $unitPlanRepository;
        $this->unitRepository = $unitRepository;
    }

    /**
     * @SWG\Get(
     *     path="/units/{unitId}/plans",
     *     summary="Display the list of Unit's plans",
     *     tags={"UnitPlan"},
     *     description="Get all Unit's plans",
     *     produces={"application/json"},
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
     *                  @SWG\Items(ref="#/definitions/UnitPlan")
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     *
     * Display a listing of the resource.
     *
     * @param $unitId
     * @return \Illuminate\Http\Response
     */
    public function index($unitId)
    {
        $unitPlans = $this
            ->unitRepository
            ->findWithoutFail($unitId)
            ->plans
            ->load('media');

        $response = (new UnitPlanTransformer)->transformCollection($unitPlans);

        return $this->sendResponse($response, "Unit's plans retrieved successfully");
    }

    /**
     * @SWG\Post(
     *      path="/units/{unitId}/plans",
     *      summary="Store a newly created Unit Plan in storage",
     *      tags={"UnitPlan"},
     *      description="Store Unit Plan",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="Unit Plan that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/UnitPlan")
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
     *                  ref="#/definitions/UnitPlan"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     *
     * Store a newly created resource in storage.
     *
     * @param CreateRequest $request
     * @param int $unitId
     * @return \Illuminate\Http\Response
     */
    public function store(CreateRequest $request, int $unitId)
    {
        $request['unit_id'] = $unitId;

        try {
            $unitPlan = $this
                ->unitPlanRepository
                ->create($request->all());
        } catch (\Exception $e) {
            return $this->sendError(__('models.unit_plans.errors.create') . $e->getMessage());
        }

        $unitPlan->load('media');

        $response = (new UnitPlanTransformer)->transform($unitPlan);

        return $this->sendResponse($response, __('models.unit_plans.saved'));
    }

    /**
     * @SWG\Get(
     *      path="/units/{unitId}/plans/{planId}",
     *      summary="Display the specified Unit Plan based on a Unit",
     *      tags={"UnitPlan"},
     *      description="Get Unit Plan based on a Unit",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="unitId",
     *          description="id of Unit",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="planId",
     *          description="id of Plan",
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
     *                  ref="#/definitions/UnitPlan"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     *
     * Display the specified resource.
     *
     * @param int $unitId
     * @param int $planId
     * @return \Illuminate\Http\Response
     */
    public function show(int $unitId, int $planId)
    {
        $unitPlan = $this
            ->unitPlanRepository
            ->with('media')
            ->findWithoutFail($planId);

        if (empty($unitPlan)) {
            return $this->sendError(__('models.unit_plans.errors.not_found'));
        }

        $response = (new UnitPlanTransformer)->transform($unitPlan);

        return $this->sendResponse($response, "Unit's plan retrieved successfully");
    }

    /**
     * @SWG\Put(
     *      path="/units/{unitId}/plans/{planId}",
     *      summary="Update the specified Unit Plan in storage",
     *      tags={"UnitPlan"},
     *      description="Update Unit Plan",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="unitId",
     *          description="id of Unit",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="planId",
     *          description="id of Plan",
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
     *                  ref="#/definitions/UnitPlan"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     *
     * Update the specified resource in storage.
     *
     * @param UpdateRequest $request
     * @param int $unitId
     * @param int $planId
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateRequest $request, int $unitId, int $planId)
    {
        $request['unit_id'] = $unitId;

        $unitPlan = $this
            ->unitPlanRepository
            ->findWithoutFail($planId);

        if (empty($unitPlan)) {
            return $this->sendError(__('models.unit_plans.errors.not_found'));
        }

        try {
            $this
                ->unitPlanRepository
                ->updateExisting($unitPlan, $request->all());
        } catch (\Exception $e) {
            return $this->sendError(__('models.unit_plans.errors.update') . $e->getMessage());
        }

        $unitPlan->load('media');

        $response = (new UnitPlanTransformer)->transform($unitPlan);

        return $this->sendResponse($response, __('models.unit_plans.saved'));
    }

    /**
     * @SWG\Delete(
     *      path="/units/{unitId}/plans/{planId}",
     *      summary="Remove the specified Unit Plan from storage",
     *      tags={"UnitPlan"},
     *      description="Delete Unit Plan",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="unitId",
     *          description="id of Unit",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="planId",
     *          description="id of Plan",
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
     * Remove the specified resource from storage.
     *
     * @param int $unitId
     * @param int $planId
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function destroy(int $unitId, int $planId)
    {
        $unitPlan = $this
            ->unitPlanRepository
            ->findWithoutFail($planId);

        if (empty($unitPlan)) {
            return $this->sendError(__('models.unit_plans.errors.not_found'));
        }

        $media = $unitPlan->media();
        if (empty($media)) {
            return $this->sendError(__('general.media_not_found'));
        }
        $media->delete();

        $unitPlan->delete();

        return $this->sendResponse($planId, __('models.unit_plans.deleted'));
    }
}
