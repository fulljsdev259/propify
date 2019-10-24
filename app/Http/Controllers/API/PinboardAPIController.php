<?php

namespace App\Http\Controllers\API;

use App\Criteria\Pinboard\FeedCriteria;
use App\Criteria\Pinboard\FilterByBuildingCriteria;
use App\Criteria\Pinboard\FilterByQuarterCriteria;
use App\Criteria\Pinboard\FilterByPermissionCriteria;
use App\Criteria\Pinboard\FilterByAnnouncementCriteria;
use App\Criteria\Pinboard\FilterByStatusCriteria;
use App\Criteria\Pinboard\FilterByTypeCriteria;
use App\Criteria\Pinboard\FilterByUserCriteria;
use App\Criteria\Pinboard\FilterByResidentCriteria;
use App\Http\Controllers\AppBaseController;
use App\Http\Requests\API\Pinboard\AssignRequest;
use App\Http\Requests\API\Pinboard\CreateRequest;
use App\Http\Requests\API\Pinboard\DeleteRequest;
use App\Http\Requests\API\Pinboard\LikeRequest;
use App\Http\Requests\API\Pinboard\ListRequest;
use App\Http\Requests\API\Pinboard\PublishRequest;
use App\Http\Requests\API\Pinboard\ShowRequest;
use App\Http\Requests\API\Pinboard\UnAssignRequest;
use App\Http\Requests\API\Pinboard\UpdateRequest;
use App\Http\Requests\API\Pinboard\ListViewsRequest;
use App\Http\Requests\API\Pinboard\ViewRequest;
use App\Models\Pinboard;
use App\Notifications\PinboardLiked;
use App\Repositories\BuildingRepository;
use App\Repositories\QuarterRepository;
use App\Repositories\PinboardRepository;
use App\Repositories\SettingsRepository;
use App\Repositories\ServiceProviderRepository;
use App\Transformers\PinboardTransformer;
use App\Transformers\PinboardViewTransformer;
use App\Transformers\UserTransformer;
use Carbon\Carbon;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;

/**
 * Class PinboardAPIController
 * @package App\Http\Controllers\API
 */
class PinboardAPIController extends AppBaseController
{
    /** @var  PinboardRepository */
    private $pinboardRepository;
    /**
     * @var PinboardTransformer
     */
    private $transformer;
    /**
     * @var UserTransformer
     */
    private $uTransformer;

    /**
     * PinboardAPIController constructor.
     * @param PinboardRepository $pinboardRepo
     * @param PinboardTransformer $pt
     * @param UserTransformer $ut
     */
    public function __construct(PinboardRepository $pinboardRepo, PinboardTransformer $pt, UserTransformer $ut)
    {
        $this->pinboardRepository = $pinboardRepo;
        $this->transformer = $pt;
        $this->uTransformer = $ut;
    }

    /**
     * @SWG\Get(
     *      path="/pinboard",
     *      summary="Get a listing of the Pinboard.",
     *      tags={"Listing"},
     *      description="Get all Pinboard",
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
     *                  @SWG\Items(ref="#/definitions/Pinboard")
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
        $this->pinboardRepository->pushCriteria(new LimitOffsetCriteria($request));
        $this->pinboardRepository->pushCriteria(new FeedCriteria($request));
        $this->pinboardRepository->pushCriteria(new FilterByStatusCriteria($request));
        $this->pinboardRepository->pushCriteria(new FilterByTypeCriteria($request));
        $this->pinboardRepository->pushCriteria(new FilterByPermissionCriteria($request));
        $this->pinboardRepository->pushCriteria(new FilterByUserCriteria($request));
        $this->pinboardRepository->pushCriteria(new FilterByQuarterCriteria($request));
        $this->pinboardRepository->pushCriteria(new FilterByBuildingCriteria($request));
        $this->pinboardRepository->pushCriteria(new FilterByAnnouncementCriteria($request));
        $this->pinboardRepository->pushCriteria(new FilterByResidentCriteria($request));

        $perPage = $request->get('per_page', env('APP_PAGINATE', 10));
        $pinboard = $this->pinboardRepository->with([
            'media',
            'user.resident',
            'likesCounter',
            'likes',
            'likes.user',
            'buildings.address.state',
            'buildings.service_providers',
            'buildings.media',
            'providers',
        ])->withCount('views')->paginate($perPage);
        $pinboard->getCollection()->loadCount('allComments');


        $out = $this->transformer->transformPaginator($pinboard);
        return $this->sendResponse($out, 'Pinboard retrieved successfully');
    }

    /**
     * @SWG\Post(
     *      path="/pinboard",
     *      summary="Store a newly created Pinboard in storage",
     *      tags={"Listing"},
     *      description="Store Pinboard",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="Pinboard that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/Pinboard")
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
     *                  ref="#/definitions/Pinboard"
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
     * @param SettingsRepository $settingsRepository
     * @return mixed
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function store(CreateRequest $request, SettingsRepository $settingsRepository)
    {
        $input = $request->only(Pinboard::Fillable);
        $input['user_id'] = \Auth::id();
	    $input['building_ids'] = $request->building_ids ?? [];
        $input['quarter_ids'] = $request->quarter_ids ?? [];
        $input['provider_ids'] = $request->provider_ids ?? [];

        if (! Auth::user()->hasRole('administrator')) {
            $input['status'] = Pinboard::StatusNew;
        } else {
            $input['status'] = $input['status'] ?? Pinboard::StatusNew;
        }

        if ($request->has('pinned')) {
            $input['announcement'] = $request->pinned;
        }

        if ($request->announcement  == true || $request->pinned  == true) { // @TODO delete pinned
            $input['type'] = Pinboard::TypeAnnouncement;
        } else {
            $input['type'] =  $input['type'] ?? Pinboard::TypePost;
        }

        //$input['needs_approval'] = true; // @TODO
        $input['needs_approval'] = ! Auth::user()->hasRole('administrator');
        if (! empty($input['type']) && $input['type'] == Pinboard::TypePost) {
            $input['notify_email'] = true;
            $settings = $settingsRepository->first();
            if ($settings) {
                $input['needs_approval'] = $settings->pinboard_approval_enable;
            }
        }

        $pinboard = $this->pinboardRepository->create($input);
        $pinboard->load([
            'media',
            'user.resident',
            'likesCounter',
            'likes',
            'likes.user',
            'buildings.address.state',
            'buildings.service_providers',
            'buildings.media',
            'quarters',
            'providers',
            'views',
        ])->loadCount('allComments');
        $data = $this->transformer->transform($pinboard);

        return $this->sendResponse($data, __('models.pinboard.saved'));
    }

    /**
     * @SWG\Get(
     *      path="/pinboard/{id}",
     *      summary="Display the specified Pinboard",
     *      tags={"Listing"},
     *      description="Get Pinboard",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Pinboard",
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
     *                  ref="#/definitions/Pinboard"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     *
     *
     * @param $id
     * @param ShowRequest $request
     * @return mixed
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function show($id, ShowRequest $request)
    {
        /** @var Pinboard $pinboard */
        $pinboard = $this->pinboardRepository->with([
            'media',
            'user.resident',
            'likesCounter',
            'likes',
            'likes.user',
            'buildings.address.state',
            'buildings.service_providers',
            'buildings.media',
            'quarters',
            'providers',
            'views',
        ])->withCount(['allComments', 'views'])->findWithoutFail($id);

        if (empty($pinboard)) {
            return $this->sendError(__('models.pinboard.errors.not_found'));
        }
        $pinboard->likers = $pinboard->collectLikers();
        $this->fixPinboardViews($pinboard);
        if ($pinboard->announcement) {
            $pinboard->load('announcement_email_receptionists');
        }
        $data = $this->transformer->transform($pinboard);
        return $this->sendResponse($data, 'Pinboard retrieved successfully');
    }

    /**
     * @param $pinboard
     */
    protected function fixPinboardViews($pinboard)
    {
        $residentId = Auth::user()->resident->id ?? null;
        if ($residentId) {
            $pinboardView = $pinboard->views->where('resident_id', $residentId)->first();
            if (empty($pinboardView)) {
                $pinboardView = $pinboard->views()->create(['resident_id' => Auth::user()->resident->id, 'views' => 1]);
                $pinboard->views->push($pinboardView);
            } else {
                $pinboardView->update(['views' => $pinboardView->views + 1]);
            }
        }
    }

    /**
     * @SWG\Put(
     *      path="/pinboard/{id}",
     *      summary="Update the specified Pinboard in storage",
     *      tags={"Listing"},
     *      description="Update Pinboard",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Pinboard",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="Pinboard that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/Pinboard")
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
     *                  ref="#/definitions/Pinboard"
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
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function update($id, UpdateRequest $request)
    {
        $input = $request->only(Pinboard::Fillable);
        if ($request->announcement || $request->pinned) { // @TODO delete
            $input['type'] = Pinboard::TypeAnnouncement;
        } else {
            $input['type'] =  $input['type'] ?? Pinboard::TypePost;
        }

        $status = $request->get('status');

        /** @var Pinboard $pinboard */
        $pinboard = $this->pinboardRepository->findWithoutFail($id);

        if (empty($pinboard)) {
            return $this->sendError(__('models.pinboard.errors.not_found'));
        }

        $this->pinboardRepository->updateExisting($pinboard, $input);
        $pinboard = $this->pinboardRepository->with([
            'media',
            'user.resident',
            'likesCounter',
            'likes',
            'likes.user',
            'buildings.address.state',
            'buildings.service_providers',
            'buildings.media',
            'quarters',
            'providers',
            'views',
        ])->withCount('allComments')->findWithoutFail($id);
        $pinboard->status = $status;
        $data = $this->transformer->transform($pinboard);
        return $this->sendResponse($data, __('models.pinboard.saved'));
    }

    /**
     * @SWG\Delete(
     *      path="/pinboard/{id}",
     *      summary="Remove the specified Pinboard from storage",
     *      tags={"Listing"},
     *      description="Delete Pinboard",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Pinboard",
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
     * @param DeleteRequest $request
     * @return mixed
     * @throws \Exception
     */
    public function destroy($id, DeleteRequest $request)
    {
        /** @var Pinboard $pinboard */
        $pinboard = $this->pinboardRepository->findWithoutFail($id);

        if (empty($pinboard)) {
            return $this->sendError(__('models.pinboard.errors.not_found'));
        }

        $pinboard->delete();

        return $this->sendResponse($id, __('models.pinboard.deleted'));
    }

    /**
     * @param DeleteRequest $request
     * @return mixed
     */
    public function destroyWithIds(DeleteRequest $request){
        $ids = $request->get('ids');
        try{
            Pinboard::destroy($ids);
        }
        catch (\Exception $e) {
            return $this->sendError(__('models.pinboard.errors.deleted') . $e->getMessage());
        }
        return $this->sendResponse($ids, __('models.pinboard.deleted'));
    }

    /**
     * @SWG\Post(
     *      path="/pinboard/{id}/publish",
     *      summary="Publish a pinboard",
     *      tags={"Listing"},
     *      description="Publish a pinboard",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Pinboard",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="status",
     *          in="body",
     *          type="integer",
     *          format="int32",
     *          description="The new status of the pinboard",
     *          required=true,
     *          @SWG\Schema(ref="#/definitions/Pinboard")
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
     *                  ref="#/definitions/Pinboard"
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
     * @param PublishRequest $request
     * @return mixed
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function publish($id, PublishRequest $request)
    {
        $newStatus = $request->get('status');
        $pinboard = $this->pinboardRepository->findWithoutFail($id);
        if (empty($pinboard)) {
            return $this->sendError(__('models.pinboard.errors.not_found'));
        }

        $pinboard = $this->pinboardRepository->setStatusExisting($pinboard, $newStatus, Carbon::now());

        return $this->sendResponse($pinboard, __('general.status_changed'));
    }

    /**
     * @SWG\Post(
     *      path="/pinboard/{id}/like",
     *      summary="Like a pinboard",
     *      tags={"Listing"},
     *      description="Like a pinboard",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Pinboard",
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
     *                  type="object",
     *                  description="logged in user"
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
     * @param LikeRequest $r
     * @return mixed
     */
    public function like($id, LikeRequest $r)
    {
        $pinboard = $this->pinboardRepository->findWithoutFail($id);
        if (empty($pinboard)) {
            return $this->sendError(__('models.pinboard.errors.not_found'));
        }

        $u = \Auth::user();
        $u->like($pinboard);

        // if logged in user is resident and
        // author of pinboard is resident and
        // author of pinboard is different than liker
        if ($u->resident && $pinboard->user->resident && $u->id != $pinboard->user_id) {
            $pinboard->user->notify(new PinboardLiked($pinboard, $u->resident));
        }
        return $this->sendResponse($this->uTransformer->transform($u),
        __('models.pinboard.liked'));
    }

    /**
     * @SWG\Post(
     *      path="/pinboard/{id}/unlike",
     *      summary="Unlike a pinboard",
     *      tags={"Listing"},
     *      description="Unlike a pinboard",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Pinboard",
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
     *                  type="integer",
     *                  description="count of likes for the pinboard"
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
     * @param LikeRequest $r
     * @return mixed
     */
    public function unlike($id, LikeRequest $r)
    {
        $pinboard = $this->pinboardRepository->findWithoutFail($id);
        if (empty($pinboard)) {
            return $this->sendError(__('models.pinboard.errors.not_found'));
        }

        $u = \Auth::user();
        $u->unlike($pinboard);
        return $this->sendResponse($this->uTransformer->transform($u),
        __('models.pinboard.unliked'));
    }

    /**
     * @SWG\Post(
     *      path="/pinboard/{id}/buildings/{bid}",
     *      summary="Assign the provided building to the pinboard",
     *      tags={"Listing"},
     *      description="Assign the provided building to the pinboard",
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
     *                  ref="#/definitions/Pinboard"
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
     * @param int $bid
     * @param BuildingRepository $bRepo
     * @param AssignRequest $r
     * @return mixed
     */
    public function assignBuilding(int $id, int $bid, BuildingRepository $bRepo, AssignRequest $r)
    {
        $p = $this->pinboardRepository->findWithoutFail($id);
        if (empty($p)) {
            return $this->sendError(__('models.pinboard.errors.not_found'));
        }
        $b = $bRepo->findWithoutFail($bid);
        if (empty($b)) {
            return $this->sendError(__('models.pinboard.errors.building_not_found'));
        }

        $p->buildings()->sync($b, false);
        $p = $this->pinboardRepository->with([
            'media',
            'user.resident',
            'likesCounter',
            'likes',
            'likes.user',
            'buildings.address.state',
            'buildings.service_providers',
            'buildings.media',
            'quarters',
        ])->withCount('allComments')->findWithoutFail($id);
        $p->likers = $p->collectLikers();


        return $this->sendResponse($p, __('general.attached.building'));
    }

    /**
     * @SWG\Delete(
     *      path="/pinboard/{id}/buildings/{bid}",
     *      summary="Unassign the provided building to the pinboard",
     *      tags={"Listing"},
     *      description="Unassign the provided building to the pinboard",
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
     *                  ref="#/definitions/Pinboard"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     * @param int $id
     * @param int $bid
     * @param BuildingRepository $bRepo
     * @param UnAssignRequest $r
     * @return Response
     */
    public function unassignBuilding(int $id, int $bid, BuildingRepository $bRepo, UnAssignRequest $r)
    {
        $p = $this->pinboardRepository->findWithoutFail($id);
        if (empty($p)) {
            return $this->sendError(__('models.pinboard.errors.not_found'));
        }
        $b = $bRepo->findWithoutFail($bid);
        if (empty($b)) {
            return $this->sendError(__('models.pinboard.errors.building_not_found'));
        }

        $p->buildings()->detach($b);
        $p = $this->pinboardRepository->with([
            'media',
            'user.resident',
            'likesCounter',
            'likes',
            'likes.user',
            'buildings.address.state',
            'buildings.service_providers',
            'buildings.media',
            'quarters',
        ])->withCount('allComments')->findWithoutFail($id);
        $p->likers = $p->collectLikers();


        return $this->sendResponse($p, __('general.detached.building'));
    }

    /**
     * @SWG\Post(
     *      path="/pinboard/{id}/quarters/{did}",
     *      summary="Assign the provided quarter to the pinboard",
     *      tags={"Listing"},
     *      description="Assign the provided quarter to the pinboard",
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
     *                  ref="#/definitions/Pinboard"
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
     * @param int $qid
     * @param QuarterRepository $qRepo
     * @param AssignRequest $r
     * @return Response
     */
    public function assignQuarter(int $id, int $qid, QuarterRepository $qRepo, AssignRequest $r)
    {
        $p = $this->pinboardRepository->findWithoutFail($id);
        if (empty($p)) {
            return $this->sendError(__('models.pinboard.errors.not_found'));
        }
        $d = $qRepo->findWithoutFail($qid);
        if (empty($d)) {
            return $this->sendError(__('models.pinboard.errors.quarter_not_found'));
        }

        $p->quarters()->sync($d, false);
        $p = $this->pinboardRepository->with([
            'media',
            'user.resident',
            'likesCounter',
            'likes',
            'likes.user',
            'buildings.address.state',
            'buildings.service_providers',
            'buildings.media',
            'quarters',
        ])->withCount('allComments')->findWithoutFail($id);
        $p->likers = $p->collectLikers();


        return $this->sendResponse($p, __('general.attached.quarter'));
    }

    /**
     * @SWG\Delete(
     *      path="/pinboard/{id}/quarters/{did}",
     *      summary="Unassign the provided quarter to the pinboard",
     *      tags={"Listing"},
     *      description="Unassign the provided quarter to the pinboard",
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
     *                  ref="#/definitions/Pinboard"
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
     * @param int $qid
     * @param QuarterRepository $qRepo
     * @param UnAssignRequest $r
     * @return Response
     */
    public function unassignQuarter(int $id, int $qid, QuarterRepository $qRepo, UnAssignRequest $r)
    {
        $p = $this->pinboardRepository->findWithoutFail($id);
        if (empty($p)) {
            return $this->sendError(__('models.pinboard.errors.not_found'));
        }
        $d = $qRepo->findWithoutFail($qid);
        if (empty($d)) {
            return $this->sendError(__('models.pinboard.errors.building_not_found'));
        }

        $p->quarters()->detach($d);
        $p = $this->pinboardRepository->with([
            'media',
            'user.resident',
            'likesCounter',
            'likes',
            'likes.user',
            'buildings.address.state',
            'buildings.service_providers',
            'buildings.media',
            'quarters',
        ])->withCount('allComments')->findWithoutFail($id);
        $p->likers = $p->collectLikers();


        return $this->sendResponse($p, __('general.detached.quarter'));
    }

    /**
     * @SWG\Get(
     *      path="/pinboard/{id}/locations",
     *      summary="Get a listing of the pinboard locations.",
     *      tags={"Listing"},
     *      description="Get a listing of the pinboard locations.",
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
     *                  @SWG\Items(ref="#/definitions/Pinboard")
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
     * @param ViewRequest $request
     * @return Response
     * @throws \Exception
     */
    public function getLocations(int $id, ViewRequest $request)
    {
        $p = $this->pinboardRepository->findWithoutFail($id);
        if (empty($p)) {
            return $this->sendError(__('models.pinboard.errors.not_found'));
        }

        $perPage = $request->get('per_page', env('APP_PAGINATE', 10));
        $locations = $this->pinboardRepository->locations($p)->paginate($perPage);
        return $this->sendResponse($locations, 'Locations retrieved successfully');
    }

    /**
     * @SWG\Post(
     *      path="/pinboard/{id}/providers/{pid}",
     *      summary="Assign the provided service provider to the pinboard",
     *      tags={"Listing"},
     *      description="Assign the provided service provider to the pinboard",
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
     *                  ref="#/definitions/Pinboard"
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
     * @param int $pid
     * @param ServiceProviderRepository $pRepo
     * @param AssignRequest $r
     * @return Response
     */
    public function assignProvider(int $id, int $pid, ServiceProviderRepository $pRepo, AssignRequest $r)
    {
        $p = $this->pinboardRepository->findWithoutFail($id);
        if (empty($p)) {
            return $this->sendError(__('models.pinboard.errors.not_found'));
        }
        $provider = $pRepo->findWithoutFail($pid);
        if (empty($provider)) {
            return $this->sendError(__('models.pinboard.errors.provider_not_found'));
        }

        $p->providers()->sync($provider, false);
        $p = $this->pinboardRepository->with([
            'media',
            'user.resident',
            'likesCounter',
            'likes',
            'likes.user',
            'buildings.address.state',
            'buildings.service_providers',
            'buildings.media',
            'quarters',
            'providers',
        ])->withCount('allComments')->findWithoutFail($id);
        $p->likers = $p->collectLikers();


        return $this->sendResponse($p, __('general.attached.provider'));
    }

    /**
     * @SWG\Delete(
     *      path="/pinboard/{id}/providers/{pid}",
     *      summary="Unassign the provided service provider to the pinboard",
     *      tags={"Listing"},
     *      description="Unassign the provided service provider to the pinboard",
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
     *                  ref="#/definitions/Pinboard"
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
     * @param int $pid
     * @param ServiceProviderRepository $pRepo
     * @param UnAssignRequest $r
     * @return mixed
     */
    public function unassignProvider(int $id, int $pid, ServiceProviderRepository $pRepo, UnAssignRequest $r)
    {
        $p = $this->pinboardRepository->findWithoutFail($id);
        if (empty($p)) {
            return $this->sendError(__('models.pinboard.errors.not_found'));
        }
        $provider = $pRepo->findWithoutFail($pid);
        if (empty($provider)) {
            return $this->sendError(__('models.pinboard.errors.provider_not_found'));
        }

        $p->providers()->detach($provider);
        $p = $this->pinboardRepository->with([
            'media',
            'user.resident',
            'likesCounter',
            'likes',
            'likes.user',
            'buildings.address.state',
            'buildings.service_providers',
            'buildings.media',
            'quarters',
            'providers',
        ])->withCount('allComments')->findWithoutFail($id);
        $p->likers = $p->collectLikers();

        return $this->sendResponse($p, __('general.detached.service'));
    }

    /**
     * @SWG\Put(
     *      path="/pinboard/{id}/views",
     *      summary="Increment the view count of the pinboard",
     *      tags={"Listing"},
     *      description="Increment the view count of the pinboard",
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
     *                  ref="#/definitions/Pinboard"
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
     * @return mixed
     */
    public function incrementViews(int $id)
    {
        $p = $this->pinboardRepository->findWithoutFail($id);
        if (empty($p)) {
            return $this->sendError(__('models.pinboard.errors.not_found'));
        }

        $p->incrementViews(\Auth::id());
        return $this->sendResponse($id, 'Views increased successfully');
    }

    /**
     * @SWG\Get(
     *      path="/pinboard/{id}/views",
     *      summary="List the view count of the pinboard",
     *      tags={"Listing"},
     *      description="List the view count of the pinboard",
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
     *                  ref="#/definitions/PinboardView"
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
     * @param PinboardViewTransformer $pvt
     * @param ListViewsRequest $req
     * @return mixed
     */
    public function indexViews(int $id, PinboardViewTransformer $pvt, ListViewsRequest $req)
    {
        $p = $this->pinboardRepository->findWithoutFail($id);
        if (empty($p)) {
            return $this->sendError(__('models.pinboard.errors.not_found'));
        }

        $perPage = $req->get('per_page', env('APP_PAGINATE', 10));
        $vs = $p->views()->with('user')->paginate($perPage);
        $ret = $pvt->transformPaginator($vs);
        return $this->sendResponse($ret, 'Views retrieved successfully');
    }

    /**
     * @SWG\Get(
     *      path="/pinboard/rss.xml",
     *      summary="RSS feed of pinboard",
     *      tags={"RSS"},
     *      description="Get RSS feed of pinboard",
     *      produces={"application/xml"},
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation"
     *      )
     * )
     * @return \Illuminate\Contracts\Routing\ResponseFactory|Response
     */
    public function showNewsRSS()
    {
        $feed = Cache::remember('rss_feed', 600, function() {
            return file_get_contents("https://www.blick.ch/news/schweiz/rss.xml");
        });

        return response($feed, 200, [
            'Content-Type' => 'application/xml'
        ]);
    }

    /**
     * @SWG\Get(
     *      path="/pinboard/weather.json",
     *      summary="JSON feed of weather",
     *      tags={"weather"},
     *      description="Get json feed of weather",
     *      produces={"application/json"},
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation"
     *      )
     * )
     *
     * @param SettingsRepository $settingsRepository
     * @return \Illuminate\Contracts\Routing\ResponseFactory|Response
     */
    public function showWeatherJSON(SettingsRepository $settingsRepository)
    {
        $zip = $this->getZip($settingsRepository);
        $feed = Cache::remember('weather_at_' . $zip, 60*60, function() use ($settingsRepository, $zip) {
            $appid = env('OPENWEATHERMAP_API_KEY');
            $countryCode = env('OPENWEATHERMAP_COUNTRY_CODE', 'ch');
            $zipCountry = $zip . ',' . $countryCode;
            $url = "http://api.openweathermap.org/data/2.5/weather?zip=" . $zipCountry . "&appid=" . $appid;

            return file_get_contents($url);
        });

        return response($feed, 200, [
            'Content-Type' => 'application/json'
        ]);
    }

    /**
     * @param $settingRepository
     * @return int
     */
    private function getZip($settingRepository)
    {
        $u = \Auth::user();
        if ($u->resident && $u->resident->address && $u->resident->address->zip) {
            return $u->resident->address->zip;
        }
        $defaultZip = 3172;
        $settings = $settingRepository->first();
        if (empty($settings)) {
            return $defaultZip;
        }
        if (!isset($settings->address)) {
            return $defaultZip;
        }

        return $settings->address->zip;
    }
}
