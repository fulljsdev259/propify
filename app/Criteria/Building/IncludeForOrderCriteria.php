<?php

namespace App\Criteria\Building;

use App\Models\Relation;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class FilterByUserRoleCriteria
 * @package App\Criteria\Quarter
 */
class IncludeForOrderCriteria implements CriteriaInterface
{
    /**
     * @var Request
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
        $model->when($this->request->orderBy == 'requests_count', function ($q) {
            $q->withCount('requests');
        });

        $sortedBy = strtolower($this->request->sortedBy) == 'asc' ? 'asc' : 'desc';
        foreach (\App\Models\Request::Status as $status => $value) {
            $requestStatusCount = 'requests_' . $value . '_count';
            if ($this->request->orderByRaw == $requestStatusCount) {
                $model->orderByRaw('(select count(*) from `units` 
                  where `buildings`.`id` = `units`.`building_id` 
                  and exists (
                    select * from `requests` inner join `relations` on `relations`.`id` = `requests`.`relation_id` 
                      where `units`.`id` = `relations`.`unit_id`
                      and `requests`.`status` = ' . $status . ' 
                      and `requests`.`deleted_at` is null
                  ) 
                  and `units`.`deleted_at` is null
                  and units.building_id = buildings.id) ' . $sortedBy);
            }
        }

        foreach (Relation::Status as $status => $value) {
            if ($this->request->orderByRaw == Relation::Status[$status] . '_units_count') {
                $model->orderByRaw('(SELECT COUNT(*) FROM `units` WHERE 
                        deleted_at IS NULL 
                        and (
                        (select status from relations where relations.unit_id = units.id order by relations.id desc limit 1) = ' . $status
                    . ($status == Relation::StatusInActive ? ' or (select status from relations where relations.unit_id = units.id order by relations.id desc limit 1) IS NULL' : '')
                    . ') and building_id = buildings.id) ' . $sortedBy
                );
            }
        }

        return $model;
    }
}
