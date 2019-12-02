<?php

namespace App\Transformers;

use App\Models\Building;
use App\Models\Relation;
use App\Models\Request;
use App\Models\Resident;

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
     */
    public function transform(Building $model)
    {
        $response = $this->getAttributesIfExists($model, [
            'id',
            'building_format',
            'types',
            'label',
            'contact_enable',
            'description',
            'floor_nr',
            'under_floor',
            'basement',
            'attic',
            'quarter_id',
            'address_id',
            'internal_building_id',
            'units_count',
            'global_email_receptionist',
            'has_email_receptionists', // @TODO delete
            'count_of_apartments_units'
        ]);

        // @TODO maybe delete
        $response['created_at'] = $model->created_at ? $model->created_at->format('Y-m-d') : '';
        $response['residents_count'] = 0;
        $response['active_residents_count'] = 0;
        $response['in_active_residents_count'] = 0;
        $response['property_managers_count'] = 0;

        $withCount = $model->getStatusRelationCounts();
        $response = array_merge($response, $withCount);

        $response = $this->includeRelationIfExists($model, $response, [
            'address' => AddressTransformer::class,
//            'service_providers' => ServiceProviderTransformer::class,
            'media' => MediaTransformer::class,
        ]);

        if ($model->relationExists('units')) {
            $response['units'] = (new UnitTransformer())->transformCollectionBy($model->units, 'transformForIndex');
            $statusCounts = $this->getUnitsStatus($response);
            $response = array_merge($response, $statusCounts);
            $requestStatusCounts = (new Request())->request_status_columns;
            $unitsData = collect( $response['units'] );
            foreach ($requestStatusCounts as $requestStatusCount) {
                $response[$requestStatusCount] = $unitsData->sum($requestStatusCount);
            }
        }

        $response['users'] = [];
        if ($model->relationExists('quarter')) {
            $response['quarter'] = (new QuarterTransformer)->transform($model->quarter);
            $response['users'] = $response['quarter']['users'] ?? [];
            $response[ 'internal_quarter_id'] = $model->quarter->internal_quarter_id;
        } else  {
            $model->load('quarter:id,internal_quarter_id');
            $response[ 'internal_quarter_id'] = $model->quarter->internal_quarter_id ?? '';
        }

        // @TODO $assignedUsers login
//        $assignedUsers = $model->newCollection();
//        if ($model->relationExists('propertyManagers')) {
//            $assignedUsers = $assignedUsers->merge($model->propertyManagers->pluck('user'));
//            $response['managers'] = (new PropertyManagerSimpleTransformer)->transformCollection($model->propertyManagers);
//
//            if ($model->property_managers_count > 2) {
//                $response['property_managers_count'] = $model->property_managers_count - 2;
//            }
//        }

//        if ($model->relationExists('users')) {
//            $assignedUsers = $assignedUsers->merge($model->users);
//            $response['users'] = (new UserTransformer())->transformCollection($model->users);
//        }

//        if ($assignedUsers->count()) {
//            $response['assignedUsers'] = (new UserTransformer)->transformCollection($assignedUsers);
//        } else {
//            $response['assignedUsers'] = [];
//        }

        // @TODO fix now building is not directly assigned relations
        if ($model->relationExists('relations')) {
            $response['relations'] = (new RelationTransformer())->transformCollection($model->relations);
            $residents = collect($response['relations'])->pluck('resident')->unique();


            // @TODO delete
            if ($residents->isNotEmpty()) {
                $response['residents'] = $residents->all();
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

    /**
     * @param $data
     * @return array
     */
    protected function getUnitsStatus($data)
    {
        $unitsCountByStatus = collect($data['units'])->countBy('status');
        $statusCodes = Relation::StatusColorCode;
        $response = [];
        foreach ($statusCodes as $status => $color) {
            $response[Relation::Status[$status] . '_units_count'] = $unitsCountByStatus[$status] ?? 0;
        }
        $response['total_units_count'] = array_sum($response);
        return $response;
    }
}
