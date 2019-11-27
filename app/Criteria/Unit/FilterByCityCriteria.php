<?php

namespace App\Criteria\Unit;

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
class FilterByCityCriteria implements CriteriaInterface
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

        $cities = $this->request->cities;
        if ($cities) {
            $cities = Arr::wrap($cities);
            return $model->where(function ($q) use ($cities) {
                $q->whereHas('building', function ($q)  use ($cities) {
                    $q->whereHas('address', function ($q) use ($cities) {
                        $q->whereIn('city', $cities);
                    });
                    $this->filterByQuarter($q, $cities);
                });
                $this->filterByQuarter($q, $cities);
            });
        }

        return $model;
    }

    protected function filterByQuarter($query, $cities)
    {
        $query->orWhereHas('quarter', function ($q)  use ($cities) {
            $q->whereHas('address', function ($q) use ($cities) {
                $q->whereIn('city', $cities);
            });
        });
    }
}
