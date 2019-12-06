<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\AppBaseController;
use App\Repositories\UnitPlanRepository;
use App\Repositories\UnitRepository;
use App\Transformers\MediaTransformer;
use Illuminate\Http\Request;

class UploadUnitPlanAPIController extends AppBaseController
{
    /**
     * @var UnitPlanRepository
     */
    private $unitPlanRepository;

    /**
     * @var UnitRepository
     */
    private $unitRepository;

    public function __construct(UnitRepository $unitRepository)
    {
        $this->unitRepository = $unitRepository;
    }

    /**
     * @param Request $request
     * @param int $unitId
     * @return mixed
     * @throws \OwenIt\Auditing\Exceptions\AuditingException
     */
    public function store(Request $request, int $unitId)
    {
        $unit = $this
            ->unitRepository
            ->findWithoutFail($unitId);

//        $unit
//            ->addMedia($request['media'])
//            ->toMediaCollection('unit_plan', 'units_plans');

        $media = $this
            ->unitRepository
            ->uploadFile(
                'plans',
                $request['media'],
                $unit
            );

         $response = (new MediaTransformer)->transform($media);

        return $this->sendResponse($response, __('general.swal.media.added'));
    }
}
