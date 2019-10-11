<?php

namespace App\Http\Controllers\API;

use App\Criteria\Comment\FilterChildrenCriteria;
use App\Criteria\Comment\FilterChildrenOutCriteria;
use App\Criteria\Comment\FilterIdsOutCriteria;
use App\Criteria\Comment\FilterModelCriteria;
use App\Http\Controllers\AppBaseController;
use App\Http\Requests\API\Comment\ChildrenListRequest;
use App\Http\Requests\API\Comment\CreateRequest;
use App\Http\Requests\API\Comment\DeleteRequest;
use App\Http\Requests\API\Comment\ListRequest;
use App\Http\Requests\API\Comment\UpdateRequest;
use App\Notifications\PinboardCommented;
use App\Notifications\ListingCommented;
use App\Repositories\CommentRepository;
use App\Repositories\PinboardRepository;
use App\Repositories\ListingRepository;
use App\Repositories\SettingsRepository;
use App\Repositories\RequestRepository;
use App\Transformers\CommentTransformer;
use Illuminate\Http\Response;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;

/**
 * Class CommentController
 * @package App\Http\Controllers\API
 */
class CommentAPIController extends AppBaseController
{
    /** @var  CommentRepository */
    private $pinboardRepository;
    /**
     * @var ListingRepository
     */
    private $listingRepository;
    /**
     * @var RequestRepository
     */
    private $requestRepository;
    /**
     * @var CommentRepository
     */
    private $commentRepository;
    /**
     * @var SettingsRepository
     */
    private $settingsRepository;
    /**
     * @var CommentTransformer
     */
    private $transformer;

    /**
     * CommentAPIController constructor.
     * @param PinboardRepository $pinboardRepo
     * @param CommentRepository $commRepo
     * @param ListingRepository $prodRepo
     * @param RequestRepository $sr
     * @param SettingsRepository $setRepo
     * @param CommentTransformer $pt
     */
    public function __construct(
        PinboardRepository $pinboardRepo,
        CommentRepository $commRepo,
        ListingRepository $prodRepo,
        RequestRepository $sr,
        SettingsRepository $setRepo,
        CommentTransformer $pt
    )
    {
        $this->pinboardRepository = $pinboardRepo;
        $this->listingRepository = $prodRepo;
        $this->requestRepository = $sr;
        $this->commentRepository = $commRepo;
        $this->settingsRepository = $setRepo;
        $this->transformer = $pt;
    }

    /**
     * @SWG\Post(
     *      path="/pinboard/{id}/comments",
     *      summary="Store a newly created comment in storage",
     *      tags={"Comment"},
     *      description="Store Comment",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="comment",
     *          in="body",
     *          description="Comment that should be stored",
     *          required=true,
     *          @SWG\Schema(ref="#/definitions/Comment")
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
     *                  ref="#/definitions/Comment"
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
     * @param CreateRequest $createRequest
     * @return Response
     */
    public function storePinboardComment(int $id, CreateRequest $createRequest)
    {
        $pinboard = $this->pinboardRepository->findWithoutFail($id);
        if (empty($pinboard)) {
            return $this->sendError(__('models.pinboard.errors.not_found'));
        }

        $comment = $pinboard->comment($createRequest->comment, $createRequest->parent_id);
        $comment->load('user');


        // if logged in user is resident and
        // author of pinboard is resident and
        // author of pinboard is different than liker
        $u = \Auth::user();
        if ($u->resident && $pinboard->user->resident && $u->id != $pinboard->user_id) {
            $pinboard->user->notify(new PinboardCommented($pinboard, $u->resident, $comment));
        }
        $out = $this->transformer->transform($comment);
        return $this->sendResponse($out, __('general.comment_created'));
    }

    /**
     * @SWG\Post(
     *      path="/requests/{id}/comments",
     *      summary="Store a newly created comment in storage",
     *      tags={"Comment"},
     *      description="Store Comment",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="comment",
     *          in="body",
     *          description="Comment that should be stored",
     *          required=true,
     *          @SWG\Schema(ref="#/definitions/Comment")
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
     *                  ref="#/definitions/Comment"
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
     * @param CreateRequest $createRequest
     * @return Response
     */
    public function storeRequestComment(int $id, CreateRequest $createRequest)
    {
        $request = $this->requestRepository->findWithoutFail($id);
        if (empty($request)) {
            return $this->sendError(__('models.request.errors.not_found'));
        }

        $comment = $request->comment($createRequest->comment, $createRequest->parent_id);
        $comment->load('user');
        $out = $this->transformer->transform($comment);
        $this->requestRepository->notifyNewComment($request, $comment);

        return $this->sendResponse($out, __('general.comment_created'));
    }

    /**
     * @SWG\Post(
     *      path="/listings/{id}/comments",
     *      summary="Store a newly created comment in storage",
     *      tags={"Listing"},
     *      description="Store Comment",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="comment",
     *          in="body",
     *          description="Comment that should be stored",
     *          required=true,
     *          @SWG\Schema(ref="#/definitions/Comment")
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
     *                  ref="#/definitions/Comment"
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
     * @param CreateRequest $createRequest
     * @return Response
     */
    public function storeListingComment(int $id, CreateRequest $createRequest)
    {
        $listing = $this->listingRepository->findWithoutFail($id);
        if (empty($listing)) {
            return $this->sendError(__('models.listing.errors.not_found'));
        }

        $comment = $listing->comment($createRequest->comment, $createRequest->parent_id);
        $comment->load('user');

        // if logged in user is resident and
        // author of pinboard is resident and
        // author of pinboard is different than liker
        $u = \Auth::user();
        if ($u->resident && $listing->user->resident && $u->id != $listing->user_id) {
            $listing->user->notify(new ListingCommented($listing, $u->resident, $comment));
        }
        $out = $this->transformer->transform($comment);
        return $this->sendResponse($out, __('models.listing.comment_created'));
    }

    /**
     * @SWG\Get(
     *      path="/comments",
     *      summary="Get a listing of the Comments.",
     *      tags={"Comment"},
     *      description="Get all Comments",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          in="query",
     *          description="The id of the commentable to retrieve comments for",
     *          required=true,
     *          type="integer"
     *      ),
     *      @SWG\Parameter(
     *          name="commentable",
     *          in="query",
     *          description="The type of the commentable to retrieve comments for",
     *          required=true,
     *          type="integer"
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
     *                  @SWG\Items(ref="#/definitions/Comment")
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     *
     * @param ListRequest $listRequest
     * @return mixed
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function index(ListRequest $listRequest)
    {
        $this->commentRepository->pushCriteria(new RequestCriteria($listRequest));
        $this->commentRepository->pushCriteria(new LimitOffsetCriteria($listRequest));
        $this->commentRepository->pushCriteria(new FilterChildrenOutCriteria());
        $this->commentRepository->pushCriteria(new FilterModelCriteria($listRequest));
        $this->commentRepository->pushCriteria(new FilterIdsOutCriteria($listRequest));

        $perPage = $listRequest->get('per_page', env('APP_PAGINATE', 10));
        $comments = $this->commentRepository->with([
            'user',
            'childrenCountRelation',
            // 'children' => function($q){$q->orderBy("created_at", "desc");},
            // 'children.user',
        ])->paginate($perPage);

        $out = $this->transformer->transformPaginator($comments);
        return $this->sendResponse($out, 'Comments retrieved successfully');
    }

    /**
     * @SWG\Get(
     *      path="/comments/{id}",
     *      summary="Get a listing of the children comments.",
     *      tags={"Comment"},
     *      description="Get children comments",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          in="path",
     *          description="The id of the comment to retrieve children for",
     *          required=true,
     *          type="integer"
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
     *                  @SWG\Items(ref="#/definitions/Comment")
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     *
     * @param ChildrenListRequest $request
     * @return Response
     * @throws \Exception
     */
    public function children($id, ChildrenListRequest $request)
    {
        $this->commentRepository->pushCriteria(new RequestCriteria($request));
        $this->commentRepository->pushCriteria(new LimitOffsetCriteria($request));
        $this->commentRepository->pushCriteria(new FilterChildrenCriteria($request));
        $this->commentRepository->pushCriteria(new FilterIdsOutCriteria($request));

        $perPage = $request->get('per_page', env('APP_PAGINATE', 10));
        $comments = $this->commentRepository
            ->with([ 'user'])->paginate($perPage);

        $out = $this->transformer->transformPaginator($comments);
        return $this->sendResponse($out, 'Children comments retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateRequest $request
     * @return Response
     * @throws \Exception
     *
     * @SWG\Put(
     *      path="/comments/{id}",
     *      summary="Update a created comment in storage",
     *      tags={"Comment"},
     *      description="Update Comment",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="comment",
     *          in="body",
     *          description="Comment that should be updated",
     *          required=true,
     *          @SWG\Schema(ref="#/definitions/Comment")
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
     *                  ref="#/definitions/Comment"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function updateComment(int $id, UpdateRequest $request)
    {
        $comment = $this->commentRepository->findWithoutFail($id);
        if (empty($comment)) {
            return $this->sendError(__('general.comment_not_found'));
        }

        $timeout = 120;
        if ($settings = $this->settingsRepository->first()) {
            $timeout = $settings->comment_update_timeout;
        }
        $isAdmin = $request->user()->hasRole('administrator') ||
            $request->user()->hasRole('administrator');
        if (!$isAdmin && $comment->created_at->addMinutes($timeout)->lessThan(now())) {
            $err = "Comments can only be edited in the first %d minutes after being created.";
            return $this->sendError(sprintf($err, $timeout));
        }
        $input = ['comment' => $request->comment];
        $comment = $this->commentRepository->update($input, $id);
        $comment->load([
            'user',
            'childrenCountRelation',
        ]);

        $data = $this->transformer->transform($comment);
        return $this->sendResponse($data, __('general.comment_updated'));
    }

    /**
     * @SWG\Delete(
     *      path="/comments/{id}",
     *      summary="Detele a created comment in storage",
     *      tags={"Comment"},
     *      description="Delete Comment",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="comment",
     *          in="body",
     *          description="Comment that should be deleted",
     *          required=true,
     *          @SWG\Schema(ref="#/definitions/Comment")
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
     *                  ref="#/definitions/Comment"
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
     * @throws \Exception
     */
    public function destroyComment(int $id, DeleteRequest $request)
    {
        $comment = $this->commentRepository->findWithoutFail($id);

        if (empty($comment)) {
            return $this->sendError(__('general.comment_not_found'));
        }

        return $this->sendResponse($this->commentRepository->destroy($comment), __('general.comment_deleted'));
    }
}
