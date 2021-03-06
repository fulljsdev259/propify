<?php

namespace App\Http\Controllers\API;

use App\Criteria\Common\RequestCriteria;
use App\Criteria\Common\WhereCriteria;
use App\Criteria\Unit\FilterByCityCriteria;
use App\Criteria\Unit\FilterByQuarterCriteria;
use App\Criteria\Unit\FilterByRelatedFieldsCriteria;
use App\Criteria\Unit\FilterByTypeCriteria;
use App\Criteria\Unit\FilterByUserRoleCriteria;
use App\Criteria\Unit\IncludeForOrderCriteria;
use App\Http\Controllers\AppBaseController;
use App\Http\Requests\API\Unit\AssignRequest;
use App\Http\Requests\API\Unit\CreateRequest;
use App\Http\Requests\API\Unit\UnAssignRequest;
use App\Http\Requests\API\Unit\DeleteRequest;
use App\Http\Requests\API\Unit\ListRequest;
use App\Http\Requests\API\Unit\UpdateRequest;
use App\Http\Requests\API\Unit\ViewRequest;
use App\Models\Building;
use App\Models\Unit;
use App\Repositories\RelationRepository;
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
    private $relationRepository;

    /**
     * UnitAPIController constructor.
     * @param UnitRepository $unitRepository
     * @param ResidentRepository $residentRepository
     * @param RelationRepository $relationRepository
     */
    public function __construct(
        UnitRepository $unitRepository,
        ResidentRepository $residentRepository,
        RelationRepository $relationRepository
    )
    {
        $this->unitRepository = $unitRepository;
        $this->residentRepository = $residentRepository;
        $this->relationRepository = $relationRepository;
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
        if ($request->orderBy == 'internal_quarter_id') {
            $request->merge([
                'orderBy' => RequestCriteria::NoOrder,
                'orderByRaw' => 'internal_quarter_id',
            ]);
        }
        if ($request->orderBy == 'status') {
            $request->merge([
                'orderBy' => RequestCriteria::NoOrder,
                'orderByRaw' => 'status',
            ]);
        }
        $this->unitRepository->pushCriteria(new RequestCriteria($request));
        $this->unitRepository->pushCriteria(new LimitOffsetCriteria($request));
        $this->unitRepository->pushCriteria(new FilterByRelatedFieldsCriteria($request));
        $this->unitRepository->pushCriteria(new FilterByQuarterCriteria($request));
        $this->unitRepository->pushCriteria(new FilterByTypeCriteria($request));
        $this->unitRepository->pushCriteria(new FilterByCityCriteria($request));
        $this->unitRepository->pushCriteria(new FilterByUserRoleCriteria($request));
        $this->unitRepository->pushCriteria(new IncludeForOrderCriteria($request));

        if ($request->group_by_building) {
            $units = $this->unitRepository->with('building.address')->get();
            $units = $units->sortBy(function($item) {
                $houseNumber = $item->building->address->house_num ?? '';
                $sort = !empty($houseNumber)
                    ? '0-' . str_fill_to($houseNumber, 50):
                    '1-';
                $sort .= sprintf('%-%s', str_fill_to($item->type, 10), $item->unit_format);
                return $sort;
            })->groupBy(function ($item) {
                return $item->building->address->house_num ?? 'Quarter'; // @TODO is need use translations
            })->toArray();
            return $this->sendResponse($units, 'Units retrieved successfully');
        }

        if ($request->group_by_floor) {
            $units = $this->unitRepository->get();
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
            return $this->sendResponse($units->toArray(), 'Units retrieved successfully');
        }

        $perPage = $request->get('per_page', env('APP_PAGINATE', 10));

        $units = $this->unitRepository->with([
                'building',
                'quarter',
                'media',
                'relations' => function ($q) {
                    $q->with('unit', 'resident.user');
                },
            ])
            ->scope('allRequestStatusCount')
            ->paginate($perPage);
        $response = (new UnitTransformer)->transformPaginator($units, 'transformForIndex');
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
     * @param PinboardRepository $pinboardRepository
     * @return mixed
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function store(CreateRequest $request, PinboardRepository $pinboardRepository)
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

        $unit->load([
            'building.address',
            'quarter',
            'relations' => function ($q) {
                $q->with('unit', 'resident.user');
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
     * @param $id
     * @param ViewRequest $r
     * @return mixed
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function show($id, ViewRequest $r)
    {
        /** @var Unit $unit */
        $unit = $this->unitRepository->findWithoutFail($id);
        if (empty($unit)) {
            return $this->sendError(__('models.unit.errors.not_found'));
        }

        $unit->load([
            'building.address',
            'quarter',
            'relations' => function ($q) {
                $q->with('unit', 'resident.user');
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
     * @param PinboardRepository $pinboardRepository
     * @return mixed
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function update($id, UpdateRequest $request, PinboardRepository $pinboardRepository)
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

        try {
            $unit = $this->unitRepository->update($input, $id);
        } catch (\Exception $e) {
            return $this->sendError(__('models.unit.errors.update') . $e->getMessage());
        }

        $unit->load([
            'building.address',
            'quarter',
            'relations' => function ($q) {
                $q->with('unit', 'resident.user');
            },
            'media'
        ]);

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
}
