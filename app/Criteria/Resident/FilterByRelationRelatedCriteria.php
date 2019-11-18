<?php

namespace App\Criteria\Resident;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class FilterByRelationRelatedCriteria
 * @package App\Criteria\Resident
 */
class FilterByRelationRelatedCriteria implements CriteriaInterface
{
    /**
     * @var \Illuminate\Http\Request
     */
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Apply criteria in query repository
     *
     * @param         Builder|Model     $model
     * @param RepositoryInterface $repository
     *
     * @return mixed
     * @throws \Exception
     */
    public function apply($model, RepositoryInterface $repository)
    {

        $quarterId =  $this->request->get('quarter_id', null);
        $buildingId = $this->request->get('building_id', null);
        $unitId = $this->request->get('unit_id', null);
        $stateId = $this->request->get('state_id', null);
        $hasRelation = $this->request->get('has_relation', null);

        if (!( $buildingId || $quarterId || $unitId || $stateId || $hasRelation) ) {
            return $model;
        }

        $model->whereHas('relations', function ($q) use($buildingId, $quarterId, $unitId, $stateId) {
            $q->when($buildingId, function ($q) use ($buildingId) {
                    $q->where('building_id', $buildingId);
                })->when($unitId, function ($q) use ($unitId) {
                    $q->where('unit_id', $unitId);
                })->when(($quarterId || $stateId), function ($q) use ($quarterId, $stateId) {
                     $q->whereHas('building', function ($q) use ($quarterId, $stateId) {
                         $q->when($quarterId, function ($q) use ($quarterId) {
                            $q->where('quarter_id', $quarterId);
                         })->when($stateId, function ($q) use ($stateId) {
                             $q->whereHas('address', function ($q) use ($stateId) {
                                 $q->where('state_id', $stateId);
                             });
                         });
                     });
                })
            ;
        });

        return $model;
    }  
}
