<?php

namespace App\Criteria\Contract;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class FilterByBuildingCriteria
 * @package Prettus\Repository\Criteria
 */
class FilterByBuildingCriteria implements CriteriaInterface
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
        $hasBuilding = $this->request->get('has_building', null);
        if ($hasBuilding) {
            $model = $model->whereNotNull('building_id');
        }

        $buildingId = $this->request->get('building_id', null);
        if ($buildingId) {
            $model = $model->where('building_id', (int)$buildingId);
        }

        $quarterId = $this->request->get('quarter_id', null);
        if ($quarterId) {
            $model = $model->whereHas('building', function ($q) use ($quarterId) {
                $q->where('quarter_id', $quarterId);
            });
        }
        return $model;     
    }  
}
