<?php

namespace App\Http\Controllers\API;

use App\Criteria\Common\RequestCriteria;
use App\Criteria\Pinboard\FilterByResidentCriteria;
use App\Criteria\ResidentsRentContract\FilterByBuildingCriteria;
use App\Criteria\ResidentsRentContract\FilterByUnitCriteria;
use App\Http\Controllers\AppBaseController;
use App\Http\Requests\API\RentContract\ShowRequest;
use App\Http\Requests\API\RentContract\DeleteRequest;
use App\Http\Requests\API\RentContract\UpdateRequest;
use App\Http\Requests\API\RentContract\CreateRequest;
use App\Http\Requests\API\RentContract\ListRequest;
use App\Models\RentContract;
use App\Repositories\RentContractRepository;
use App\Transformers\RentContractTransformer;
use Illuminate\Http\Response;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;

/**
 * Class RentContractController
 * @package App\Http\Controllers\API
 */
class RentContractAPIController extends AppBaseController
{
    /** @var  RentContractRepository */
    private $rentContractRepository;

    /**
     * RentContractAPIController constructor.
     * @param RentContractRepository $rentContractRepo
     */
    public function __construct(RentContractRepository $rentContractRepo)
    {
        $this->rentContractRepository = $rentContractRepo;
    }

    /**
     * @SWG\Get(
     *      path="/rent-contracts",
     *      summary="Get a listing of the RentContracts.",
     *      tags={"RentContract"},
     *      description="Get all RentContracts",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="building_id",
     *          in="query",
     *          type="integer",
     *          description="fuilter by building",
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
     *                  @SWG\Items(ref="#/definitions/RentContract")
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
            'model' => (new RentContract)->getTable(),
        ]);

        $this->rentContractRepository->pushCriteria(new RequestCriteria($request));
        $this->rentContractRepository->pushCriteria(new LimitOffsetCriteria($request));
        $this->rentContractRepository->pushCriteria(new FilterByBuildingCriteria($request));
        $this->rentContractRepository->pushCriteria(new FilterByUnitCriteria($request));
        $this->rentContractRepository->pushCriteria(new FilterByResidentCriteria($request));

        $getAll = $request->get('get_all', false);
        if ($getAll) {
            $request->merge(['limit' => env('APP_PAGINATE', 10)]);
            $this->rentContractRepository->pushCriteria(new LimitOffsetCriteria($request));
            $rentContracts = $this->rentContractRepository->get();
            $response = (new RentContractTransformer())->transformCollection($rentContracts);
            return $this->sendResponse($response, 'RentContracts retrieved successfully');
        }

        $perPage = $request->get('per_page', env('APP_PAGINATE', 10));
        // @TODO CONTRACT is need? building, address, unit . I think not need because many
        $rentContracts = $this->rentContractRepository->with(['resident', 'building.address', 'unit'])->paginate($perPage);
        $response = (new RentContractTransformer())->transformPaginator($rentContracts);
        return $this->sendResponse($response, 'RentContracts retrieved successfully');
    }

    /**
     * @SWG\Get(
     *      path="/rent-contracts/{id}",
     *      summary="Display the specified Resident Rent Contract",
     *      tags={"RentContract"},
     *      description="Get Resident Rent Contract",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Resident Rent Contract",
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
     *                  ref="#/definitions/RentContract"
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
        /** @var RentContract $rentContract */
        $rentContract = $this->rentContractRepository->findWithoutFail($id);
        if (empty($rentContract)) {
            return $this->sendError(__('models.rent_contract.errors.not_found'));
        }

        $rentContract->load(['resident', 'building.address', 'unit']);
        $response = (new RentContractTransformer())->transform($rentContract);
        return $this->sendResponse($response, 'Resident Rent Contract retrieved successfully');
    }

    /**
     * @SWG\Post(
     *      path="/rent-contracts",
     *      summary="Store a newly created Resident renat Contract in storage",
     *      tags={"RentContract"},
     *      description="Store Resident Rent Contract",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="Resident that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/RentContract")
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
     *                  ref="#/definitions/RentContract"
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
            $rentContract = $this->rentContractRepository->create($input);
        } catch (\Exception $e) {
            return $this->sendError(__('models.rent_contract.errors.create') . $e->getMessage());
        }


        $rentContract->load(['resident', 'building.address', 'unit']);

        $response = (new RentContractTransformer())->transform($rentContract);
        return $this->sendResponse($response, __('models.rent_contract.saved'));
    }

    /**
     * @SWG\Put(
     *      path="/rent-contracts/{id}",
     *      summary="Update the specified Resident Rent Contract in storage",
     *      tags={"RentContract"},
     *      description="Update Resident Rent Contract",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Resident Rent Contract",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="Resident that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/RentContract")
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
     *                  ref="#/definitions/RentContract"
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
        $input =  $input = $request->all();
        /** @var RentContract $rentContract */
        $rentContract = $this->rentContractRepository->findWithoutFail($id);
        if (empty($rentContract)) {
            return $this->sendError(__('models.rent_contract.errors.not_found'));
        }

        try {
            $resident = $this->rentContractRepository->updateExisting($rentContract, $input);
        } catch (\Exception $e) {
            return $this->sendError(__('models.resident.errors.create') . $e->getMessage());
        }

        $rentContract->load(['resident', 'building.address', 'unit']);
        $response = (new RentContractTransformer())->transform($rentContract);
        return $this->sendResponse($response, __('models.resident.saved'));
    }

    /**
     * @SWG\Delete(
     *      path="/rent-contracts/{id}",
     *      summary="Remove the specified Resident Rent Contract from storage",
     *      tags={"RentContract"},
     *      description="Delete RentContract",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of RentContract",
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
            $this->rentContractRepository->delete($id);
        } catch (\Exception $e) {
            return $this->sendError('Delete error: ' . $e->getMessage());
        }

        return $this->sendResponse($id, __('models.rent_contract.deleted'));
    }

    /**
     * @SWG\Post(
     *      path="/rent-contracts/deletewithids",
     *      summary="Remove multiple Resident Rent Contract from storage",
     *      tags={"RentContract"},
     *      description="Delete multiple RentContract",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="ids",
     *          description="id of RentContract",
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
            RentContract::destroy($ids);
        }
        catch (\Exception $e) {
            return $this->sendError(__('models.rent_contract.errors.deleted') . $e->getMessage());
        }
        return $this->sendResponse($ids, __('models.rent_contract.deleted'));
    }
}
