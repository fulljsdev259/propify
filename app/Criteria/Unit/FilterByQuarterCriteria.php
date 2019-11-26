<?php

namespace App\Criteria\Unit;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class FilterByQuarterCriteria
 * @package Prettus\Repository\Criteria
 */
class FilterByQuarterCriteria implements CriteriaInterface
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
     * @param         Builder|Model $model
     * @param RepositoryInterface $repository
     *
     * @return mixed
     * @throws \Exception
     */
    public function apply($model, RepositoryInterface $repository)
    {

        $quarterIds = $this->request->quarter_ids ?? $this->request->quarter_id; // @TODO delete quarter_id
        if ($quarterIds) {
            $quarterIds = Arr::wrap($quarterIds);
            return $model->where(function ($query) use ($quarterIds) {
                $query->whereIn('quarter_id', $quarterIds)
                    ->orWhereHas('building', function ($query) use ($quarterIds) {
                        $query->whereIn('quarter_id', $quarterIds);
                    });
            });
        }

        return $model;
    }
}
