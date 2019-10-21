<?php

namespace App\Criteria\Resident;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class FilterByBuildingCriteria
 * @package Prettus\Repository\Criteria
 */
class FilterByContractRelatedCriteria implements CriteriaInterface
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
        $hasBuilding = $this->request->get('has_building', null);



        if (!( $buildingId || $quarterId || $hasBuilding || $unitId || $stateId) ) {
            return $model;
        }
        $model->join('contracts', 'contracts.resident_id', '=', 'residents.id');

        if ($hasBuilding) {
            $model->whereNotNull('contracts.building_id');
        }

        if ($buildingId) {
            $model->where('contracts.building_id', $buildingId);
        }

        if ($unitId) {
            $model->where('contracts.unit_id', $unitId);
        }

        if ($quarterId || $stateId) {
            $model->join('buildings', 'contracts.building_id', '=', 'buildings.id');

            if ($quarterId) {
                $model->where('buildings.quarter_id', $quarterId);
            }

            if ($stateId) {
                $model->join('loc_addresses', 'loc_addresses.id', '=', 'buildings.address_id')
                    ->where('loc_addresses.state_id', $stateId);
            }
        }

        return $model;     
    }  
}
