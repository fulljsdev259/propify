<?php

namespace App\Criteria\Quarter;

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
        $raw = 'SELECT
    COUNT(u.id)
FROM
    (
    SELECT TYPE
        ,
        id,
        deleted_at
    FROM
        `units`
    WHERE
        quarter_id = 10
    UNION ALL
SELECT TYPE
    ,
    id,
    deleted_at
FROM
    `units`
WHERE
    building_id IN(
    SELECT
        id
    FROM
        buildings
    WHERE
        quarter_id = 10 AND deleted_at IS NULL
)
) AS u
WHERE TYPE
    = 1 AND deleted_at IS NULL
ORDER BY
    `u`.`id` ASC'; // @TODO delete $raw

        $model->when($this->request->orderBy == 'requests_count', function ($q) {
                $q->withCount('requests');
            })
            ->when($this->request->orderBy == 'buildings_count', function ($q) {
                $q->withCount('buildings');
            });

        return $model;
    }
}
