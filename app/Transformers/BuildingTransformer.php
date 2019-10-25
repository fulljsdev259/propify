<?php

namespace App\Transformers;

use App\Models\Building;
use App\Models\Resident;
use function GuzzleHttp\Promise\queue;

/**
 * Class BuildingTransformer.
 *
 * @package namespace App\Transformers;
 */
class BuildingTransformer extends BaseTransformer
{
    /**
     * Transform the Building entity.
     *
     * @param Building $model
     * @return array
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function transform(Building $model)
    {
        $response = [
            'id' => $model->id,
            'name' => $model->name,
            'building_format' => $model->building_format,
            'label' => $model->label,
            'contact_enable' => $model->contact_enable,
            'description' => $model->description,
            'floor_nr' => $model->floor_nr,
            'under_floor' => $model->under_floor,
            'basement' => $model->basement,
            'attic' => $model->attic,
            'created_at' => $model->created_at ? $model->created_at->format('Y-m-d') : '',
            'quarter_id' => $model->quarter_id,
            'address_id' => $model->address_id,
            'internal_building_id' => $model->internal_building_id,

            'units_count' => $model->units_count,
            'residents_count' => 0,
            'active_residents_count' => 0,
            'in_active_residents_count' => 0,
            'property_managers_count' => 0
        ];

        $withCount = $model->getStatusRelationCounts();
        $response = array_merge($response, $withCount);

        if ($model->relationExists('address')) {
            $response['address'] = (new AddressTransformer)->transform($model->address);
        }

        if ($model->relationExists('quarter')) {
            $response['quarter'] = (new QuarterTransformer)->transform($model->quarter);
        }

        if ($model->relationExists('service_providers')) {
            $response['service_providers'] = (new ServiceProviderTransformer)->transformCollection($model->service_providers);
        }

        $assignedUsers = $model->newCollection();
        if ($model->relationExists('propertyManagers')) {
            $assignedUsers = $assignedUsers->merge($model->propertyManagers->pluck('user'));
            $response['managers'] = (new PropertyManagerSimpleTransformer)->transformCollection($model->propertyManagers);

            if ($model->relationExists('lastPropertyManagers')) {
                $response['managers_last'] = (new PropertyManagerSimpleTransformer)->transformCollection($model->lastPropertyManagers);
            }
            
            if ($model->property_managers_count > 2) {
                $response['property_managers_count'] = $model->property_managers_count - 2;
            }
        }

        if ($model->relationExists('users')) {
            $assignedUsers = $assignedUsers->merge($model->users);
            $response['users'] = (new UserTransformer())->transformCollection($model->users);
        }

        if ($assignedUsers->count()) {
            $response['assignedUsers'] = (new UserTransformer)->transformCollection($assignedUsers);
        } else {
            $response['assignedUsers'] = [];
        }

        if ($model->relationExists('media')) {
            $response['media'] = (new MediaTransformer)->transformCollection($model->media);
        }

        if ($model->relationExists('contracts')) {
            $response['contracts'] = (new ContractTransformer())->transformCollection($model->contracts);
            $residents = collect($response['contracts'])->pluck('resident')->unique();


            // @TODO delete
            if ($residents->isNotEmpty()) {
                $response['residents'] = $residents->all();
                $response['residents_last'] = $residents->sortByDesc('id')->slice(0, 5)->all();
                $response['residents_count'] = $residents->count();
                $response['active_residents_count'] = $residents->where('status', Resident::StatusActive)->count();
                $response['in_active_residents_count'] = $residents->where('status', Resident::StatusInActive)->count();
            }
        }

        return $response;
    }

    /**
     * Transform Request to Address entity.
     *
     * @param array $input
     *
     * @return array
     */
    public function transformRequest(array $input)
    {
        if (!isset($input['address'])) {
            $input['address'] = [];
        }

        return $input;
    }

}
