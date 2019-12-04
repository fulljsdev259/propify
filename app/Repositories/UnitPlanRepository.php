<?php

namespace App\Repositories;

use App\Models\UnitPlan;

/**
 * Class UnitPlan
 *
 * @package App\Repositories
 * @version December 03, 2020, 12:50 pm UTC
 *
 * @method UnitPlan findWithoutFail($id, $columns = ['*'])
 * @method UnitPlan find($id, $columns = ['*'])
 * @method UnitPlan first($columns = ['*'])
 */
class UnitPlanRepository extends BaseRepository
{
    /**
     * Configure the Model
     **/
    public function model()
    {
        return UnitPlan::class;
    }
}
