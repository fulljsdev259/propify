<?php

namespace App\Criteria\Request;

use App\Models\Request;
use App\Models\Resident;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class FilterByPermissionsCriteria
 * @package Prettus\Repository\Criteria
 */
class FilterByPermissionsCriteria implements CriteriaInterface
{
    /**
     * @var \Illuminate\Http\Request
     */
    protected $request;

    /**
     * FilterByPermissionsCriteria constructor.
     * @param \Illuminate\Http\Request $request
     */
    public function __construct(\Illuminate\Http\Request $request)
    {
        $this->request = $request;
    }

    /**
     * Apply criteria in query repository
     *
     * @param         Builder|Model $model
     * @param RepositoryInterface $repository
     *
     * @return mixed
     * @throws \Exception
     */
    public function apply($model, RepositoryInterface $repository)
    {
        $user = $this->request->user();

        if ($user->resident) {


            $contracts = $user->resident->contracts()
                ->where('status', Resident::StatusActive)
                ->select('id', 'building_id')->with('building:id,quarter_id')
                ->get();

            $buildingIds = $contracts->pluck('building.id')->toArray();
            $quarterIds = $contracts->pluck('building.quarter_id')->toArray();

            $model->where(function ($q) use ($user, $quarterIds, $buildingIds) {
                $q->where(function ($q) use ($user) {
                    $q->where('visibility', Request::VisibilityResident)
                        ->where('resident_id', $user->resident->id);
                })->when($buildingIds, function ($q) use ($buildingIds) {
                    $q->orWhere(function ($q) use ($buildingIds) {
                        $q->where('visibility', Request::VisibilityBuilding)
                            ->whereHas('contract', function ($q) use ($buildingIds){
                                $q->whereIn('building_id', $buildingIds);
                            });
                        });
                })->when($quarterIds, function ($q) use ($quarterIds) {
                    $q->orWhere(function ($q) use ($quarterIds) {
                        $q->where('visibility', Request::VisibilityQuarter)
                            ->whereHas('contract', function ($q) use ($quarterIds) {
                                $q->whereHas('building', function ($q) use ($quarterIds) {
                                    $q->whereIn('quarter_id', $quarterIds);
                                });
                            });
                        });
                });

            });
            return $model;
        }

//        if ($u->hasRole('service') && $u->serviceProvider) {
//            $model->whereHas('providers', function ($q) use ($u) {
//                $q->where('service_providers.id', $u->serviceProvider->id);
//            });
//        }

        return $model;
    }
}
