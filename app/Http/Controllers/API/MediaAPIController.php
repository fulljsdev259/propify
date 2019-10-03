<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\AppBaseController;
use App\Http\Requests\API\Media\BuildingDeleteRequest;
use App\Http\Requests\API\Media\BuildingUploadRequest;
use App\Http\Requests\API\Media\PinboardDeleteRequest;
use App\Http\Requests\API\Media\PinboardUploadRequest;
use App\Http\Requests\API\Media\ListingDeleteRequest;
use App\Http\Requests\API\Media\ListingUploadRequest;
use App\Http\Requests\API\Media\RequestDeleteRequest;
use App\Http\Requests\API\Media\RequestUploadRequest;
use App\Http\Requests\API\Media\TenantDeleteRequest;
use App\Http\Requests\API\Media\RentContractDeleteRequest;
use App\Http\Requests\API\Media\RentContractUploadRequest;
use App\Http\Requests\API\Media\TenantUploadRequest;
use App\Models\Building;
use App\Repositories\AddressRepository;
use App\Repositories\BuildingRepository;
use App\Repositories\PinboardRepository;
use App\Repositories\ListingRepository;
use App\Repositories\RequestRepository;
use App\Repositories\RentContractRepository;
use App\Repositories\TenantRepository;
use App\Transformers\MediaTransformer;
use Illuminate\Http\Response;

/**
 * Class MediaController
 * @package App\Http\Controllers\API
 */
class MediaAPIController extends AppBaseController
{
    /** @var  BuildingRepository */
    private $buildingRepository;

    /** @var  AddressRepository */
    private $addressRepository;

    /** @var  PinboardRepository */
    private $pinboardRepository;

    /** @var  ListingRepository */
    private $listingRepository;

    /** @var  TenantRepository */
    private $tenantRepository;

    /**
     * @var RentContractRepository
     */
    private $rentContractRepository;

    /** @var  RequestRepository */
    private $requestRepository;

    /**
     * MediaAPIController constructor.
     * @param BuildingRepository $buildingRepo
     * @param AddressRepository $addrRepo
     * @param PinboardRepository $pinboardRepo
     * @param ListingRepository $listingRepo
     * @param TenantRepository $tenantRepo
     * @param RentContractRepository $rentContractRepository
     * @param RequestRepository $requestRepo
     */
    public function __construct(
        BuildingRepository $buildingRepo,
        AddressRepository $addrRepo,
        PinboardRepository $pinboardRepo,
        ListingRepository $listingRepo,
        TenantRepository $tenantRepo,
        RentContractRepository $rentContractRepository,
        RequestRepository $requestRepo
    )
    {
        $this->buildingRepository = $buildingRepo;
        $this->addressRepository = $addrRepo;
        $this->pinboardRepository = $pinboardRepo;
        $this->listingRepository = $listingRepo;
        $this->tenantRepository = $tenantRepo;
        $this->requestRepository = $requestRepo;
        $this->rentContractRepository = $rentContractRepository;
    }

    /**
     * @SWG\Post(
     *      path="/buildings/{building_id}/media",
     *      summary="Store a newly created Building Media in storage",
     *      tags={"Building"},
     *      description="Store Media",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="Media that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/Building")
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
     *                  ref="#/definitions/Building"
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
     * @param BuildingUploadRequest $request
     * @return Response
     */
    public function buildingUpload(int $id, BuildingUploadRequest $request)
    {
        /** @var Building $building */
        $building = $this->buildingRepository->findWithoutFail($id);
        if (empty($building)) {
            return $this->sendError(__('models.building.not_found'));
        }

        Building::BuildingMediaCategories;
        $collectionName = '';
        $data = '';
        foreach (Building::BuildingMediaCategories as $mediaCategory) {
            if ($request->has($mediaCategory . '_upload')) {
                $collectionName = $mediaCategory;
                $data = $request->get($mediaCategory . '_upload', '');
            }
        }

        if (!$media = $this->buildingRepository->uploadFile($collectionName, $data, $building, $request->merge_in_audit)) {
            return $this->sendError(__('general.upload_error'));
        }

        $response = (new MediaTransformer)->transform($media);
        return $this->sendResponse($response, __('models.building.document.uploaded'));
    }

    /**
     * @SWG\Delete(
     *      path="/building/{building_id}/media/{media_id}",
     *      summary="Remove the specified Media from storage",
     *      tags={"Building"},
     *      description="Delete Media",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Media",
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
     * @param int $building_id
     * @param int $media_id
     * @param BuildingDeleteRequest $r
     * @return Response
     */
    public function buildingDestroy(int $building_id, int $media_id, BuildingDeleteRequest $r)
    {
        /** @var Building $building */
        $building = $this->buildingRepository->with('media')->findWithoutFail($building_id);
        if (empty($building)) {
            return $this->sendError(__('models.building.not_found'));
        }

        $media = $building->media->find($media_id);
        if (empty($media)) {
            return $this->sendError(__('general.media_not_found'));
        }

        $media->delete();

        return $this->sendResponse($media_id, __('models.building.document.deleted'));
    }

    /**
     * @SWG\Post(
     *      path="/pinboard/{pinboard_id}/media",
     *      summary="Store a newly created Pinboard Media in storage",
     *      tags={"Listing"},
     *      description="Store Media",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="Media that should be stored",
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
     * @param int $id
     * @param PinboardUploadRequest $request
     * @return Response
     */
    public function pinboardUpload(int $id, PinboardUploadRequest $request)
    {
        $pinboard = $this->pinboardRepository->findWithoutFail($id);
        if (empty($pinboard)) {
            return $this->sendError(__('models.building.not_found'));
        }

        $data = $request->get('media', '');
        if (!$media = $this->pinboardRepository->uploadFile('media', $data, $pinboard, $request->merge_in_audit)) {
            return $this->sendError(__('general.upload_error'));
        }

        $response = (new MediaTransformer)->transform($media);
        return $this->sendResponse($response, __('general.swal.media.added'));
    }

    /**
     * @SWG\Delete(
     *      path="/pinboard/{pinboard_id}/media/{media_id}",
     *      summary="Remove the specified Media from storage",
     *      tags={"Listing"},
     *      description="Delete Media",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Media",
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
     * @param int $media_id
     * @param PinboardDeleteRequest $r
     * @return Response
     */
    public function pinboardDestroy(int $id, int $media_id, PinboardDeleteRequest $r)
    {
        $pinboard = $this->pinboardRepository->findWithoutFail($id);
        if (empty($pinboard)) {
            return $this->sendError(__('models.pinboard.errors.not_found'));
        }

        $media = $pinboard->media->find($media_id);
        if (empty($media)) {
            return $this->sendError(__('general.media_not_found'));
        }

        $media->delete();

        return $this->sendResponse($media_id, __('general.swal.media.deleted'));
    }

    /**
     * @SWG\Post(
     *      path="/tenants/{tenant_id}/media",
     *      summary="Store a newly created Tenant Media in storage",
     *      tags={"Tenant"},
     *      description="Store Media",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="Media that should be stored",
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
     *                  ref="#/definitions/Tenant"
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
     * @param TenantUploadRequest $request
     * @return mixed
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function tenantUpload(int $id, TenantUploadRequest $request)
    {
        $tenant = $this->tenantRepository->findWithoutFail($id);
        if (empty($tenant)) {
            return $this->sendError(__('models.tenant.errors.not_found'));
        }

        //@TODO tmp solution
        $tenant->load('rent_contracts');
        $rentContract = $tenant->rent_contracts->first();
        if (empty($rentContract)) {
            $rentContract = $this->rentContractRepository->create(['tenant_id' => $tenant->id]);
        }

        $data = $request->get('media', '');
        if (!$media = $this->rentContractRepository->uploadFile('media', $data, $rentContract, $request->merge_in_audit)) {
            return $this->sendError(__('general.upload_error'));
        }

        $data = $request->get('media', '');
        if (!$media = $this->tenantRepository->uploadFile('media', $data, $tenant)) {
            return $this->sendError(__('general.upload_error'));
        }

        $response = (new MediaTransformer)->transform($media);
        return $this->sendResponse($response, __('general.swal.media.added'));
    }


    /**
     * @SWG\Delete(
     *      path="/tenants/{id}/media/{media_id}",
     *      summary="Remove the specified Media from storage",
     *      tags={"Tenant"},
     *      description="Delete Media",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Media",
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
     * @param int $media_id
     * @param TenantDeleteRequest $r
     * @return Response
     */
    public function tenantDestroy(int $id, int $media_id, TenantDeleteRequest $r)
    {
        $tenant = $this->tenantRepository->findWithoutFail($id);
        if (empty($tenant)) {
            return $this->sendError(__('models.pinboard.errors.not_found'));
        }

        $media = $tenant->media->find($media_id);
        if (empty($media)) {
            return $this->sendError(__('general.media_not_found'));
        }

        $media->delete();

        return $this->sendResponse($media_id, __('general.swal.media.deleted'));
    }

    /**
     * @SWG\Post(
     *      path="/rent-contracts/{id}/media",
     *      summary="Store a newly created RentContract Media in storage",
     *      tags={"RentContract"},
     *      description="Store Media",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="Media that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/Media")
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
     * @param int $id
     * @param RentContractUploadRequest $request
     * @return Response
     */
    public function rentContractUpload(int $id, RentContractUploadRequest $request)
    {
        $rentContract = $this->rentContractRepository->findWithoutFail($id);
        if (empty($rentContract)) {
            return $this->sendError(__('models.rent_contract.errors.not_found'));
        }

        $data = $request->get('media', '');
        if (!$media = $this->rentContractRepository->uploadFile('media', $data, $rentContract, $request->merge_in_audit)) {
            return $this->sendError(__('general.upload_error'));
        }

        $response = (new MediaTransformer)->transform($media);
        return $this->sendResponse($response, __('general.swal.media.added'));
    }

    /**
     * @SWG\Delete(
     *      path="/rent-contracts/{id}/media/{media_id}",
     *      summary="Remove the specified Media from storage",
     *      tags={"RentContract"},
     *      description="Delete Media",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Media",
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
     * @param int $media_id
     * @param RentContractDeleteRequest $r
     * @return Response
     */
    public function rentContractDestroy(int $id, int $media_id, RentContractDeleteRequest $r)
    {
        $rentContract = $this->rentContractRepository->findWithoutFail($id);
        if (empty($rentContract)) {
            return $this->sendError(__('models.rent_contract.errors.not_found'));
        }

        $media = $rentContract->media->find($media_id);
        if (empty($media)) {
            return $this->sendError(__('general.media_not_found'));
        }

        $media->delete();

        return $this->sendResponse($media_id, __('general.swal.media.deleted'));
    }

    /**
     * @SWG\Post(
     *      path="/requests/{id}/media",
     *      summary="Store a newly created Request Media in storage",
     *      tags={"Request"},
     *      description="Store Media",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="Media that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/Request")
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
     *                  ref="#/definitions/Request"
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
     * @param RequestUploadRequest $request
     * @return Response
     */
    public function requestUpload(int $id, RequestUploadRequest $request)
    {
        $request = $this->requestRepository->findWithoutFail($id);
        if (empty($request)) {
            return $this->sendError(__('models.request.errors.not_found'));
        }

        $data = $request->get('media', '');
        if (!$media = $this->requestRepository->uploadFile('media', $data, $request, $request->merge_in_audit)) {
            return $this->sendError(__('general.upload_error'));
        }
        $request->touch();
        $this->requestRepository->notifyMedia($request, \Auth::user(), $media);
        $response = (new MediaTransformer)->transform($media);
        return $this->sendResponse($response, __('general.swal.media.added'));
    }

    /**
     * @SWG\Delete(
     *      path="/requests/{id}/media/{media_id}",
     *      summary="Remove the specified Media from storage",
     *      tags={"Request"},
     *      description="Delete Media",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Media",
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
     * @param int $media_id
     * @param RequestDeleteRequest $r
     * @return Response
     */
    public function requestDestroy(int $id, int $media_id, RequestDeleteRequest $r)
    {
        $request = $this->requestRepository->findWithoutFail($id);
        if (empty($request)) {
            return $this->sendError(__('models.request.errors.not_found'));
        }

        $media = $request->media->find($media_id);
        if (empty($media)) {
            return $this->sendError(__('general.media_not_found'));
        }

        $media->delete();
        $request->touch();
        return $this->sendResponse($media_id, __('general.swal.media.deleted'));
    }

    /**
     * @SWG\Post(
     *      path="/listings/{listing_id}/media",
     *      summary="Store a newly created listing Media in storage",
     *      tags={"Listing"},
     *      description="Store Media",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="Media that should be stored",
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
     *
     * @param int $id
     * @param ListingUploadRequest $request
     * @return Response
     */
    public function listingUpload(int $id, ListingUploadRequest $request)
    {
        $listing = $this->listingRepository->findWithoutFail($id);
        if (empty($listing)) {
            return $this->sendError(__('models.building.not_found'));
        }

        $data = $request->get('media', '');
        if (!$media = $this->listingRepository->uploadFile('media', $data, $listing, $request->merge_in_audit)) {
            return $this->sendError(__('general.upload_error'));
        }

        $response = (new MediaTransformer)->transform($media);
        return $this->sendResponse($response, __('general.swal.media.added'));
    }

    /**
     * @SWG\Delete(
     *      path="/listings/{listing_id}/media/{media_id}",
     *      summary="Remove the specified Media from storage",
     *      tags={"Listing"},
     *      description="Delete Media",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Media",
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
     * @param int $media_id
     * @param ListingDeleteRequest $r
     * @return Response
     *
     */
    public function listingDestroy(int $id, int $media_id, ListingDeleteRequest $r)
    {
        $listing = $this->listingRepository->findWithoutFail($id);
        if (empty($listing)) {
            return $this->sendError(__('models.listing.errors.not_found'));
        }

        $media = $listing->media->find($media_id);
        if (empty($media)) {
            return $this->sendError(__('general.media_not_found'));
        }

        $media->delete();

        return $this->sendResponse($media_id, __('general.swal.media.deleted'));
    }
}
