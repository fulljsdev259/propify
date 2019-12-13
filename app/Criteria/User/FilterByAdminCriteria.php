<?php

namespace App\Criteria\User;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class FilterByAdminCriteria
 * @package App\Criteria\User
 */
class FilterByAdminCriteria implements CriteriaInterface
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
        $orderBy = $this->request->get(config('repository.criteria.params.orderBy', 'orderBy'), 'id');
        $sortedBy = $this->request->get(config('repository.criteria.params.sortedBy', 'sortedBy'), 'desc');
        $model = $model->orderBy($orderBy, $sortedBy);
        $search = $this->request->search;

        $model->where(function ($q) use ($search) {
            $q->whereHas('property_manager', function ($q) use ($search) {
                $q->when($search, function ($q) use ($search) {
                    $q->whereRaw('Concat(first_name, " ", last_name) like ?' ,   '%' . $search . '%');
                });
            })->orWhereHas('service_provider', function ($q) use ($search) {
                $q->when($search, function ($q) use ($search) {
                    $q->where(function ($q) use ($search) {
                        $q->whereRaw('Concat(first_name, " ", last_name) like ?' ,   '%' . $search . '%')
                            ->orWhere('company_name', 'like', '%' . $search . '%');
                    });
                });
            })->when($search, function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search .'%');
            });
        });

        return $model;
    }
}
