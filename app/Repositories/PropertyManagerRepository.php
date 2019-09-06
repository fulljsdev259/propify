<?php

namespace App\Repositories;

use App\Models\Building;
use App\Models\Quarter;
use App\Models\PropertyManager;
use Illuminate\Support\Arr;

/**
 * Class PropertyManagerRepository
 * @package App\Repositories
 * @version March 8, 2019, 9:38 pm UTC
 */
class PropertyManagerRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'description' => 'like',
        'first_name' => 'like',
        'last_name' => 'like',
    ];

    /**
     * Return searchable fields
     *
     * @return array
     */
    public function getFieldsSearchable()
    {
        return $this->fieldSearchable;
    }

    /**
     * Configure the Model
     **/
    public function model()
    {
        return PropertyManager::class;
    }

    public function create(array $attributes)
    {
        $model = parent::create($attributes);

        if (isset($attributes['buildings'])) {
            $model->buildings()->sync($attributes['buildings']);
        }

        return $model;
    }

    public function update(array $attributes, $id)
    {
        unset($attributes['quarters']);
        $model = parent::update($attributes, $id);

        $settings = Arr::pull($attributes, 'settings');
        if ($settings) {
            $model->settings()->update($settings);
        }

        if (isset($attributes['buildings'])) {
            $model->buildings()->sync($attributes['buildings']);
        }

        return $model;
    }

    public function delete($id)
    {
        $this->applyScope();
        $model = $this->find($id);
        $model->buildings()->detach();
        $deleted = $model->delete();

        return $deleted;
    }

    public function assignments(PropertyManager $propertyManager)
    {
        $buildings = Building::select(\DB::raw('buildings.id, buildings.name, "building" as type'))
            ->join('building_assignees', 'building_id', '=', 'buildings.id')
            ->where('assignee_type', get_morph_type_of(PropertyManager::class))
            ->where('building_assignees.assignee_id',  $propertyManager->id);

        $quarters = Quarter::select(\DB::raw('quarters.id, quarters.name, "quarter" as type'))
            ->join('quarter_property_manager', 'quarter_id', '=', 'quarters.id')
            ->where('quarter_property_manager.property_manager_id', $propertyManager->id);

        return $buildings->union($quarters)->orderBy('name');
    }

    public function assignmentsWithIds(array $ids)
    {
        $buildings = Building::select(\DB::raw('buildings.id, buildings.name, "building" as type'))
            ->join('building_assignees', 'building_id', '=', 'buildings.id')
            ->where('assignee_type', get_morph_type_of(PropertyManager::class))
            ->whereIn('building_assignees.assignee_id', $ids);
        return $buildings;
    }
}
