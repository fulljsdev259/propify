<?php

namespace App\Criteria\Pinboard;

use App\Models\Contract;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;
use App\Models\Pinboard;

/**
 * Class FilterByLocationCriteria
 * @package Prettus\Repository\Criteria
 */
class FilterByPermissionCriteria implements CriteriaInterface
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
        $user = \Auth::user();

        $resident = $user->resident;
        if (!$resident) {
            return $model;
        }


        $contracts = $resident->contracts()
            ->where('status', Contract::StatusActive)
            ->where('start_date', '<', now())
            ->select('id', 'building_id')->with('building:id,quarter_id')
            ->get();

        $buildingIds = $contracts->pluck('building.id')->toArray();
        $quarterIds = $contracts->pluck('building.quarter_id')->toArray();
        $model->where(function ($q) use ($quarterIds, $buildingIds) {
            $q->where(function ($q) {
                $q->where('visibility', Pinboard::VisibilityAll)
                    ->where('user_id', \Auth::id());
            })->when($buildingIds, function ($q) use ($buildingIds) {
                $q->orWhere(function ($q) use ($buildingIds) {
                    $q->where('visibility', Pinboard::VisibilityAddress)
                        ->whereHas('buildings', function ($q) use ($buildingIds){
                            $q->whereIn('building_id', $buildingIds);
                        });
                });
            })->when($quarterIds, function ($q) use ($quarterIds) {
                $q->orWhere(function ($q) use ($quarterIds) {
                    $q->where('visibility', Pinboard::VisibilityQuarter)
                        ->whereHas('quarters', function ($q) use ($quarterIds) {
                            $q->whereIn('quarter_id', $quarterIds);
                        });
                });
            });

        });

        return $model;
    }
}
