<?php

namespace App\Criteria\Request;

use App\Models\Request;
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
        $u = $this->request->user();

        if ($u->resident) {

            $qs = [
                '(requests.visibility = ? and requests.resident_id = ?)',
                '(requests.visibility = ? and buildings.quarter_id = ?)',
            ];
            $model->select('requests.*')
                ->join('units', 'units.id', '=', 'requests.unit_id')
                ->join('buildings', 'units.building_id', '=', 'buildings.id');
            $vs = [
                Request::VisibilityResident, $u->resident->id,
                Request::VisibilityBuilding, $u->resident->building_id,
            ];
            if ($u->resident->building) {
                $vs[] = Request::VisibilityQuarter;
                $vs[] = $u->resident->building->quarter_id;
                $qs[] = '(requests.visibility = ? and units.building_id = ?)';
            }
            return $model->whereRaw('(' . implode(' or ', $qs) . ')', $vs);
        }

//        if ($u->hasRole('service') && $u->serviceProvider) {
//            $model->whereHas('providers', function ($q) use ($u) {
//                $q->where('service_providers.id', $u->serviceProvider->id);
//            });
//        }

        return $model;
    }
}
