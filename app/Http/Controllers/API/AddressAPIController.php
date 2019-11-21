<?php

namespace App\Http\Controllers\API;

use App\Criteria\Address\FilterByRelatedCriteria;
use App\Criteria\Common\DistinctCriteria;
use App\Http\Controllers\AppBaseController;
use App\Http\Requests\API\Address\CreateRequest;
use App\Http\Requests\API\Address\DeleteRequest;
use App\Http\Requests\API\Address\ListRequest;
use App\Http\Requests\API\Address\UpdateRequest;
use App\Http\Requests\API\Address\ViewRequest;
use App\Models\Address;
use App\Models\Model;
use App\Repositories\AddressRepository;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;

/**
 * Class AddressController
 * @package App\Http\Controllers\API
 */
class AddressAPIController extends AppBaseController
{
    /** @var  AddressRepository */
    private $addressRepository;

    /**
     * AddressAPIController constructor.
     * @param AddressRepository $addressRepo
     */
    public function __construct(AddressRepository $addressRepo)
    {
        $this->addressRepository = $addressRepo;
    }

    /**
     * @param ListRequest $request
     * @return mixed
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function getCities(ListRequest $request)
    {
        $request->merge([
            'orderBy' => 'city',
        ]);
        $this->addressRepository->pushCriteria(new RequestCriteria($request));
        $this->addressRepository->pushCriteria(new LimitOffsetCriteria($request));
        $this->addressRepository->pushCriteria(new FilterByRelatedCriteria($request));
        $this->addressRepository->pushCriteria(new DistinctCriteria());

        $getAll = $request->get('get_all', false);
        if ($getAll) {
            $addresses = $this->addressRepository->get(['city']);
            return $this->sendResponse($addresses->pluck('city')->toArray(), 'Addresses retrieved successfully');
        }

        $perPage = $request->get('per_page', env('APP_PAGINATE', 10));
        $addresses = $this->addressRepository->paginate($perPage, ['city']);

        return $this->sendResponse($addresses->pluck('city')->toArray(), 'Cities retrieved successfully');
    }

    /**
     * @param ListRequest $request
     * @return mixed
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function index(ListRequest $request)
    {
        $this->addressRepository->pushCriteria(new RequestCriteria($request));
        $this->addressRepository->pushCriteria(new LimitOffsetCriteria($request));

        $getAll = $request->get('get_all', false);
        if ($getAll) {
            $addresses = $this->addressRepository->get();
            return $this->sendResponse($addresses->toArray(), 'Addresses retrieved successfully');
        }

        $perPage = $request->get('per_page', env('APP_PAGINATE', 10));
        $addresses = $this->addressRepository->with(['country', 'state'])->paginate($perPage);

        return $this->sendResponse($addresses->toArray(), 'Addresses retrieved successfully');
    }

    /**
     * @param CreateRequest $request
     * @return mixed
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function store(CreateRequest $request)
    {
        $input = $request->all();
        $input['country_id'] = $input['country_id'] ?? \App\Models\Country::where('name', 'Switzerland')->value('id');
        $addresses = $this->addressRepository->create($input);

        return $this->sendResponse($addresses->toArray(), 'Address saved successfully');
    }

    /**
     * @param $id
     * @param ViewRequest $r
     * @return mixed
     */
    public function show($id, ViewRequest $r)
    {
        /** @var Address $address */
        $address = $this->addressRepository->with(['country', 'state'])->findWithoutFail($id);

        if (empty($address)) {
            return $this->sendError('Address not found');
        }

        return $this->sendResponse($address->toArray(), 'Address retrieved successfully');
    }

    /**
     * @param $id
     * @param UpdateRequest $request
     * @return mixed
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function update($id, UpdateRequest $request)
    {
        $input = $request->all();
        $input['country_id'] = $input['country_id'] ?? \App\Models\Country::where('name', 'Switzerland')->value('id');

        /** @var Address $address */
        $address = $this->addressRepository->findWithoutFail($id);

        if (empty($address)) {
            return $this->sendError('Address not found');
        }

        $address = $this->addressRepository->update($input, $id);

        return $this->sendResponse($address->toArray(), 'Address updated successfully');
    }

    /**
     * @param $id
     * @param DeleteRequest $r
     * @return mixed
     * @throws \Exception
     */
    public function destroy($id, DeleteRequest $r)
    {
        /** @var Address $address */
        $address = $this->addressRepository->findWithoutFail($id);

        if (empty($address)) {
            return $this->sendError('Address not found');
        }

        $address->delete();

        return $this->sendResponse($id, 'Address deleted successfully');
    }
}
