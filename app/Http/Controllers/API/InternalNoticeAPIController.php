<?php

namespace App\Http\Controllers\API;

use App\Criteria\Common\WhereCriteria;
use App\Http\Requests\API\InternalNotice\CreateRequest;
use App\Http\Requests\API\InternalNotice\DeleteRequest;
use App\Http\Requests\API\InternalNotice\ListRequest;
use App\Http\Requests\API\InternalNotice\UpdateRequest;
use App\Http\Requests\API\InternalNotice\ViewRequest;
use App\Models\InternalNotice;
use App\Repositories\InternalNoticeRepository;
use App\Transformers\InternalNotesTransformer;
use App\Http\Controllers\AppBaseController;
use Illuminate\Support\Facades\Auth;

/**
 * Class InternalNoticeController
 * @package App\Http\Controllers\API
 */

class InternalNoticeAPIController extends AppBaseController
{
    /** @var  InternalNoticeRepository */
    private $internalNoticeRepository;

    /**
     * InternalNoticeAPIController constructor.
     * @param InternalNoticeRepository $internalNoticeRepo
     */
    public function __construct(InternalNoticeRepository $internalNoticeRepo)
    {
        $this->internalNoticeRepository = $internalNoticeRepo;
    }

    /**
     * @SWG\Get(
     *      path="/internalNotices",
     *      summary="Get a listing of the InternalNotices.",
     *      tags={"InternalNotice"},
     *      description="Get all InternalNotices",
     *      produces={"application/json"},
     *     @SWG\Parameter(
     *          name="get_all",
     *          in="query",
     *          description="Get all InternalNotices. if no pass it must be return paginated",
     *          type="string",
     *      ),
     *     @SWG\Parameter(
     *          name="request_id",
     *          in="query",
     *          description="Get only request related InternalNotices",
     *          type="integer",
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
     *                  @SWG\Items(ref="#/definitions/InternalNotice")
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
        $perPage = $request->get('per_page', env('APP_PAGINATE', 10));
        if (!empty($request->request_id)) {
            $this->internalNoticeRepository->pushCriteria(new WhereCriteria('request_id', $request->request_id));
        }

        $getAll = $request->get('get_all', false);
        if ($getAll) {
            $internalNotices = $this->internalNoticeRepository->get();
            $response = $internalNotices->toArray();
        } else {
            $internalNotices = $this->internalNoticeRepository->with('user')->paginate($perPage);
            $response = (new InternalNotesTransformer())->transformPaginator($internalNotices);
        }


        return $this->sendResponse($response, 'Internal Notices retrieved successfully');
    }

    /**
     * @SWG\Post(
     *      path="/internalNotices",
     *      summary="Store a newly created InternalNotice in storage",
     *      tags={"InternalNotice"},
     *      description="Store InternalNotice",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="InternalNotice that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/InternalNotice")
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
     *                  ref="#/definitions/InternalNotice"
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
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function store(CreateRequest $request)
    {
        $input = $request->all();

        $input['user_id'] = Auth::id();
        $input['manager_ids'] = $request->manager_ids ?? $request->selectedManagerLists ?? '';
        $internalNotice = $this->internalNoticeRepository->create($input);
        $internalNotice->load('user');
        return $this->sendResponse($internalNotice->toArray(), __('models.request.internal_notice_saved'));
    }

    /**
     * @SWG\Get(
     *      path="/internalNotices/{id}",
     *      summary="Display the specified InternalNotice",
     *      tags={"InternalNotice"},
     *      description="Get InternalNotice",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of InternalNotice",
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
     *                  ref="#/definitions/InternalNotice"
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
        /** @var InternalNotice $internalNotice */
        $internalNotice = $this->internalNoticeRepository->find($id);

        if (empty($internalNotice)) {
            return $this->sendError(__('models.request.errors.internal_notice_not_found'));
        }
        $internalNotice->load('user');
        $response = (new InternalNotesTransformer())->transform($internalNotice);
        return $this->sendResponse($response, 'Internal Notice retrieved successfully');
    }

    /**
     * @SWG\Put(
     *      path="/internalNotices/{id}",
     *      summary="Update the specified InternalNotice in storage",
     *      tags={"InternalNotice"},
     *      description="Update InternalNotice",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of InternalNotice",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="InternalNotice that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/InternalNotice")
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
     *                  ref="#/definitions/InternalNotice"
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
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function update($id, UpdateRequest $request)
    {
        $input = $request->all();

        /** @var InternalNotice $internalNotice */
        $internalNotice = $this->internalNoticeRepository->find($id);

        if (empty($internalNotice)) {
            return $this->sendError(__('models.request.errors.internal_notice_not_found'));
        }

        $internalNotice = $this->internalNoticeRepository->update($input, $id);
        $internalNotice->load('user');
        return $this->sendResponse($internalNotice->toArray(), __('models.request.internal_notice_updated'));
    }

    /**
     * @SWG\Delete(
     *      path="/internalNotices/{id}",
     *      summary="Remove the specified InternalNotice from storage",
     *      tags={"InternalNotice"},
     *      description="Delete InternalNotice",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of InternalNotice",
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
        /** @var InternalNotice $internalNotice */
        $internalNotice = $this->internalNoticeRepository->find($id);

        if (empty($internalNotice)) {
            return $this->sendError(__('models.request.errors.internal_notice_not_found'));
        }

        $internalNotice->delete();

        return $this->sendResponse($internalNotice->only('id', 'request_id', 'user_id', 'comment'), __('models.request.internal_notice_deleted'));
    }
}
