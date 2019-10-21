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
        $requestId = $this->request->get('request_id', null);
        $requestStatus = $this->request->get('request_status', null);
        if (! empty($requestId) || ! empty($requestStatus)) {
            $model->whereHas('requests', function ($q) use ($requestStatus, $requestId) {
                $q->when($requestStatus, function ($q) use ($requestStatus) {
                    $q->where('requests.status', $requestStatus);
                })->when($requestId, function ($q) use ($requestId) {
                    $q->where('requests.id', $requestId);
                });
            });
        }

        return $model;
    }
}
