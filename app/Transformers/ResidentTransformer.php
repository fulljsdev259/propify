<?php

namespace App\Transformers;

use App\Models\Building;
use App\Models\Relation;
use App\Models\Resident;

/**
 * Class ResidentTransformer.
 *
 * @package namespace App\Transformers;
 */
class ResidentTransformer extends BaseTransformer
{

    /**
     * @param Resident $model
     * @return array
     */
    public function transform(Resident $model)
    {
        $response = $this->getAttributesIfExists($model, [
            'id',
            'default_relation_id',
            'title',
            'company',
            'first_name',
            'last_name',
            'birth_date',
            'mobile_phone',
            'private_phone',
            'work_phone',
            'status',
            'resident_format',
            'nation',
            'review',
            'rating',
            'created_by' => $model->created_by,
        ]);
        $response['birth_date_formatted'] = $model->birth_date ? $model->birth_date->format('d.m.Y') : null;
        $response['created_by'] = $model->created_by;

        $withCount = $model->getStatusRelationCounts();
        $response = array_merge($response, $withCount);

        if ($model->relationExists('settings')) {
            $response['settings'] = $model->settings;
        }

        if ($model->relationExists('default_relation')) {
            $response['default_relation'] = (new RelationTransformer())->transform($model->default_relation);
        }

        if ($model->relationExists('user')) {
            $response['user'] = (new UserTransformer)->transform($model->user);
        }

        $response['media'] = [];
        if ($model->relationExists('media')) {
            $response['media'] = (new MediaTransformer())->transformCollection($model->media);
        }

        if ($model->relations || $model->relationExists('relations')) { // @TODO delete reloading
            $response['relations'] = (new RelationTransformer())->transformCollection($model->relations);

            $allCount = $model->relations->count();

            foreach (Relation::Status as $const => $status) {
                $response[$status . '_relations_count'] = $model->relations->where('status', $const)->count();;
            }
            $response['total_relations_count'] = $allCount;

            if ([Relation::StatusInActive] == collect($response['relations'])->pluck('status')->all()) {
                $response['types'] = [Relation::TypeFormerResident];
            } else {
                $response['types'] = collect($response['relations'])->pluck('type')->unique()->values()->all();
            }
        }
//
//        if ( $model->relationExists('garant_relations')) { // @TODO delete reloading
//
//
//            $response['relations'] = $response['relations'] ?? [];
//            $garantRelationData = (new RelationTransformer())->transformCollection($model->garant_relations);
//            foreach ($garantRelationData as $single) {
//                $single['garant'] = 1;
//                $response['relations'][] = $single;
//            }
//        }

        return $this->addAuditIdInResponseIfNeed($model, $response);
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
        if (!isset($input['user'])) {
            $input['user'] = [];
            return $input;
        }

        if (!isset($input['user']['password'])) {
           $input['user']['password'] = str_random(8);
        }

        $input['user']['name'] = sprintf('%s %s', $input['first_name'], $input['last_name']);

        return $input;
    }

    /**
     * Include Address
     *
     * @param Building $building
     *
     * @return \League\Fractal\Resource\Item
     */
    public function includeAddress(Building $building)
    {
        $address = $building->address;

        return $this->item($address, new AddressTransformer);
    }

    /**
     * @param Resident $model
     * @return array
     */
    public function myNeighbours(Resident $model)
    {
        return [
            'id' => $model->id,
            'first_name' => $model->first_name,
            'last_name' => $model->last_name,
            'avatar' => $model->user->avatar ?? null,
        ];
    }
}
