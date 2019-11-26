<?php

namespace App\Criteria\Building;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class FilterByUserRoleCriteria
 * @package App\Criteria\Quarter
 */
class FilterByUserRoleCriteria implements CriteriaInterface
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

        $roles = $this->request->get('roles', []);
        $userIds = $this->request->get('user_ids', []);
        if ($roles || $userIds) {
            $roles = Arr::wrap($roles);
            $userIds = Arr::wrap($userIds);
            return $model->whereHas('quarter', function ($q) use ($roles, $userIds) {
                $q->whereHas('users', function ($q) use ($roles, $userIds) {
                    $q->when($roles, function ($q) use ($roles) {
                        $q->whereHas('roles', function ($q) use ($roles) {
                            $q->whereIn('name', $roles);
                        });
                    })
                        ->when($userIds, function ($q) use ($userIds) {
                            $q->whereIn('users.id', $userIds);
                        });
                });
            });
        }

        return $model;
    }
}
