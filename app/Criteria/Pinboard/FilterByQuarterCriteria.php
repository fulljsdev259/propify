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
        $quarterId = $this->request->get('quarter_id', null);
        if (!$quarterId) {
            // @TODO check residents can see only relation->quarter->pinboard or all
            return $model;
        }

        $quarterIds = is_array($quarterId) ? $quarterId : [$quarterId];
        $user = \Auth::user();
        if ($user->resident) {
            $quarterIds = $user->resident->relations()->pluck('quarter_id');
        }

        $model->whereHas('quarters', function ($query) use ($quarterIds) {
            $query->whereIn('id', $quarterIds);
        });

        return $model;
    }
}
