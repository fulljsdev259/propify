<?php

namespace App\Criteria\Unit;

use App\Models\Relation;
use App\Models\Unit;
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
        $sortedBy = strtolower($this->request->sortedBy) == 'asc' ? 'asc' : 'desc';
        $model->when($this->request->orderByRaw == 'status', function ($q) use ($sortedBy) {
           $q->orderByRaw('(SELECT status FROM relations 
                WHERE units.id = relations.unit_id
                order by id desc limit 1) ' . $sortedBy
            );
        })->when($this->request->orderByRaw == 'internal_quarter_id', function ($q) use ($sortedBy) {
           $q->orderByRaw('IF (
                building_id IS NULL, 
                (select internal_quarter_id from quarters where quarters.id = units.quarter_id),
                (select internal_quarter_id from quarters where quarters.id = (select quarter_id from buildings where buildings.id = units.building_id))
                ) ' . $sortedBy);
        })->when($this->request->orderBy == 'requests_count', function ($q) use ($sortedBy) {
           $q->withCount('requests');
        });


        return $model;
    }
}
