<?php

namespace App\Transformers;

use App\Models\Building;
use App\Models\Quarter;
use App\Models\User;
use App\Models\Workflow;

/**
 * Class WorkflowTransformer
 * @package App\Transformers
 */
class WorkflowTransformer extends BaseTransformer
{
    /**
     * @param Quarter $model
     * @return array
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function transform(Workflow $model)
    {
        $response = [
            'id' => $model->id,
            'quarter_id' => $model->quarter_id,
            'category_id' => $model->category_id,
            'title' => $model->title,
            'building_ids' => $model->building_ids,
            'to_user_ids' => $model->to_user_ids,
            'cc_user_ids' => $model->cc_user_ids,
        ];

        $toUserIds = $model->to_user_ids ?? [];
        $ccUserIds = $model->cc_user_ids ?? [];
        $userIds = array_merge($toUserIds, $ccUserIds);
        $users = User::whereIn('id', $userIds)->get(['id', 'name']);

        $response['to_users'] = $users->whereIn('id', $toUserIds)->values()->transform( function ($user) {
            return $user->only(['id', 'name']);
        });
        $response['cc_users'] = $users->whereIn('id', $ccUserIds)->values()->transform( function ($user) {
            return $user->only(['id', 'name']);
        });

        $buildingIds = $model->building_ids ?? [];
        $buildings = Building::whereIn('id', $buildingIds)->with('address')->get();
        $response['buildings'] = (new BuildingTransformer())->transformCollection($buildings);
        $response['category'] = $model->category_id ? get_category_details($model->category_id) : [];

        return $response;
    }
}
