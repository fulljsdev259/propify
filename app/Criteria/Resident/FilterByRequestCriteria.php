<?php

namespace App\Criteria\Resident;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class FilterByRequestCriteria
 * @package Prettus\Repository\Criteria
 */
class FilterByRequestCriteria implements CriteriaInterface
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
        $rid = $this->request->get('request_id', null);
        $rs = $this->request->get('request_status', null);
        if ($rid || $rs) {
            $model->join('requests', 'requests.resident_id', '=', 'residents.id');
        }
        if ($rid) {
            $model->where('requests.id', $rid);
        }
        if ($rs) {
            $model->where('requests.status', $rs);
        }

        return $model;
    }
}
