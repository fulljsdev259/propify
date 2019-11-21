<?php

namespace App\Transformers;

use App\Models\BuildingAssignee;

/**
 * Class BuildingAssigneeTransformer
 *
 * @package App\Transformers
 */
class BuildingAssigneeTransformer extends AssigneeTransformer
{
    /**
     * Transform the ServiceProvider entity.
     *
     * @param BuildingAssignee $model
     *
     * @return array
     */
    public function transform(BuildingAssignee $model)
    {
        $response = $this->transformAssignee($model);;
        $response['user_id'] = $model->user_id;
        return $response;
    }
}
