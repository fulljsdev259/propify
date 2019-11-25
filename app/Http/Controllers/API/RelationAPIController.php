<?php

namespace App\Http\Controllers\API;

use App\Criteria\Common\RequestCriteria;
use App\Criteria\Pinboard\FilterByResidentCriteria;
use App\Criteria\Relation\FilterByBuildingCriteria;
use App\Criteria\Relation\FilterByUnitCriteria;
use App\Http\Controllers\AppBaseController;
use App\Http\Requests\API\Relation\ShowRequest;
use App\Http\Requests\API\Relation\DeleteRequest;
use App\Http\Requests\API\Relation\UpdateRequest;
use App\Http\Requests\API\Relation\CreateRequest;
use App\Http\Requests\API\Relation\ListRequest;
use App\Models\Relation;
use App\Repositories\RelationRepository;
use App\Repositories\PinboardRepository;
use App\Transformers\RelationTransformer;
use Illuminate\Http\Response;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;

/**
 * Class RelationController
 * @package App\Http\Controllers\API
 */
class RelationAPIController extends AppBaseController
{
    /** @var  RelationRepository */
    private $relationRepository;

    /**
     * RelationAPIController constructor.
     * @param RelationRepository $relationRepo
     */
    public function __construct(RelationRepository $relationRepo)
    {
        $this->relationRepository = $relationRepo;
    }

    /**
     * @SWG\Get(
     *      path="/relations",
     *      summary="Get a listing of the Relations.",
     *      tags={"Relation"},
     *      description="Get all Relations",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="quarter_id",
     *          in="query",
     *          type="integer",
     *          description="fuilter by quarter",
     *          required=false,
     *      ),
     *     @SWG\Parameter(
     *          name="resident_id",
     *          type="integer",
     *          in="query",
     *          description="fuilter by resident",
     *          required=false,
     *      ),
     *     @SWG\Parameter(
     *          name="unit_id",
     *          type="integer",
     *          in="query",
     *          description="fuilter by unit",
     *          required=false,
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
     *                  @SWG\Items(ref="#/definitions/Relation")
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
        $request->merge([
            'model' => (new Relation)->getTable(),
        ]);

        $this->relationRepository->pushCriteria(new RequestCriteria($request));
        $this->relationRepository->pushCriteria(new LimitOffsetCriteria($request));
        $this->relationRepository->pushCriteria(new FilterByBuildingCriteria($request));
        $this->relationRepository->pushCriteria(new FilterByUnitCriteria($request));
        $this->relationRepository->pushCriteria(new FilterByResidentCriteria($request));

        $getAll = $request->get('get_all', false);
        if ($getAll) {
            $request->merge(['limit' => env('APP_PAGINATE', 10)]);
            $this->relationRepository->pushCriteria(new LimitOffsetCriteria($request));
            $relations = $this->relationRepository->get();
            $response = (new RelationTransformer())->transformCollection($relations);
            return $this->sendResponse($response, 'Relations retrieved successfully');
        }

        $perPage = $request->get('per_page', env('APP_PAGINATE', 10));
        // @TODO RELATION is need? building, address, unit . I think not need because many
        $relations = $this->relationRepository->with([
            'resident.user',
            'quarter.address',
            'unit.building.address',
            'media',
        ])->paginate($perPage);
        $response = (new RelationTransformer())->transformPaginator($relations);
        return $this->sendResponse($response, 'Relations retrieved successfully');
    }

    /**
     * @SWG\Get(
     *      path="/relations/{id}",
     *      summary="Display the specified Resident Relation",
     *      tags={"Relation"},
     *      description="Get Resident Relation",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Resident Relation",
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
     *                  ref="#/definitions/Relation"
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
     * @param ShowRequest $request
     * @return mixed
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function show($id, ShowRequest $request)
    {
        /** @var Relation $relation */
        $relation = $this->relationRepository->findWithoutFail($id);
        if (empty($relation)) {
            return $this->sendError(__('models.resident.relation.errors.not_found'));
        }

        $relation->load([
            'resident.user',
            'quarter.address',
            'unit.building.address',
            'unit',
            'media',
//            'garant_residents:residents.id,residents.first_name,residents.last_name'
        ]);
        $response = (new RelationTransformer())->transform($relation);
        return $this->sendResponse($response, 'Resident Relation retrieved successfully');
    }

    /**
     * @SWG\Post(
     *      path="/relations",
     *      summary="Store a newly created Resident renat Relation in storage",
     *      tags={"Relation"},
     *      description="Store Resident Relation",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="Resident that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/Relation")
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
     *                  ref="#/definitions/Relation"
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
            $relation = $this->relationRepository->create($input);
        } catch (\Exception $e) {
            return $this->sendError(__('models.resident.relation.errors.create') . $e->getMessage());
        }


        $relation->load([
            'resident.user',
            'quarter.address',
            'unit.building.address',
            'unit',
            'media',
//            'garant_residents:residents.id,residents.first_name,residents.last_name'
        ]);

        $response = (new RelationTransformer())->transform($relation);
        return $this->sendResponse($response, __('models.resident.relation.saved'));
    }

    /**
     * @SWG\Put(
     *      path="/relations/{id}",
     *      summary="Update the specified Resident Relation in storage",
     *      tags={"Relation"},
     *      description="Update Resident Relation",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Resident Relation",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="Resident that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/Relation")
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
     *                  ref="#/definitions/Relation"
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
     * @throws \OwenIt\Auditing\Exceptions\AuditingException
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function update($id, UpdateRequest $request, PinboardRepository $pinboardRepository)
    {
        $input =  $input = $request->all();
        /** @var Relation $relation */
        $relation = $this->relationRepository->findWithoutFail($id);

        // @TODO by unit ->building or other logic or not needed
        //$shouldPinboard = isset($input['quarter_id']) && $input['quarter_id'] != $relation->quarter_id;
        if (empty($relation)) {
            return $this->sendError(__('models.resident.relation.errors.not_found'));
        }

        try {
            $relation = $this->relationRepository->updateExisting($relation, $input);
        } catch (\Exception $e) {
            return $this->sendError(__('models.resident.errors.create') . $e->getMessage());
        }

//        if ($shouldPinboard) {
//            $pinboardRepository->newResidentRelationPinboard($relation);
//        }

        $relation->load([
            'resident.user',
            'quarter.address',
            'unit.building.address',
            'unit',
            'media',
//            'garant_residents:residents.id,residents.first_name,residents.last_name'
        ]);
        $response = (new RelationTransformer())->transform($relation);
        return $this->sendResponse($response, __('models.resident.saved'));
    }

    /**
     * @SWG\Delete(
     *      path="/relations/{id}",
     *      summary="Remove the specified Resident Relation from storage",
     *      tags={"Relation"},
     *      description="Delete Relation",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Relation",
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
     * @param DeleteRequest $request
     * @return Response
     */
    public function destroy($id, DeleteRequest $request)
    {
        try {
            $this->relationRepository->delete($id);
        } catch (\Exception $e) {
            return $this->sendError('Delete error: ' . $e->getMessage());
        }

        return $this->sendResponse($id, __('models.resident.relation.deleted'));
    }

    /**
     * @SWG\Post(
     *      path="/relations/deletewithids",
     *      summary="Remove multiple Resident Relation from storage",
     *      tags={"Relation"},
     *      description="Delete multiple Relation",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="ids",
     *          description="id of Relation",
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
     * @param DeleteRequest $request
     * @return mixed
     */
    public function destroyWithIds(DeleteRequest $request){
        $ids = $request->get('ids');
        try{
            Relation::destroy($ids);
        }
        catch (\Exception $e) {
            return $this->sendError(__('models.resident.relation.errors.deleted') . $e->getMessage());
        }
        return $this->sendResponse($ids, __('models.resident.relation.deleted'));
    }
}
