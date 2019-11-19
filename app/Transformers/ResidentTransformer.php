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
        $response = [
            'id' => $model->id,
            'default_relation_id' => $model->default_relation_id,
            'title' => $model->title,
            'company' => $model->company,
            'first_name' => $model->first_name,
            'last_name' => $model->last_name,
            'birth_date' => $model->birth_date ? $model->birth_date->format('Y-m-d') : null,
            'birth_date_formatted' => $model->birth_date ? $model->birth_date->format('d.m.Y') : null,
            'mobile_phone' => $model->mobile_phone,
            'private_phone' => $model->private_phone,
            'work_phone' => $model->work_phone,
            'status' => $model->status,
            'resident_format' => $model->resident_format,
            'nation' => $model->nation,
            'type' => $model->type,
            'tenant_type' => $model->tenant_type,
            'review' => $model->review,
            'rating' => $model->rating,
            'created_by' => $model->created_by,
        ];

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
            $activeCount = $model->relations->where('status', Relation::StatusActive)->count();

            $response['active_relations_count'] = $activeCount;
            $response['inactive_relations_count'] = $allCount - $activeCount;
            $response['total_relations_count'] = $allCount;
        }

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
