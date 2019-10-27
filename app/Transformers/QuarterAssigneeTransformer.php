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
        return $this->transformAssignee($model);
    }
}
