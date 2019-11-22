<?php

namespace App\Criteria\Quarter;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
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

        $roles = $this->request->get('roles', null);
        if ($roles) {
            if (! is_array($roles)) {
                $roles = [$roles];
            }
            return $model->whereHas('users', function ($q) use ($roles) {
                $q->whereHas('roles', function ($q) use ($roles) {
                    $q->whereIn('id', $roles);
                });
            });
        }

        return $model;
    }
}
