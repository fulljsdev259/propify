<?php

namespace App\Criteria\Unit;

use App\Models\Request;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class FilterByRelatedFieldsCriteria
 * @package Prettus\Repository\Criteria
 */
class FilterByRelatedFieldsCriteria implements CriteriaInterface
{
    /**
     * @var \Illuminate\Http\Request
     */
    protected $request;

    /**
     * FilterByRelatedFieldsCriteria constructor.
     * @param \Illuminate\Http\Request $request
     */
    public function __construct(\Illuminate\Http\Request $request)
    {
        $this->request = $request;
    }

    /**
     * Apply criteria in query repository
     *
     * @param Builder|Model $model
     * @param RepositoryInterface $repository
     *
     * @return mixed
     * @throws \Exception
     */
    public function apply($model, RepositoryInterface $repository)
    {
        $buildingIds = $this->request->building_ids ?? $this->request->building_id;
        if ($buildingIds) {
            $buildingIds = Arr::wrap($buildingIds);
            $model->whereIn('building_id', $buildingIds);
        }

        $state_id = $this->request->get('state_id', null);
        if ($state_id) {
            $model->whereHas('building.address', function ($query) use ($state_id) {
                $query->where('state_id', (int)$state_id);
            });
        }

        // @TODO correct
        $request = $this->request->get('request', null);
        if ($request == 1) {
            $model->whereHas('relations', function ($q) {
                $q->whereHas('resident.requests', function ($query) {
                    $query->where('status', '<', Request::StatusDone);
                });
            });
        }

        // @TODO correct need or not I think no need because now used user_id and it based on quarter
        $managerId = $this->request->get('manager_id', null);
        if ($managerId) {
            $model->whereHas('building.property_managers', function ($q) use ($managerId) {
                $q->where('property_managers.id', $managerId);
            });
        }

        return $model;
    }
}
