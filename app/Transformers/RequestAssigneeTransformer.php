<?php

namespace App\Transformers;

use App\Models\RequestAssignee;

/**
 * Class RequestTransformer
 *
 * @package namespace App\Transformers;
 */
class RequestAssigneeTransformer extends AssigneeTransformer
{
    /**
     * Transform the ServiceProvider entity.
     *
     * @param RequestAssignee $model
     *
     * @return array
     */
    public function transform(RequestAssignee $model)
    {
        $response =  $this->transformAssignee($model);
        $response['assignee_type'] = $model->type;
        return $response;
    }
}
