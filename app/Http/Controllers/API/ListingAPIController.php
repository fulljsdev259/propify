<?php

namespace App\Http\Controllers\API;

use App\Criteria\Common\RequestCriteria;
use App\Criteria\Listing\FilterByResidentCriteria;
use App\Criteria\Listing\FilterByTypeCriteria;
use App\Criteria\Listing\FilterByUserCriteria;
use App\Criteria\Listing\FilterByStatusCriteria;
use App\Criteria\Listing\FilterByQuarterCriteria;
use App\Http\Requests\API\Listing\LikeRequest;
use App\Http\Requests\API\Listing\ListRequest;
use App\Notifications\ListingLiked;
use App\Http\Controllers\AppBaseController;
use App\Http\Requests\API\Listing\CreateRequest;
use App\Http\Requests\API\Listing\DeleteRequest;
use App\Http\Requests\API\Listing\PublishRequest;
use App\Http\Requests\API\Listing\UpdateRequest;
use App\Http\Requests\API\Listing\ViewRequest;
use App\Models\Listing;
use App\Repositories\ListingRepository;
use App\Repositories\SettingsRepository;
use App\Transformers\ListingTransformer;
use App\Transformers\UserTransformer;
use Illuminate\Http\Response;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;

/**
 * Class ListingAPIController
 * @package App\Http\Controllers\API
 */
class ListingAPIController extends AppBaseController
{
    /** @var  ListingRepository */
    private $listingRepository;
    /**
     * @var SettingsRepository
     */
    private $settingsRepository;
    /**
     * @var ListingTransformer
     */
    private $transformer;
    /**
     * @var UserTransformer
     */
    private $uTransformer;

    /**
     * ListingAPIController constructor.
     * @param ListingRepository $listingRepository
     * @param SettingsRepository $settingsRepository
     * @param ListingTransformer $listingTransformer
     * @param UserTransformer $userTransformer
     */
    public function __construct(
        ListingRepository $listingRepository,
        SettingsRepository $settingsRepository,
        ListingTransformer $listingTransformer,
        UserTransformer $userTransformer
    )
    {
        $this->listingRepository = $listingRepository;
        $this->settingsRepository = $settingsRepository;
        $this->transformer = $listingTransformer;
        $this->uTransformer = $userTransformer;
    }

    /**
     * @SWG\Get(
     *      path="/listings",
     *      summary="Get a listing of the Listings.",
     *      tags={"Listing"},
     *      description="Get all Listings",
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
     *                  @SWG\Items(ref="#/definitions/Listing")
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
        $this->listingRepository->pushCriteria(new RequestCriteria($request));
        $this->listingRepository->pushCriteria(new LimitOffsetCriteria($request));
        $this->listingRepository->pushCriteria(new FilterByResidentCriteria($request));
        $this->listingRepository->pushCriteria(new FilterByUserCriteria($request));
        $this->listingRepository->pushCriteria(new FilterByTypeCriteria($request));
        $this->listingRepository->pushCriteria(new FilterByStatusCriteria($request));
        $this->listingRepository->pushCriteria(new FilterByQuarterCriteria($request));
        
        $perPage = $request->get('per_page', env('APP_PAGINATE', 10));
        $listings = $this->listingRepository->with([
            'media',
            'user.resident',
            'likesCounter',
            'likes',
            'likes.user',
        ])->orderBy('published_at', 'desc')->orderBy('created_at', 'desc')
            ->paginate($perPage);
        $listings->getCollection()->loadCount('allComments');

        $out = $this->transformer->transformPaginator($listings);
        return $this->sendResponse($out, 'Listings retrieved successfully');
    }

    /**
     * @SWG\Post(
     *      path="/listings",
     *      summary="Store a newly created Listing in storage",
     *      tags={"Listing"},
     *      description="Store Listing",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="Listing that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/Listing")
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
     *                  ref="#/definitions/Listing"
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
        $input = $request->all();
        $input['user_id'] = \Auth::id();
        $input['status'] = Listing::StatusUnpublished;

        $settings = $this->settingsRepository->first();
        $input['needs_approval'] = false;
        if ($settings) {
            $input['needs_approval'] = $settings->listing_approval_enable;
        }

        $listing = $this->listingRepository->create($input);

        $data = $this->transformer->transform($listing);
        return $this->sendResponse($data, __('models.listing.saved'));
    }

    /**
     * @SWG\Get(
     *      path="/listings/{id}",
     *      summary="Display the specified Listing",
     *      tags={"Listing"},
     *      description="Get Listing",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Listing",
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
     *                  ref="#/definitions/Listing"
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
     * @param ViewRequest $request
     * @return mixed
     */
    public function show($id, ViewRequest $request)
    {
        /** @var Listing $listing */
        $listing = $this->listingRepository->with([
            'media',
            'user.resident',
            'likesCounter',
            'likes',
            'likes.user',
        ])->withCount('allComments')->findWithoutFail($id);

        if (empty($listing)) {
            return $this->sendError(__('models.listing.errors.not_found'));
        }
        $listing->likers = $listing->collectLikers();

        $data = $this->transformer->transform($listing);
        return $this->sendResponse($data, 'Listing retrieved successfully');
    }

    /**
     * @SWG\Put(
     *      path="/listings/{id}",
     *      summary="Update the specified Listing in storage",
     *      tags={"Listing"},
     *      description="Update Listing",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Listing",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="Listing that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/Listing")
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
     *                  ref="#/definitions/Listing"
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
        $input = $request->only(Listing::Fillable);

        /** @var Listing $listing */
        $listing = $this->listingRepository->findWithoutFail($id);

        if (empty($listing)) {
            return $this->sendError(__('models.listing.errors.not_found'));
        }

        $listing = $this->listingRepository->update($input, $id);

        $data = $this->transformer->transform($listing);
        return $this->sendResponse($data, __('models.listing.saved'));
    }

    /**
     * @SWG\Delete(
     *      path="/listings/{id}",
     *      summary="Remove the specified Listing from storage",
     *      tags={"Listing"},
     *      description="Delete Listing",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Listing",
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
        /** @var Listing $listing */
        $listing = $this->listingRepository->findWithoutFail($id);

        if (empty($listing)) {
            return $this->sendError(__('models.listing.errors.not_found'));
        }

        $listing->delete();

        return $this->sendResponse($id, __('models.listing.deleted'));
    }

    /**
     * @param DeleteRequest $request
     * @return mixed
     */
    public function destroyWithIds(DeleteRequest $request){
        $ids = $request->get('ids');
        try{
            Listing::destroy($ids);
        }
        catch (\Exception $e) {
            return $this->sendError(__('models.listing.errors.deleted') . $e->getMessage());
        }
        return $this->sendResponse($ids, __('models.listing.deleted'));
    }

    /**
     * @SWG\Post(
     *      path="/listings/{id}/like",
     *      summary="Like a listing",
     *      tags={"Listing"},
     *      description="Like a Listing",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Listing",
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
     * @param LikeRequest $likeRequest
     * @return mixed
     */
    public function like($id, LikeRequest $likeRequest)
    {
        $listing = $this->listingRepository->findWithoutFail($id);
        if (empty($listing)) {
            return $this->sendError(__('models.listing.errors.not_found'));
        }

        $u = \Auth::user();
        $u->like($listing);

        // if logged in user is resident and
        // author of listing is resident and
        // author of listing is different than liker
        if ($u->resident && $listing->user->resident && $u->id != $listing->user_id) {
            $listing->user->notify(new ListingLiked($listing, $u->resident));
        }
        return $this->sendResponse($this->uTransformer->transform($u),
        __('models.listing.liked'));
    }

    /**
     * @SWG\Post(
     *      path="/listings/{id}/unlike",
     *      summary="Unlike a Listing",
     *      tags={"Listing"},
     *      description="Unlike a listing",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Listing",
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
     * @param LikeRequest $likeRequest
     * @return mixed
     */
    public function unlike($id, LikeRequest $likeRequest)
    {
        $listing = $this->listingRepository->findWithoutFail($id);
        if (empty($listing)) {
            return $this->sendError(__('models.listing.errors.not_found'));
        }

        $u = \Auth::user();
        $u->unlike($listing);
        return $this->sendResponse($this->uTransformer->transform($u),
        __('models.listing.unliked'));
    }

    /**
     * @SWG\Post(
     *      path="/listings/{id}/publish",
     *      summary="Publish a listing",
     *      tags={"Listing"},
     *      description="Publish a listing",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Listing",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="status",
     *          in="body",
     *          type="integer",
     *          format="int32",
     *          description="The new status of the listing",
     *          required=true,
     *          @SWG\Schema(ref="#/definitions/Listing")
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
     *                  ref="#/definitions/Listing"
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
     */
    public function publish($id, PublishRequest $request)
    {
        $newStatus = $request->get('status');
        $listing = $this->listingRepository->findWithoutFail($id);
        if (empty($listing)) {
            return $this->sendError(__('models.listing.errors.not_found'));
        }

        $listing = $this->listingRepository->setStatusExisting($listing, $newStatus);

        return $this->sendResponse($id, __('general.status_changed'));
    }
}
