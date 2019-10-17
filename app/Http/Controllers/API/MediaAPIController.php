<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\AppBaseController;
use App\Http\Requests\API\Media\BuildingDeleteRequest;
use App\Http\Requests\API\Media\BuildingUploadRequest;
use App\Http\Requests\API\Media\UnitDeleteRequest;
use App\Http\Requests\API\Media\UnitUploadRequest;
use App\Http\Requests\API\Media\QuarterDeleteRequest;
use App\Http\Requests\API\Media\QuarterUploadRequest;
use App\Http\Requests\API\Media\PinboardDeleteRequest;
use App\Http\Requests\API\Media\PinboardUploadRequest;
use App\Http\Requests\API\Media\ListingDeleteRequest;
use App\Http\Requests\API\Media\ListingUploadRequest;
use App\Http\Requests\API\Media\RequestDeleteRequest;
use App\Http\Requests\API\Media\RequestUploadRequest;
use App\Http\Requests\API\Media\ResidentDeleteRequest;
use App\Http\Requests\API\Media\ContractDeleteRequest;
use App\Http\Requests\API\Media\ContractUploadRequest;
use App\Http\Requests\API\Media\ResidentUploadRequest;
use App\Models\Building;
use App\Repositories\AddressRepository;
use App\Repositories\BuildingRepository;
use App\Repositories\PinboardRepository;
use App\Repositories\ListingRepository;
use App\Repositories\QuarterRepository;
use App\Repositories\RequestRepository;
use App\Repositories\ContractRepository;
use App\Repositories\ResidentRepository;
use App\Repositories\UnitRepository;
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

    /** @var  UnitRepository */
    private $unitRepository;

    /** @var  QuarterRepository */
    private $quarterRepository;

    /** @var  AddressRepository */
    private $addressRepository;

    /** @var  PinboardRepository */
    private $pinboardRepository;

    /** @var  ListingRepository */
    private $listingRepository;

    /** @var  ResidentRepository */
    private $residentRepository;

    /**
     * @var ContractRepository
     */
    private $contractRepository;

    /** @var  RequestRepository */
    private $requestRepository;

    /**
     * MediaAPIController constructor.
     * @param BuildingRepository $buildingRepo
     * @param QuarterRepository $quarterRepo
     * @param UnitRepository $unitRepo
     * @param AddressRepository $addrRepo
     * @param PinboardRepository $pinboardRepo
     * @param ListingRepository $listingRepo
     * @param ResidentRepository $residentRepo
     * @param ContractRepository $contractRepository
     * @param RequestRepository $requestRepo
     */
    public function __construct(
        BuildingRepository $buildingRepo,
        QuarterRepository $quarterRepo,
        UnitRepository $unitRepo,
        AddressRepository $addrRepo,
        PinboardRepository $pinboardRepo,
        ListingRepository $listingRepo,
        ResidentRepository $residentRepo,
        ContractRepository $contractRepository,
        RequestRepository $requestRepo
    )
    {
        $this->buildingRepository = $buildingRepo;
        $this->unitRepository = $unitRepo;
        $this->quarterRepository = $quarterRepo;
        $this->addressRepository = $addrRepo;
        $this->pinboardRepository = $pinboardRepo;
        $this->listingRepository = $listingRepo;
        $this->residentRepository = $residentRepo;
        $this->requestRepository = $requestRepo;
        $this->contractRepository = $contractRepository;
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

        list($collectionName, $data) = $this->getFileRelated($request);
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
     *      path="/units/{unit_id}/media",
     *      summary="Store a newly created Unit Media in storage",
     *      tags={"Unit"},
     *      description="Store Media",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="Media that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/Unit")
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
     *                  ref="#/definitions/Unit"
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
     * @param UnitUploadRequest $request
     * @return Response
     */
    public function unitUpload(int $id, UnitUploadRequest $request)
    {
        /** @var Unit $unit */
        $unit = $this->unitRepository->findWithoutFail($id);
        if (empty($unit)) {
            return $this->sendError(__('models.unit.not_found'));
        }

        list($collectionName, $data) = $this->getFileRelated($request);
        if (!$media = $this->unitRepository->uploadFile($collectionName, $data, $unit, $request->merge_in_audit)) {
            return $this->sendError(__('general.upload_error'));
        }

        $response = (new MediaTransformer)->transform($media);
        return $this->sendResponse($response, __('models.unit.document.uploaded'));
    }

    /**
     * @SWG\Delete(
     *      path="/unit/{unit_id}/media/{media_id}",
     *      summary="Remove the specified Media from storage",
     *      tags={"Unit"},
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
     * @param int $unit_id
     * @param int $media_id
     * @param UnitDeleteRequest $r
     * @return Response
     */
    public function unitDestroy(int $unit_id, int $media_id, UnitDeleteRequest $r)
    {
        /** @var Unit $unit */
        $unit = $this->unitRepository->with('media')->findWithoutFail($unit_id);
        if (empty($unit)) {
            return $this->sendError(__('models.unit.not_found'));
        }

        $media = $unit->media->find($media_id);
        if (empty($media)) {
            return $this->sendError(__('general.media_not_found'));
        }

        $media->delete();

        return $this->sendResponse($media_id, __('models.unit.document.deleted'));
    }

    /**
     * @SWG\Post(
     *      path="/quarters/{quarter_id}/media",
     *      summary="Store a newly created Quarter Media in storage",
     *      tags={"Quarter"},
     *      description="Store Media",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="Media that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/Quarter")
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
     *                  ref="#/definitions/Quarter"
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
     * @param QuarterUploadRequest $request
     * @return Response
     */
    public function quarterUpload(int $id, QuarterUploadRequest $request)
    {
        /** @var Quarter $quarter */
        $quarter = $this->quarterRepository->findWithoutFail($id);
        if (empty($quarter)) {
            return $this->sendError(__('models.quarter.not_found'));
        }

        list($collectionName, $data) = $this->getFileRelated($request);
        if (!$media = $this->quarterRepository->uploadFile($collectionName, $data, $quarter, $request->merge_in_audit)) {
            return $this->sendError(__('general.upload_error'));
        }

        $response = (new MediaTransformer)->transform($media);
        return $this->sendResponse($response, __('models.quarter.document.uploaded'));
    }

    /**
     * @SWG\Delete(
     *      path="/quarter/{quarter_id}/media/{media_id}",
     *      summary="Remove the specified Media from storage",
     *      tags={"Quarter"},
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
     * @param int $quarter_id
     * @param int $media_id
     * @param QuarterDeleteRequest $r
     * @return Response
     */
    public function quarterDestroy(int $quarter_id, int $media_id, QuarterDeleteRequest $r)
    {
        /** @var Quarter $quarter */
        $quarter = $this->quarterRepository->with('media')->findWithoutFail($quarter_id);
        if (empty($quarter)) {
            return $this->sendError(__('models.quarter.not_found'));
        }

        $media = $quarter->media->find($media_id);
        if (empty($media)) {
            return $this->sendError(__('general.media_not_found'));
        }

        $media->delete();

        return $this->sendResponse($media_id, __('models.quarter.document.deleted'));
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
     *      path="/residents/{resident_id}/media",
     *      summary="Store a newly created Resident Media in storage",
     *      tags={"Resident"},
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
     *                  ref="#/definitions/Resident"
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
     * @param ResidentUploadRequest $request
     * @return mixed
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function residentUpload(int $id, ResidentUploadRequest $request)
    {
        $resident = $this->residentRepository->findWithoutFail($id);
        if (empty($resident)) {
            return $this->sendError(__('models.resident.errors.not_found'));
        }

        //@TODO tmp solution
        $resident->load('contracts');
        $contract = $resident->contracts->first();
        if (empty($contract)) {
            $contract = $this->contractRepository->create(['resident_id' => $resident->id]);
        }

        $data = $request->get('media', '');
        if (!$media = $this->contractRepository->uploadFile('media', $data, $contract, $request->merge_in_audit)) {
            return $this->sendError(__('general.upload_error'));
        }

        $data = $request->get('media', '');
        if (!$media = $this->residentRepository->uploadFile('media', $data, $resident)) {
            return $this->sendError(__('general.upload_error'));
        }

        $response = (new MediaTransformer)->transform($media);
        return $this->sendResponse($response, __('general.swal.media.added'));
    }


    /**
     * @SWG\Delete(
     *      path="/residents/{id}/media/{media_id}",
     *      summary="Remove the specified Media from storage",
     *      tags={"Resident"},
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
     * @param ResidentDeleteRequest $r
     * @return Response
     */
    public function residentDestroy(int $id, int $media_id, ResidentDeleteRequest $r)
    {
        $resident = $this->residentRepository->findWithoutFail($id);
        if (empty($resident)) {
            return $this->sendError(__('models.pinboard.errors.not_found'));
        }

        $media = $resident->media->find($media_id);
        if (empty($media)) {
            return $this->sendError(__('general.media_not_found'));
        }

        $media->delete();

        return $this->sendResponse($media_id, __('general.swal.media.deleted'));
    }

    /**
     * @SWG\Post(
     *      path="/contracts/{id}/media",
     *      summary="Store a newly created Contract Media in storage",
     *      tags={"Contract"},
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
     * @param int $id
     * @param ContractUploadRequest $request
     * @return Response
     */
    public function contractUpload(int $id, ContractUploadRequest $request)
    {
        $contract = $this->contractRepository->findWithoutFail($id);
        if (empty($contract)) {
            return $this->sendError(__('models.resident.contract.errors.not_found'));
        }

        $data = $request->get('media', '');
        if (!$media = $this->contractRepository->uploadFile('media', $data, $contract, $request->merge_in_audit)) {
            return $this->sendError(__('general.upload_error'));
        }

        $response = (new MediaTransformer)->transform($media);
        return $this->sendResponse($response, __('general.swal.media.added'));
    }

    /**
     * @SWG\Delete(
     *      path="/contracts/{id}/media/{media_id}",
     *      summary="Remove the specified Media from storage",
     *      tags={"Contract"},
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
     * @param ContractDeleteRequest $r
     * @return Response
     */
    public function contractDestroy(int $id, int $media_id, ContractDeleteRequest $r)
    {
        $contract = $this->contractRepository->findWithoutFail($id);
        if (empty($contract)) {
            return $this->sendError(__('models.resident.contract.errors.not_found'));
        }

        $media = $contract->media->find($media_id);
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
     * @param RequestUploadRequest $requestUploadRequest
     * @return Response
     */
    public function requestUpload(int $id, RequestUploadRequest $requestUploadRequest)
    {
        $request = $this->requestRepository->findWithoutFail($id);
        if (empty($request)) {
            return $this->sendError(__('models.request.errors.not_found'));
        }
        $data = $requestUploadRequest->get('media', '');
        if (!$media = $this->requestRepository->uploadFile('media', $data, $request, $requestUploadRequest->merge_in_audit)) {
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
     * @param int $mediaId
     * @param RequestDeleteRequest $r
     * @return Response
     */
    public function requestDestroy(int $id, int $mediaId, RequestDeleteRequest $r)
    {
        $request = $this->requestRepository->findWithoutFail($id);
        if (empty($request)) {
            return $this->sendError(__('models.request.errors.not_found'));
        }

        $media = $request->media->find($mediaId);
        if (empty($media)) {
            return $this->sendError(__('general.media_not_found'));
        }

        $media->delete();
        $request->touch();
        return $this->sendResponse($mediaId, __('general.swal.media.deleted'));
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

    /**
     * @param $request
     * @return array
     */
    protected function getFileRelated($request)
    {
        $fileCategories = \ConstFileCategories::MediaCategories;
        $collectionName = '';
        $data = '';
        foreach ($fileCategories as $mediaCategory) {
            if ($request->has($mediaCategory . '_upload')) {
                $collectionName = $mediaCategory;
                $data = $request->get($mediaCategory . '_upload', '');
            }
        }

        return [$collectionName, $data];
    }
}
