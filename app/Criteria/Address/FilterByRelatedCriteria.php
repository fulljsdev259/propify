<?php

namespace App\Criteria\Address;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class FilterByRelatedCriteria
 * @package App\Criteria\Address
 */
class FilterByRelatedCriteria implements CriteriaInterface
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
        $quarters = $this->request->get('quarters', null);
        if ($quarters) {
            $model->has('quarter');
        }

        $buildings = $this->request->get('buildings', null);
        if ($buildings) {
            $model->has('building');
        }

        $units = $this->request->get('units', null);
        if ($units) {
            $model->where(function ($q) {
                $q->whereHas('building', function ($q) {
                    $q->has('units');
                })->orWhereHas('quarter', function ($q) {
                    $q->has('units');
                });
            });
        }


        return $model;
    }
}
