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
            $model->where(function ($q) {
                $companyName = $this->request->company_name;
                $q->has('property_manager')->orWhereHas('service_provider', function ($q) use ($companyName) {
                    $q->where('company_name', 'like', '%' . $companyName . '%');
                });
            });
        }

        return $model;
    }
}
