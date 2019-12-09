<?php

namespace App\Repositories;

use App\Models\Model;
use App\Models\UnitPlan;
use App\Traits\SaveMediaUploads;

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
    use SaveMediaUploads;

    /**
     * Configure the Model
     **/
    public function model()
    {
        return UnitPlan::class;
    }

    /**
     * @param array $attributes
     * @return mixed
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function create(array $attributes)
    {
        $unitPlan = parent::create($attributes);

        if (!empty($attributes['media'])) {
            $this->saveMediaUploads($unitPlan, $attributes);
        }

        return $unitPlan;
    }

    /**
     * @param Model $model
     * @param $attributes
     * @return Model|mixed
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function updateExisting(Model $model, $attributes)
    {
        $model = parent::updateExisting($model, $attributes);

        if (!empty($attributes['media'])) {
            $this->saveMediaUploads($model, $attributes);
        }

        return $model;
    }
}
