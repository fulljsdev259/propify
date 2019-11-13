<?php

namespace App\Transformers;

use App\Models\Quarter;
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

        return $response;
    }
}
