<?php

namespace App\Criteria\Unit;

use App\Models\Request;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class FilterByOpenRequestsCriteria
 * @package Prettus\Repository\Criteria
 */
class FilterByOpenRequestsCriteria implements CriteriaInterface
{
    /**
     * @var \Illuminate\Http\Request
     */
    protected $request;

    /**
     * FilterByOpenRequestsCriteria constructor.
     * @param \Illuminate\Http\Request $request
     */
    public function __construct(\Illuminate\Http\Request $request)
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
        $request = $this->request->get('request', null);
        if ($request == 1) {
            return $model->whereHas('resident.requests', function ($query) {
                $query->where('status', '<', Request::StatusDone);
            });
        }

        return $model;
    }
}
