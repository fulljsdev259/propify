<?php

namespace App\Criteria\Building;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
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

        $quarterId = $this->request->get('quarter_id', null);
        if ($quarterId) {
            $model->where('quarter_id', $quarterId);
        }

        $managerId = $this->request->get('manager_id', null);
        if ($managerId) {
            return $model->whereHas('propertyManagers', function ($q) use ($managerId) {
                $q->where('building_assignees.assignee_id', $managerId);
            });
        }

        $requestStatus = $this->request->get('request_status', null);
        if ($requestStatus) {
            return $model->whereHas('contracts', function ($q) use ($requestStatus) {
                $q->whereHas('residents.requests', function ($query) use ($requestStatus) {
                    $query->where('status', $requestStatus);
                });
            });
        }

        return $model;
    }
}
