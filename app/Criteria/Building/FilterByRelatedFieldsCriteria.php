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
        $search = $this->request->get(config('repository.criteria.params.search', 'search'), null);
        if ($search) {
            // @TODO if building make by code that case street and house_num not need. It is already included
            //App\Criteria\CommonRequestCriteria see BuildingRepository $fieldSearchable property
            $model = $model->orWhereHas('address', function ($q) use ($search) {
                $q->where('loc_addresses.city', 'like', '%' . $search . '%')
                ->orWhere('loc_addresses.street', 'like', '%' . $search . '%')
                ->orWhere('loc_addresses.zip', 'like', '%' . $search . '%')
                ->orWhere('loc_addresses.house_num', 'like', '%' . $search . '%');
            });
        }

        $state_id = $this->request->get('state_id', null);
        if ($state_id) {
            return $model->whereHas('address', function ($q) use ($state_id) {
                $q->where('state_id', $state_id);
            });
        }

        $quarter_id = $this->request->get('quarter_id', null);
        if ($quarter_id) {
            $model->where('quarter_id', $quarter_id);
        }

        $manager_id = $this->request->get('manager_id', null);
        if ($manager_id) {
            return $model->whereHas('propertyManagers', function ($q) use ($manager_id) {
                $q->where('building_assignees.assignee_id', $manager_id);
            });
        }

        $request_status = $this->request->get('request_status', null);
        if ($request_status) {
            return $model->whereHas('residents.requests', function ($query) use ($request_status) {
                $query->where('status', $request_status);
            });
        }

        return $model;
    }
}
