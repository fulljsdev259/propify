<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\AppBaseController;
use App\Repositories\UnitPlanRepository;
use App\Repositories\UnitRepository;
use App\Transformers\UnitPlanTransformer;
use Illuminate\Http\Request;

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
