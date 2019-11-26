<?php

namespace App\Criteria\Quarter;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class FilterByStateCriteria
 * @package App\Criteria\Building
 */
class FilterByStateCriteria implements CriteriaInterface
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

        $stateId = $this->request->state_id;
        if ($stateId) {
            return $model->whereHas('address', function ($q) use ($stateId) {
                $q->where('state_id', $stateId);
            });
        }

        return $model;
    }
}
