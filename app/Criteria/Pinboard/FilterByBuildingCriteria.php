<?php

namespace App\Criteria\Pinboard;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class FilterByBuildingCriteria
 * @package Prettus\Repository\Criteria
 */
class FilterByBuildingCriteria implements CriteriaInterface
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
     * @param Builder|Model $model
     * @param RepositoryInterface $repository
     *
     * @return mixed
     * @throws \Exception
     */
    public function apply($model, RepositoryInterface $repository)
    {
        $buildingId = $this->request->get('building_id', null);
        if (!$buildingId) {
            return $model;
        }
        $buildingIds = is_array($buildingId) ? $buildingId : [$buildingId];

        // @TODO check residents can see only contract->buildings->pinboard or all
        $user = \Auth::user();
        if ($user->resident) {
            // @TODO fix contract related
            $buildingIds = $user->resident->contracts()->pluck('building_id');
        }

        $model->whereHas('buildings', function ($query) use ($buildingIds) {
            $query->whereIn('id', $buildingIds);
        });

        return $model;
    }
}
