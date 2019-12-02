<?php

namespace App\Http\Controllers\API;

use App\Criteria\Common\RequestCriteria;
use App\Criteria\Common\WhereCriteria;
use App\Criteria\UserFilter\FilterByUserCriteria;
use App\Criteria\UserFilter\FilterByMenuCriteria;
use App\Http\Controllers\AppBaseController;
use App\Http\Requests\API\UserFilter\CreateRequest;
use App\Http\Requests\API\UserFilter\DeleteRequest;
use App\Http\Requests\API\UserFilter\ListRequest;
use App\Http\Requests\API\UserFilter\UpdateRequest;
use App\Http\Requests\API\UserFilter\ViewRequest;
use App\Models\UserFilter;
use App\Repositories\UserFilterRepository;
use App\Transformers\UserFilterTransformer;
use Illuminate\Http\Response;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;

/**
 * Class UserFilterController
 * @package App\Http\Controllers\API
 */
class UserFilterAPIController extends AppBaseController
{
    /** @var  UserFilterRepository */
    private $userFilterRepository;

    /**
     * UserFilterAPIController constructor.
     * @param UserFilterRepository $userFilterRepository
     */
    public function __construct(UserFilterRepository $userFilterRepository)
    {
        $this->userFilterRepository = $userFilterRepository;
    }

    /**
     * @SWG\Get(
     *      path="/userFilters",
     *      summary="Get a listing of the UserFilters.",
     *      tags={"UserFilter"},
     *      description="Get all UserFilters",
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
     *                  @SWG\Items(ref="#/definitions/UserFilter")
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
        $this->userFilterRepository->pushCriteria(new RequestCriteria($request));
        $this->userFilterRepository->pushCriteria(new LimitOffsetCriteria($request));
        $this->userFilterRepository->pushCriteria(new FilterByMenuCriteria($request));
        $this->userFilterRepository->pushCriteria(new FilterByUserCriteria($request));

        $getAll = $request->get('get_all', false);
        if ($getAll) {
            $userFilters = $this->userFilterRepository->get();
            $response = (new UserFilterTransformer)->transformCollection($userFilters);
            return $this->sendResponse($response, 'UserFilters retrieved successfully');
        }

        $perPage = $request->get('per_page', env('APP_PAGINATE', 10));

        $userFilters = $this->userFilterRepository->paginate($perPage);
        $response = (new UserFilterTransformer)->transformPaginator($userFilters);
        return $this->sendResponse($response, 'UserFilters retrieved successfully');
    }

    /**
     * @SWG\Post(
     *      path="/userFilters",
     *      summary="Store a newly created UserFilter in storage",
     *      tags={"UserFilter"},
     *      description="Store UserFilter",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="UserFilter that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/UserFilter")
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
     *                  ref="#/definitions/UserFilter"
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
     */
    public function store(CreateRequest $request)
    {
        $input = $request->all();
        $input['sq_meter'] = $input['sq_meter'] ?? 0;
        try {
            $userFilter = $this->userFilterRepository->create($input);
        } catch (\Exception $e) {
            return $this->sendError(__('models.userFilter.errors.create') . $e->getMessage());
        }

        $response = (new UserFilterTransformer)->transform($userFilter);
        return $this->sendResponse($response, __('models.userFilter.saved'));
    }

    /**
     * @SWG\Get(
     *      path="/userFilters/{id}",
     *      summary="Display the specified UserFilter",
     *      tags={"UserFilter"},
     *      description="Get UserFilter",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of UserFilter",
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
     *                  ref="#/definitions/UserFilter"
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
     */
    public function show($id, ViewRequest $r)
    {
        /** @var UserFilter $userFilter */
        $userFilter = $this->userFilterRepository->findWithoutFail($id);
        if (empty($userFilter)) {
            return $this->sendError(__('models.userFilter.errors.not_found'));
        }

        $response = (new UserFilterTransformer)->transform($userFilter);
        return $this->sendResponse($response, 'UserFilter retrieved successfully');
    }

    /**
     * @SWG\Put(
     *      path="/userFilters/{id}",
     *      summary="Update the specified UserFilter in storage",
     *      tags={"UserFilter"},
     *      description="Update UserFilter",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of UserFilter",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="UserFilter that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/UserFilter")
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
     *                  ref="#/definitions/UserFilter"
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
     */
    public function update($id, UpdateRequest $request)
    {
        $input = $request->all();
        /** @var UserFilter $userFilter */
        $userFilter = $this->userFilterRepository->findWithoutFail($id);
        if (empty($userFilter)) {
            return $this->sendError(__('models.userFilter.errors.not_found'));
        }

        try {
            $userFilter = $this->userFilterRepository->update($input, $id);
        } catch (\Exception $e) {
            return $this->sendError(__('models.userFilter.errors.update') . $e->getMessage());
        }

        $response = (new UserFilterTransformer)->transform($userFilter);
        return $this->sendResponse($response, __('models.userFilter.saved'));
    }

    /**
     * @SWG\Delete(
     *      path="/userFilters/{id}",
     *      summary="Remove the specified UserFilter from storage",
     *      tags={"UserFilter"},
     *      description="Delete UserFilter",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of UserFilter",
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
        /** @var UserFilter $userFilter */
        $userFilter = $this->userFilterRepository->findWithoutFail($id);

        if (empty($userFilter)) {
            return $this->sendError(__('models.userFilter.errors.not_found'));
        }

        $userFilter->delete();

        return $this->sendResponse($id, __('models.userFilter.deleted'));
    }
}
