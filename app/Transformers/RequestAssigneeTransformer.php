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
        $user = $model->user;
        $response =  $this->transformAssignee($model);
        $response['assignee_type'] = $model->type;
        if ($user) {
            $response['first_name'] = $user->property_manager->first_name ?? $user->service_provider->first_name ?? '';
            $response['last_name'] =  $user->property_manager->last_name ?? $user->service_provider->last_name ?? '';
        } else {
            $response['first_name'] = '';
            $response['last_name'] =  '';

        }
        return $response;
    }
}
