<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\AppBaseController;
use App\Repositories\UnitPlanRepository;
use App\Repositories\UnitRepository;
use App\Transformers\UnitPlanTransformer;
use Illuminate\Http\Request;
use Swagger\Annotations as SWG;

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
     *     path="/units/{unitId}/plans"
     *     summary="Display the list of Unit's plans"
     *     tags={Unit_Plan}
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
     *                  @SWG\Items(ref="#/definitions/Unit_Plans")
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
     *      tags={"Unit_Plan"},
     *      description="Store Unit Plan",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="Unit Plan that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/Unit_Plans")
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
     *                  ref="#/definitions/Unit_Plans"
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
     * @param  \Illuminate\Http\Request $request
     * @param int $unitId
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, int $unitId)
    {
        $request['unit_id'] = $unitId;

        try {
            $unitPlan = $this
                ->unitPlanRepository
                ->create($request->all());
        } catch (\Exception $e) {
            return $this->sendError(__('models.unit_plans.errors.create') . $e->getMessage());
        }

        if ($request->has('media')) {
            $unitPlan
                ->addMedia($request['media'])
                ->toMediaCollection('unit_plan', 'units_plans');
        }

        $unitPlan->load('media');

        $response = (new UnitPlanTransformer)->transform($unitPlan);

        return $this->sendResponse($response, __('models.unit_plans.saved'));
    }

    /**
     * @SWG\Get(
     *      path="/units/{unitId}/plans/{planId}",
     *      summary="Display the specified Unit Plan based on a Unit",
     *      tags={"Unit_Plan"},
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
     *                  ref="#/definitions/Unit_Plans"
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
            ->findWithoutFail($planId)
            ->load(['media']);

        $response = (new UnitPlanTransformer)->transform($unitPlan);

        return $this->sendResponse($response, "Unit's plan retrieved successfully");
    }

    /**
     * @SWG\Put(
     *      path="/units/{unitId}/plans/{planId}",
     *      summary="Update the specified Unit Plan in storage",
     *      tags={"Unit_Plan"},
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
     *                  ref="#/definitions/Unit_Plans"
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
     * @param  \Illuminate\Http\Request $request
     * @param int $unitId
     * @param int $planId
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, int $unitId, int $planId)
    {
        $request['unit_id'] = $unitId;

        $unitPlan = $this
            ->unitPlanRepository
            ->findWithoutFail($planId);

        try {
            $unitPlan->update($request->all());
        } catch (\Exception $e) {
            return $this->sendError(__('models.unit_plans.errors.update') . $e->getMessage());
        }

        if ($request->has('media')) {
            $unitPlan
                ->addMedia($request['media'])
                ->toMediaCollection('unit_plan', 'units_plans');
        }

        $unitPlan->load('media');

        $response = (new UnitPlanTransformer)->transform($unitPlan);

        return $this->sendResponse($response, __('models.unit_plans.saved'));
    }

    /**
     * @SWG\Delete(
     *      path="/units/{unitId}/plans/{planId}",
     *      summary="Remove the specified Unit Plan from storage",
     *      tags={"Unit_Plan"},
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
     */
    public function destroy(int $unitId, int $planId)
    {
        $this
            ->unitPlanRepository
            ->deleteWhere([
                'unit_id' => $unitId,
                'id' => $planId,
            ]);

        return $this->sendResponse($planId, __('models.unit_plans.deleted'));
    }
}
