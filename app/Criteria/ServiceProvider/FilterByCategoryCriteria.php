<?php

namespace App\Criteria\ServiceProvider;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class FilterByCategoryCriteria
 * @package App\Criteria\ServiceProvider
 */
class FilterByCategoryCriteria implements CriteriaInterface
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
        $category = $this->request->get('category', null) ?? $this->request->get('category_id', null);
        if (! $category) { return $model; }

        $model = $model->where('category', $category);
        return $model;
    }
}
