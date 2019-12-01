<?php

namespace App\Criteria\User;

use App\Models\BuildingAssignee;
use App\Models\QuarterAssignee;
use App\Models\RequestAssignee;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class FilterByExcludeAssigneesCriteria
 * @package App\Criteria\User
 */
class FilterByExcludeAssigneesCriteria implements CriteriaInterface
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
        $userIds = collect();
        $excludeAssigneesQuarterId = $this->request->exclude_assignees_quarter_id;
        if ($excludeAssigneesQuarterId) {
            $userIds = QuarterAssignee::where('quarter_id', $excludeAssigneesQuarterId)->pluck('user_id');
        }
        $excludeAssigneesRequestId = $this->request->exclude_assignees_request_id;
        if ($excludeAssigneesRequestId) {
            $userIds = RequestAssignee::where('request_id', $excludeAssigneesRequestId)->pluck('user_id');
        }

        $excludeAssigneesBuildingId = $this->request->exclude_assignees_building_id;
        if ($excludeAssigneesBuildingId) {
            $buildingAssignedUserIds = BuildingAssignee::where('building_id', $excludeAssigneesBuildingId)->pluck('user_id');
            $userIds = $userIds->merge($buildingAssignedUserIds);
        }

        if ($userIds->isNotEmpty()) {
            $userIds = $userIds->all();
            $userIds = array_diff($userIds, [null]);
            $model = $model->whereNotIn('id', $userIds);
        }

        return $model;
    }
}
