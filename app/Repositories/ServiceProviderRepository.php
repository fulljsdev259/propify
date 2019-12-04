<?php

namespace App\Repositories;

use App\Models\Model;
use App\Models\ServiceProvider;
use App\Models\Building;
use App\Models\Quarter;
use App\Models\Unit;
use Illuminate\Support\Arr;

/**
 * Class ServiceProviderRepository
 * @package App\Repositories
 * @version February 14, 2019, 9:18 pm UTC
 *
 * @method ServiceProvider findWithoutFail($id, $columns = ['*'])
 * @method ServiceProvider find($id, $columns = ['*'])
 * @method ServiceProvider first($columns = ['*'])
 */
class ServiceProviderRepository extends BaseRepository
{

    /**
     * @var array
     */
    protected $fieldSearchable = [
        'category' => 'like',
        'company_name' => 'like',
        'first_name' => 'like',
        'last_name' => 'like',
        'email' => 'like',
        'phone' => 'like',
        'service_provider_format' => 'like',
        'user.email' => 'like',
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return ServiceProvider::class;
    }

    public function create(array $attributes)
    {
        if (isset($attributes['user'])) {
            unset($attributes['user']);
        }

        if (isset($attributes['address'])) {
            unset($attributes['address']);
        }

        return parent::create($attributes);
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
        if (isset($attributes['unit_id'])) {
            $unit = Unit::with('building')->find($attributes['unit_id']);
            if ($unit) {
                $attributes['building_id'] = $unit->building_id;
                $attributes['unit_id'] = $unit->id;
                $attributes['address_id'] = $unit->building->address_id;
            }
            unset($attributes['unit']);
        }

        if (isset($attributes['address'])) {
            unset($attributes['address']);
        }

        if (isset($attributes['building'])) {
            unset($attributes['building']);
        }

        if (isset($attributes['user'])) {
            unset($attributes['user']);
        }

        return parent::updateExisting($model, $attributes);
    }

}
