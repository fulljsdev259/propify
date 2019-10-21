<?php

namespace App\Criteria\Common;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class FilterByPinboardCriteria
 * @package Prettus\Repository\Criteria
 */
class FilterByPinboardCriteria implements CriteriaInterface
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

        $pinboardId = $this->request->pinboard_id ?? $this->request->post_id;
        if (! empty($pinboardId)) {
            $model->whereHas('pinboards', function ($q) use ($pinboardId) {
                $q->where('pinboard_id', $pinboardId);
            });
        }

        return $model;
    }
}
