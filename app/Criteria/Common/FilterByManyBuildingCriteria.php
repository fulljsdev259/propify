<?php

namespace App\Criteria\Common;

use App\Models\ServiceProvider;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class FilterByBuildingCriteria
 * @package App\Criteria\ServiceProvider
 */
class FilterByManyBuildingCriteria implements CriteriaInterface
{
    /**
     * @var \Illuminate\Http\Request
     */
    protected $request;

    /**
     * FilterByRelationsCriteria constructor.
     * @param Request $request
     */
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
        $buildingId = $this->request->get('building_id', null);

        if (! empty($buildingId)) {
            $model->whereHas('buildings', function ($q) use ($buildingId) {
                $q->where('building_id', $buildingId);
            });
        }


        return $model;
    }
}
