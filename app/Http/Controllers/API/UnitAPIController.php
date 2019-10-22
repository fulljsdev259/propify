<?php

namespace App\Http\Controllers\API;

use App\Criteria\Common\RequestCriteria;
use App\Criteria\Common\WhereCriteria;
use App\Criteria\Unit\FilterByRelatedFieldsCriteria;
use App\Criteria\Unit\FilterByTypeCriteria;
use App\Http\Controllers\AppBaseController;
use App\Http\Requests\API\Unit\AssignRequest;
use App\Http\Requests\API\Unit\CreateRequest;
use App\Http\Requests\API\Unit\UnAssignRequest;
use App\Http\Requests\API\Unit\DeleteRequest;
use App\Http\Requests\API\Unit\ListRequest;
use App\Http\Requests\API\Unit\UpdateRequest;
use App\Http\Requests\API\Unit\ViewRequest;
use App\Models\Building;
use App\Models\Contract;
use App\Models\Unit;
use App\Repositories\ContractRepository;
use App\Repositories\PinboardRepository;
use App\Repositories\ResidentRepository;
use App\Repositories\UnitRepository;
use App\Transformers\UnitTransformer;
use Illuminate\Http\Response;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;

/**
 * Class UnitController
 * @package App\Http\Controllers\API
 */
class UnitAPIController extends AppBaseController
{
    /** @var  UnitRepository */
    private $unitRepository;

    /** @var  ResidentRepository */
    private $residentRepository;

    /** @var  ResidentRepository */
    private $contractRepository;

    /**
     * UnitAPIController constructor.
     * @param UnitRepository $unitRepository
     * @param ResidentRepository $residentRepository
     * @param ContractRepository $contractRepository
     */
    public function __construct(
        UnitRepository $unitRepository,
        ResidentRepository $residentRepository,
        ContractRepository $contractRepository
    )
    {
        $this->unitRepository = $unitRepository;
        $this->residentRepository = $residentRepository;
        $this->contractRepository = $contractRepository;
    }

    /**
     * @SWG\Get(
     *      path="/units",
     *      summary="Get a listing of the Units.",
     *      tags={"Unit"},
     *      description="Get all Units",
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
     *                  @SWG\Items(ref="#/definitions/Unit")
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
        $this->unitRepository->pushCriteria(new RequestCriteria($request));
        $this->unitRepository->pushCriteria(new FilterByRelatedFieldsCriteria($request));
        $this->unitRepository->pushCriteria(new FilterByTypeCriteria($request));
        $this->unitRepository->pushCriteria(new LimitOffsetCriteria($request));

        if ($request->show_contract_counts) {
            $this->unitRepository->withCount([
                'contracts as total_contracts_count',
                'contracts as active_contracts_count' => function ($q) {
                    $q->where('status', Contract::StatusActive);
                }
            ]);
        }

        if ($request->group_by_floor) {
            $units = $this->unitRepository->get();
            if ($request->show_contract_counts) {
                $units->each(function ($unit) {
                    $unit->inactive_contracts_count = $unit->total_contracts_count - $unit->active_contracts_count;
                });
            }
            $units = $units->sortBy('name')->groupBy('floor')->sortKeys()->toArray();
            if ($request->building_id) {
                $buildingHasAttic = Building::whereKey($request->building_id)->value('attic');
                if ($buildingHasAttic) {
                    $atticUnits = array_pop($units);
                    $units['attic'] = $atticUnits;
                }
                // @TODO maybe change attic logic by unit attic filed
            }

            return $this->sendResponse($units, 'Units retrieved successfully');
        }

        $getAll = $request->get('get_all', false);
        if ($getAll) {

            $units = $this->unitRepository->get();
            if ($request->show_contract_counts) {
                $units->each(function ($unit) {
                    $unit->inactive_contracts_count = $unit->total_contracts_count - $unit->active_contracts_count;
                });
            }

            return $this->sendResponse($units->toArray(), 'Units retrieved successfully');
        }

        $perPage = $request->get('per_page', env('APP_PAGINATE', 10));

        $units = $this->unitRepository->with([
            'building',
            'media',
            'contracts' => function ($q) {
                $q->with('building.address', 'unit', 'resident.user');
            },
        ])->paginate($perPage);

        $response = (new UnitTransformer)->transformPaginator($units);
        return $this->sendResponse($response, 'Units retrieved successfully');
    }

    /**
     * @SWG\Post(
     *      path="/units",
     *      summary="Store a newly created Unit in storage",
     *      tags={"Unit"},
     *      description="Store Unit",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="Unit that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/Unit")
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
     *                  ref="#/definitions/Unit"
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
     * @param PinboardRepository $pr
     * @return Response
     */
    public function store(CreateRequest $request, PinboardRepository $pr)
    {
        $input = $request->all();
        $input['sq_meter'] = $input['sq_meter'] ?? 0;
        if (isset($input['monthly_rent'])) {
            $input['monthly_gross_rent'] = $input['monthly_net_rent'] ?? $input['monthly_rent'];
            $input['monthly_gross_rent'] = $input['monthly_net_rent'] ?? $input['monthly_rent'];
        }
        try {
            $unit = $this->unitRepository->create($input);
        } catch (\Exception $e) {
            return $this->sendError(__('models.unit.errors.create') . $e->getMessage());
        }

        if (isset($input['resident_id'])) {
            try {
                $contract = $this->contractRepository->newContractForUnit($unit, $input['resident_id']);
                $resident = $this->residentRepository->find($input['resident_id']);
                $pr->newResidentPinboard($resident); // @TODO use $resident or @contract
            } catch (\Exception $e) {
                return $this->sendError(__('models.unit.errors.resident_assign') . $e->getMessage());
            }
        }

        $unit->load([
            'building',
            'contracts' => function ($q) {
                $q->with('building.address', 'unit', 'resident.user');
            },
            'media'
        ]);
        $response = (new UnitTransformer)->transform($unit);
        return $this->sendResponse($response, __('models.unit.saved'));
    }

    /**
     * @SWG\Get(
     *      path="/units/{id}",
     *      summary="Display the specified Unit",
     *      tags={"Unit"},
     *      description="Get Unit",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Unit",
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
     *                  ref="#/definitions/Unit"
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
     * @param ViewRequest $r
     * @return Response
     */
    public function show($id, ViewRequest $r)
    {
        /** @var Unit $unit */
        $unit = $this->unitRepository->findWithoutFail($id);
        if (empty($unit)) {
            return $this->sendError(__('models.unit.errors.not_found'));
        }

        $unit->load([
            'building',
            'contracts' => function ($q) {
                $q->with('building.address', 'unit', 'resident.user');
            },
            'media'
        ]);
        $response = (new UnitTransformer)->transform($unit);
        return $this->sendResponse($response, 'Unit retrieved successfully');
    }

    /**
     * @SWG\Put(
     *      path="/units/{id}",
     *      summary="Update the specified Unit in storage",
     *      tags={"Unit"},
     *      description="Update Unit",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Unit",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="Unit that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/Unit")
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
     *                  ref="#/definitions/Unit"
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
     * @param PinboardRepository $pr
     * @return mixed
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function update($id, UpdateRequest $request, PinboardRepository $pr)
    {
        $input = $request->all();
        if (isset($input['monthly_rent'])) {
            $input['monthly_gross_rent'] = $input['monthly_gross_rent'] ?? $input['monthly_rent'];
            $input['monthly_net_rent'] = $input['monthly_net_rent'] ?? $input['monthly_rent'];
        }
        /** @var Unit $unit */
        $unit = $this->unitRepository->findWithoutFail($id);
        if (empty($unit)) {
            return $this->sendError(__('models.unit.errors.not_found'));
        }

//        $shouldPinboard = isset($input['resident_id']) &&
//            (!$unit->resident || ($unit->resident && $unit->resident->id != $input['resident_id']));
        $shouldPinboard = false; // @TODO correct contract related

        try {
            $unit = $this->unitRepository->update($input, $id);
        } catch (\Exception $e) {
            return $this->sendError(__('models.unit.errors.update') . $e->getMessage());
        }

//        $currentResident = $unit->resident ? $unit->resident->id : 0;
        $currentResident = 0; // @TODO correct contract related
        if (isset($input['resident_id']) && $input['resident_id'] != $currentResident) {
            try {
                $contract = Contract::where('unit_id', $unit->id)->where('resident_id', $input['resident_id'])->delete(); // @TODO delete single or many
                $this->contractRepository->newContractForUnit($unit, $input['resident_id']);
            } catch (\Exception $e) {
                return $this->sendError(__('models.unit.errors.create') . $e->getMessage());
            }
        }

        $unit->load([
            'building',
            'contracts' => function ($q) {
                $q->with('building.address', 'unit', 'resident.user');
            },
            'media'
        ]);
        if ($shouldPinboard) {
            $resident = $this->residentRepository->find($input['resident_id']);
            $pr->newResidentPinboard($resident); // @TODO use $resident or new created contract
        }

        $response = (new UnitTransformer)->transform($unit);
        return $this->sendResponse($response, __('models.unit.saved'));
    }

    /**
     * @SWG\Delete(
     *      path="/units/{id}",
     *      summary="Remove the specified Unit from storage",
     *      tags={"Unit"},
     *      description="Delete Unit",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Unit",
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
        /** @var Unit $unit */
        $unit = $this->unitRepository->findWithoutFail($id);

        if (empty($unit)) {
            return $this->sendError(__('models.unit.errors.not_found'));
        }

        // TODO: unassign Resident from deleted Unit
        $unit->delete();

        return $this->sendResponse($id, __('models.unit.deleted'));
    }

    /**
     * @param DeleteRequest $request
     * @return mixed
     */
    public function destroyWithIds(DeleteRequest $request){
        $ids = $request->get('ids');
        try{
            Unit::destroy($ids);      
        }
        catch (\Exception $e) {
            return $this->sendError(__('models.unit.errors.deleted') . $e->getMessage());
        }
        return $this->sendResponse($ids, __('models.unit.deleted'));
    }

    /**
     * @SWG\Post(
     *      path="/units/{id}/assignees/{assignee_id}",
     *      summary="Assign the resident to unit",
     *      tags={"Unit", "Resident"},
     *      description="Assign the resident to unit",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          in="path",
     *          required=true,
     *          description="unit id",
     *          type="integer",
     *      ),
     *      @SWG\Parameter(
     *          name="assignee_id",
     *          in="path",
     *          required=true,
     *          description="resident id",
     *          type="integer",
     *      ),
     *      @SWG\Response(
     *          response=404,
     *          description="not found",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean",
     *                  example="false"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string",
     *                  example="Incorrect resident"
     *              )
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
     *                  type="integer"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string",
     *                  example="resident assigned unit successfully"
     *              )
     *          )
     *      )
     * )
     *
     * @param $unitId
     * @param $residentId
     * @param AssignRequest $r
     * @return mixed
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function assignResident($unitId, $residentId, AssignRequest $r)
    {
        $unit = $this->unitRepository->find($unitId, ['id']);
        if (empty($unit)) {
            return $this->sendError(__('models.unit.errors.not_found'));
        }

        $resident = $this->residentRepository->find($residentId, ['id']);
        if (empty($resident)) {
            return $this->sendError(__('models.unit.errors.not_found'));
        }

        $contract = $this->contractRepository->newContractForUnit($unit, $resident);

        // @TODO need newResidentPinboard
        return $this->sendResponse($unitId, __('models.unit.resident_assigned'));
    }

    /**
     * @SWG\Delete(
     *      path="/units/{id}/assignees/{assignee_id}",
     *      summary="Un assign the resident to unit",
     *      tags={"Unit", "Resident"},
     *      description="Un assign the resident to unit",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          in="path",
     *          required=true,
     *          description="unit id",
     *          type="integer",
     *      ),
     *      @SWG\Parameter(
     *          name="assignee_id",
     *          in="path",
     *          required=true,
     *          description="resident id",
     *          type="integer",
     *      ),
     *      @SWG\Response(
     *          response=404,
     *          description="not found",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean",
     *                  example="false"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string",
     *                  example="Incorrect resident"
     *              )
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
     *                  type="integer"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string",
     *                  example="resident un assigned unit successfully"
     *              )
     *          )
     *      )
     * )
     *
     * @param $unitId
     * @param $residentId
     * @param UnAssignRequest $r$residentId
     * @return mixed
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function unassignResident($unitId, $residentId, UnAssignRequest $r)
    {
        $this->contractRepository->pushCriteria(new WhereCriteria('unit_id', $unitId));
        $this->contractRepository->pushCriteria(new WhereCriteria('resident_id', $residentId));

        $contracts = $this->contractRepository->get(['id']);
        if ($contracts->isEmpty()) {
            return $this->sendError(__('models.unit.errors.resident_not_assign'));
        }

        // @TODO delete single contract or all
        foreach ($contracts as $contract) {
            $contract->delete();
        }

        return $this->sendResponse($unitId, __('models.unit.resident_unassigned'));
    }
}
