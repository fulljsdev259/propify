<?php

namespace App\Criteria\Pinboard;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
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
     * @param Builder|Model $model
     * @param RepositoryInterface $repository
     *
     * @return mixed
     * @throws \Exception
     */
    public function apply($model, RepositoryInterface $repository)
    {
        $quarter_id = $this->request->get('quarter_id', null);
        if (!$quarter_id) {
            return $model;
        }

        $u = \Auth::user();
        if (!$u->can('list-pinboard') && $u->resident) {
            $quarter_id = $u->resident->building->quarter_id;
        }

        $model->whereHas('quarters', function ($query) use ($quarter_id) {
            $query->where('id', $quarter_id);
        });

        return $model;
    }
}
