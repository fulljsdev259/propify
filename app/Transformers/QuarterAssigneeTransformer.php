<?php

namespace App\Transformers;

use App\Models\QuarterAssignee;

/**
 * Class QuarterAssigneeTransformer
 *
 * @package App\Transformers
 */
class QuarterAssigneeTransformer extends AssigneeTransformer
{
    /**
     * Transform the ServiceProvider entity.
     *
     * @param QuarterAssignee $model
     *
     * @return array
     */
    public function transform(QuarterAssignee $model)
    {
        $response = $this->transformAssignee($model);

        $response['user_id'] = $model->user_id;
        $response['place'] = $model->place;
        $response['assigned_in'] = $model->type;
        return $response;
    }
}
