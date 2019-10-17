<?php

namespace App\Repositories;

use App\Models\Model;
use App\Models\Contract;
use App\Models\Unit;
use App\Traits\SaveMediaUploads;

/**
 * Class ContractRepository
 * @package App\Repositories
 */
class ContractRepository extends BaseRepository
{
    use  SaveMediaUploads;


    /**
     * Configure the Model
     **/
    public function model()
    {
        return Contract::class;
    }

    /**
     * @param array $attributes
     * @return mixed
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function create(array $attributes)
    {
        $attributes = $this->fixBuildingData($attributes);
        $model = parent::create($attributes);
        if ($model)  {
            $model = $this->saveMediaUploads($model, $attributes);
        }

        return $model;
    }

    /**
     * @param array $attributes
     * @param $id
     * @return mixed
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function update(array $attributes, $id)
    {
        $model = $this->model->findOrFail($id);
        return $this->updateExisting($model, $attributes);
    }

    /**
     * @param Model $model
     * @param $attributes
     * @return mixed
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function updateExisting(Model $model, $attributes)
    {
        $attributes = $this->fixBuildingData($attributes);
        $model =  parent::updateExisting($model, $attributes); // TODO: Change the autogenerated stub
        if ($model)  {
            $model = $this->saveMediaUploads($model, $attributes);
        }
        return $model;
    }

    /**
     * @param $attributes
     * @return mixed
     */
    public function fixBuildingData($attributes)
    {
        if (isset($attributes['unit_id'])) {
            $unit = Unit::with('building')->find($attributes['unit_id']);
            if ($unit) {
                $attributes['building_id'] = $unit->building_id;
                $attributes['unit_id'] = $unit->id;
            }
        }
        return $attributes;
    }
}
