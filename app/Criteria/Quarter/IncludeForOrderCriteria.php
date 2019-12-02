<?php

namespace App\Criteria\Quarter;

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
        $model->when($this->request->orderBy == 'requests_count', function ($q) {
                $q->withCount('requests');
            })
            ->when($this->request->orderBy == 'buildings_count', function ($q) {
                $q->withCount('buildings');
            })->when($this->request->orderByRaw == 'count_of_apartments_units', function ($q) {
                $sortedBy = $this->request->sortedBy == 'asc' ? 'asc' : 'desc';
                $q->orderByRaw('(SELECT COUNT(*) FROM `units` WHERE 
                    deleted_at IS NULL 
                    and type = ' .  Unit::TypeApartment. '
                    and IF(
                        quarter_id IS NULL,
                        (
                            SELECT
                                quarter_id
                            FROM
                                buildings
                            WHERE
                                buildings.id = units.`building_id`
                        ),
                        quarter_id
                    ) = quarters.id) ' . $sortedBy
                );
            });

        return $model;
    }
}
