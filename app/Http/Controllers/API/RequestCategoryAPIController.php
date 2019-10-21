<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\AppBaseController;
use App\Http\Requests\API\RequestCategory\CreateRequest;
use App\Http\Requests\API\RequestCategory\DeleteRequest;
use App\Http\Requests\API\RequestCategory\ListRequest;
use App\Http\Requests\API\RequestCategory\UpdateRequest;
use App\Http\Requests\API\RequestCategory\ViewRequest;
use App\Models\Request;
use App\Models\RequestCategory;
use App\Repositories\RequestCategoryRepository;
use App\Transformers\RequestCategoryTransformer;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Response;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;

/**
 * Class RequestCategoryAPIController
 * @package App\Http\Controllers\API
 */
class RequestCategoryAPIController extends AppBaseController
{
    /** @var  RequestCategoryRepository */
    private $requestCategoryRepository;

    /**
     * RequestCategoryAPIController constructor.
     * @param RequestCategoryRepository $requestCategoryRepo
     */
    public function __construct(RequestCategoryRepository $requestCategoryRepo)
    {
        $this->requestCategoryRepository = $requestCategoryRepo;
    }

    /**
     * @SWG\Get(
     *      path="/requestCategories",
     *      summary="Get a listing of the RequestCategories.",
     *      tags={"RequestCategory"},
     *      description="Get all RequestCategories",
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
     *                  @SWG\Items(ref="#/definitions/RequestCategory")
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
     * @throws \Exception
     */
    public function index(ListRequest $request)
    {
        $this->requestCategoryRepository->pushCriteria(new RequestCriteria($request));
        $this->requestCategoryRepository->pushCriteria(new LimitOffsetCriteria($request));

        $parentRequestCategories = $this->requestCategoryRepository->with('categories')
            ->findWhere([
                'parent_id' => null
            ]);

        $tree = $request->get('tree', false);
        if ($tree) {
            $requestCategories = new Collection();

            foreach ($parentRequestCategories as $parent) {
                $requestCategories->push($parent);
                if ($parent->categories) {
                    foreach ($parent->categories as $children) {
                        $requestCategories->push($children);
                    }
                }
            }

            $response = (new RequestCategoryTransformer())->transformCollection($requestCategories);
            return $this->sendResponse($response, 'Service Requests Categories retrieved successfully');
        }

        $response = (new RequestCategoryTransformer())->transformCollection($parentRequestCategories);
        return $this->sendResponse($response, 'Service Request Categories retrieved successfully');
    }

    /**
     * @SWG\Get(
     *      path="/categoryTree",
     *      summary="Get a Tree listing of the RequestCategories.",
     *      tags={"RequestCategory"},
     *      description="Get all RequestCategories",
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
     *                  @SWG\Items(ref="#/definitions/RequestCategory")
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
     * @throws \Exception
     */
    public function categoryTree(ListRequest $request)
    {
        $categories = Request::CategorySubCategory;
        $response = [];
        foreach ($categories as $category => $subCategories) {
            $data = get_category_details($category);
            foreach ($subCategories as $subCategory) {
                $data['categories'] = get_sub_category_details($subCategory);
            }

            $response[] = $data;
        }

        return $this->sendResponse($response, 'Service Requests Categories retrieved successfully');
    }

    /**
     * @SWG\Post(
     *      path="/requestCategories",
     *      summary="Store a newly created RequestCategory in storage",
     *      tags={"RequestCategory"},
     *      description="Store RequestCategory",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="RequestCategory that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/RequestCategory")
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
     *                  ref="#/definitions/RequestCategory"
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
     * @return Response
     * @throws \Exception
     */
    public function store(CreateRequest $request)
    {
        $input = $request->only((new RequestCategory)->getFillable());
        $input['has_qualifications'] = false;

        $parentId = $request->get('parent_id');
        if ($parentId) {
            /** @var RequestCategory $requestCategory */
            $parentCategory = $this->requestCategoryRepository->findWithoutFail((int)$parentId);
            if (empty($parentCategory)) {
                return $this->sendError(__('models.requestCategory.errors.parent_not_found'));
            }

            if ($parentCategory->parent_id) {
                return $this->sendError(__('models.requestCategory.errors.multiple_level_not_found'));
            }

            $input['has_qualifications'] = $parentCategory->has_qualifications;
        }

        $requestCategories = $this->requestCategoryRepository->create($input);

        $response = (new RequestCategoryTransformer)->transform($requestCategories);
        return $this->sendResponse($response, __('models.user.requestCategorySaved'));
    }

    /**
     * @SWG\Get(
     *      path="/requestCategories/{id}",
     *      summary="Display the specified RequestCategory",
     *      tags={"RequestCategory"},
     *      description="Get RequestCategory",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of RequestCategory",
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
     *                  ref="#/definitions/RequestCategory"
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
        /** @var RequestCategory $requestCategory */
        $requestCategory = $this->requestCategoryRepository->findWithoutFail($id);
        if (empty($requestCategory)) {
            return $this->sendError(__('models.requestCategory.errors.not_found'));
        }

        $response = (new RequestCategoryTransformer)->transform($requestCategory);
        return $this->sendResponse($response, 'Service Request Category retrieved successfully');
    }

    /**
     * @SWG\Put(
     *      path="/requestCategories/{id}",
     *      summary="Update the specified RequestCategory in storage",
     *      tags={"RequestCategory"},
     *      description="Update RequestCategory",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of RequestCategory",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="RequestCategory that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/RequestCategory")
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
     *                  ref="#/definitions/RequestCategory"
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
     * @param UpdateRequest $request
     * @return mixed
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function update(int $id, UpdateRequest $request)
    {
        $input = $request->only((new RequestCategory)->getFillable());

        /** @var RequestCategory $requestCategory */
        $requestCategory = $this->requestCategoryRepository->findWithoutFail($id);
        if (empty($requestCategory)) {
            return $this->sendError(__('models.requestCategory.errors.not_found'));
        }

        $input['has_qualifications'] = $requestCategory->has_qualifications;

        $parentId = $request->get('parent_id');
        if ($parentId) {
            /** @var RequestCategory $requestCategory */
            $parentCategory = $this->requestCategoryRepository->findWithoutFail((int)$parentId);
            if (empty($parentCategory)) {
                return $this->sendError(__('models.requestCategory.errors.parent_not_found'));
            }

            if ($parentCategory->parent_id) {
                return $this->sendError(__('models.requestCategory.errors.multiple_level_not_found'));
            }

            $input['has_qualifications'] = $parentCategory->has_qualifications;
        }

        $requestCategory = $this->requestCategoryRepository->update($input, $id);

        $response = (new RequestCategoryTransformer())->transform($requestCategory);
        return $this->sendResponse($response, __('models.user.requestCategorySaved'));
    }

    /**
     * @SWG\Delete(
     *      path="/requestCategories/{id}",
     *      summary="Remove the specified RequestCategory from storage",
     *      tags={"RequestCategory"},
     *      description="Delete RequestCategory",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of RequestCategory",
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
     * @param DeleteRequest $r
     * @return mixed
     * @throws \Exception
     */
    public function destroy(int $id, DeleteRequest $r)
    {
        /** @var RequestCategory $requestCategory */
        $requestCategory = $this->requestCategoryRepository->findWithoutFail($id);
        if (empty($requestCategory)) {
            return $this->sendError(__('models.requestCategory.errors.not_found'));
        }

        $usedCategory = Request::where('category_id', $requestCategory->id)->first();
        if ($usedCategory) {
            return $this->sendError(__('models.requestCategory.errors.used_by_request'));
        }

        $requestCategory->delete();
        return $this->sendResponse($id, __('models.user.requestCategoryDeleted'));
    }
}
