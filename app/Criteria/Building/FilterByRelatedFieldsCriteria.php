<?php

namespace App\Criteria\Building;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
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
     * @var Request
     */
    protected $request;

    public function __construct(Request $request)
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

        $stateId = $this->request->get('state_id', null);
        if ($stateId) {
            return $model->whereHas('address', function ($q) use ($stateId) {
                $q->where('state_id', $stateId);
            });
        }

        $quarterIds = $this->request->quarter_ids ?? $this->request->quarter_id; // @TODO delete quarter_id
        if ($quarterIds) {
            $quarterIds = Arr::wrap($quarterIds);
            $model->whereIn('quarter_id', $quarterIds);
        }

        $excludeQuarterIds = $this->request->exclude_quarter_ids ?? [];
        if ($excludeQuarterIds) {
            $excludeQuarterIds = Arr::wrap($excludeQuarterIds);
            $model->whereNotIn('quarter_id', $excludeQuarterIds);
        }

        $managerId = $this->request->get('manager_id', null);
        if ($managerId) {
            return $model->whereHas('propertyManagers', function ($q) use ($managerId) {
                $q->where('building_assignees.assignee_id', $managerId);
            });
        }

        $requestStatus = $this->request->get('request_status', null);
        if ($requestStatus) {
            return $model->whereHas('relations', function ($q) use ($requestStatus) {
                $q->whereHas('residents.requests', function ($query) use ($requestStatus) {
                    $query->where('status', $requestStatus);
                });
            });
        }

        return $model;
    }
}
