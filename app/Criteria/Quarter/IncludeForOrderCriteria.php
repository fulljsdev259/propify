<?php

namespace App\Criteria\Quarter;

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
        $model->when($this->request->orderBy == 'requests_count', function ($q) {
                $q->withCount('requests');
            })
            ->when($this->request->orderBy == 'buildings_count', function ($q) {
                $q->withCount('buildings');
            })->when($this->request->orderByRaw == 'count_of_apartments_units', function ($q) use ($sortedBy) {
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

            $statusCodes = Relation::StatusColorCode;
            foreach ($statusCodes as $status => $color) {
                if ($this->request->orderByRaw == Relation::Status[$status] . '_units_count') {
                    $model->orderByRaw('(SELECT COUNT(*) FROM `units` WHERE 
                        deleted_at IS NULL 
                        and (
                        (select status from relations where relations.unit_id = units.id order by relations.id desc limit 1) = ' . $status
                         . ($status == Relation::StatusInActive ? ' or (select status from relations where relations.unit_id = units.id order by relations.id desc limit 1) IS NULL' : '')
                        . '
                            
                        )
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
                }
            }

        return $model;
    }
}
