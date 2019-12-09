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
        $getAdmins = $this->request->get_admins;
        if ($getAdmins) {
            $name = $this->request->name;
            if ($name) {
                $model->whereHas('property_manager', function ($q) use ($name) {
                    $q->whereRaw('Concat(first_name, " ", last_name) like ?' ,   '%' . $name . '%');
                })->orWhereHas('service_provider', function ($q) use ($name) {
                    $q->where(function ($q) use ($name) {
                        $q->whereRaw('Concat(first_name, " ", last_name) like ?' ,   '%' . $name . '%')
                            ->orWhere('company_name', 'like', '%' . $name . '%');
                    });
                });

            } else {
                $model->where(function ($q) {
                    $q->has('property_manager')->orHas('service_provider');
                });
            }

        }

        return $model;
    }
}
