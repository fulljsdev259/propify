<?php

namespace App\Criteria\User;

use App\Models\QuarterAssignee;
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
        $excludeAssigneesQuarterId = $this->request->exclude_assignees_quarter_id;
        if ($excludeAssigneesQuarterId) {
            $userIds = QuarterAssignee::where('quarter_id', $excludeAssigneesQuarterId)->pluck('user_id')->all();
            if ($userIds) {
                $model = $model->whereNotIn('id', $userIds);
            }
        }

        return $model;
    }
}
