<?php

namespace App\Criteria\Pinboard;

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
class FilterByLocationCriteria implements CriteriaInterface
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
        $u = \Auth::user();
        if ($u->can('list-pinboard')) {
            return $model;
        }

        if ($this->request->get('all', null)) {
            return $model;
        }

        $t = $u->resident;
        if (!$t) {
            return $model;
        }

        $conds = [
            "pinboard.user_id = ?",
            "pinboard.visibility = ?",
            "building_pinboard.building_id = ?",
        ];
        $args = [
            \Auth::id(),
            Pinboard::VisibilityAll,
            $t->building_id,
        ];
        // If the resident building is in a quarter, show the announcement pinboard from that quarter
        if ($t->building && $t->building->quarter_id) {
            $conds[] = "pinboard_quarter.quarter_id = ?";
            $args[] = $t->building->quarter_id;
        }

        // It's raw, Melissa, because  https://github.com/laravel/framework/issues/23957
        return $model->select('pinboard.*')->distinct()
            ->leftJoin("building_pinboard", "building_pinboard.pinboard_id", "=", "pinboard.id")
            ->leftJoin("pinboard_quarter", "pinboard_quarter.pinboard_id", "=", "pinboard.id")
            ->whereRaw("(" . implode(" or ", $conds) . ")", $args);
    }
}
