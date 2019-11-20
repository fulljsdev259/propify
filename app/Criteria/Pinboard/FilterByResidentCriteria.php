<?php

namespace App\Criteria\Pinboard;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class FilterByResidentCriteria
 * @package Prettus\Repository\Criteria
 */
class FilterByResidentCriteria implements CriteriaInterface
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
     * @param Model $model
     * @param RepositoryInterface $repository
     *
     * @return mixed
     * @throws \Exception
     */
    public function apply($model, RepositoryInterface $repository)
    {
        $residentId = $this->request->get('resident_id', null);

        $createdByResident = $this->request->get('createdby_resident', false);
        if ($createdByResident) {
            $model = $model->whereHas('user', function ($q) {
                $q->has('resident');
            });
        }

        if (!$residentId) {
            return $model;
        }

        $model = $model->where('resident_id', $residentId);
        return $model;
    }
}
