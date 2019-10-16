<?php

namespace App\Http\Controllers\API;

use App\Criteria\Common\RequestCriteria;
use App\Criteria\Pinboard\FilterByResidentCriteria;
use App\Criteria\Contract\FilterByBuildingCriteria;
use App\Criteria\Contract\FilterByUnitCriteria;
use App\Http\Controllers\AppBaseController;
use App\Http\Requests\API\Contract\ShowRequest;
use App\Http\Requests\API\Contract\DeleteRequest;
use App\Http\Requests\API\Contract\UpdateRequest;
use App\Http\Requests\API\Contract\CreateRequest;
use App\Http\Requests\API\Contract\ListRequest;
use App\Models\Contract;
use App\Repositories\ContractRepository;
use App\Transformers\ContractTransformer;
use Illuminate\Http\Response;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;

/**
 * Class ContractController
 * @package App\Http\Controllers\API
 */
class ContractAPIController extends AppBaseController
{
    /** @var  ContractRepository */
    private $contractRepository;

    /**
     * ContractAPIController constructor.
     * @param ContractRepository $contractRepo
     */
    public function __construct(ContractRepository $contractRepo)
    {
        $this->contractRepository = $contractRepo;
    }

    /**
     * @SWG\Get(
     *      path="/contracts",
     *      summary="Get a listing of the Contracts.",
     *      tags={"Contract"},
     *      description="Get all Contracts",
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
     *                  @SWG\Items(ref="#/definitions/Contract")
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
            'model' => (new Contract)->getTable(),
        ]);

        $this->contractRepository->pushCriteria(new RequestCriteria($request));
        $this->contractRepository->pushCriteria(new LimitOffsetCriteria($request));
        $this->contractRepository->pushCriteria(new FilterByBuildingCriteria($request));
        $this->contractRepository->pushCriteria(new FilterByUnitCriteria($request));
        $this->contractRepository->pushCriteria(new FilterByResidentCriteria($request));

        $getAll = $request->get('get_all', false);
        if ($getAll) {
            $request->merge(['limit' => env('APP_PAGINATE', 10)]);
            $this->contractRepository->pushCriteria(new LimitOffsetCriteria($request));
            $contracts = $this->contractRepository->get();
            $response = (new ContractTransformer())->transformCollection($contracts);
            return $this->sendResponse($response, 'Contracts retrieved successfully');
        }

        $perPage = $request->get('per_page', env('APP_PAGINATE', 10));
        // @TODO CONTRACT is need? building, address, unit . I think not need because many
        $contracts = $this->contractRepository->with(['resident', 'building.address', 'unit'])->paginate($perPage);
        $response = (new ContractTransformer())->transformPaginator($contracts);
        return $this->sendResponse($response, 'Contracts retrieved successfully');
    }

    /**
     * @SWG\Get(
     *      path="/contracts/{id}",
     *      summary="Display the specified Resident Contract",
     *      tags={"Contract"},
     *      description="Get Resident Contract",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Resident Contract",
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
     *                  ref="#/definitions/Contract"
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
        /** @var Contract $contract */
        $contract = $this->contractRepository->findWithoutFail($id);
        if (empty($contract)) {
            return $this->sendError(__('models.resident.contract.errors.not_found'));
        }

        $contract->load(['resident', 'building.address', 'unit']);
        $response = (new ContractTransformer())->transform($contract);
        return $this->sendResponse($response, 'Resident Contract retrieved successfully');
    }

    /**
     * @SWG\Post(
     *      path="/contracts",
     *      summary="Store a newly created Resident renat Contract in storage",
     *      tags={"Contract"},
     *      description="Store Resident Contract",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="Resident that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/Contract")
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
     *                  ref="#/definitions/Contract"
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
            $contract = $this->contractRepository->create($input);
        } catch (\Exception $e) {
            return $this->sendError(__('models.resident.contract.errors.create') . $e->getMessage());
        }


        $contract->load(['resident', 'building.address', 'unit']);

        $response = (new ContractTransformer())->transform($contract);
        return $this->sendResponse($response, __('models.resident.contract.saved'));
    }

    /**
     * @SWG\Put(
     *      path="/contracts/{id}",
     *      summary="Update the specified Resident Contract in storage",
     *      tags={"Contract"},
     *      description="Update Resident Contract",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Resident Contract",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="Resident that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/Contract")
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
     *                  ref="#/definitions/Contract"
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
        /** @var Contract $contract */
        $contract = $this->contractRepository->findWithoutFail($id);
        if (empty($contract)) {
            return $this->sendError(__('models.resident.contract.errors.not_found'));
        }

        try {
            $resident = $this->contractRepository->updateExisting($contract, $input);
        } catch (\Exception $e) {
            return $this->sendError(__('models.resident.errors.create') . $e->getMessage());
        }

        $contract->load(['resident', 'building.address', 'unit']);
        $response = (new ContractTransformer())->transform($contract);
        return $this->sendResponse($response, __('models.resident.saved'));
    }

    /**
     * @SWG\Delete(
     *      path="/contracts/{id}",
     *      summary="Remove the specified Resident Contract from storage",
     *      tags={"Contract"},
     *      description="Delete Contract",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Contract",
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
            $this->contractRepository->delete($id);
        } catch (\Exception $e) {
            return $this->sendError('Delete error: ' . $e->getMessage());
        }

        return $this->sendResponse($id, __('models.resident.contract.deleted'));
    }

    /**
     * @SWG\Post(
     *      path="/contracts/deletewithids",
     *      summary="Remove multiple Resident Contract from storage",
     *      tags={"Contract"},
     *      description="Delete multiple Contract",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="ids",
     *          description="id of Contract",
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
            Contract::destroy($ids);
        }
        catch (\Exception $e) {
            return $this->sendError(__('models.resident.contract.errors.deleted') . $e->getMessage());
        }
        return $this->sendResponse($ids, __('models.resident.contract.deleted'));
    }
}
