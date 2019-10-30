<?php

namespace App\Transformers;

use App\Models\EmailReceptionist;

/**
 * Class EmailReceptionistTransformer.
 *
 * @package namespace App\Transformers;
 */
class EmailReceptionistTransformer extends BaseTransformer
{
    /**
     * Transform the User entity.
     *
     * @param \App\Models\User $model
     *
     * @return array
     */
    public function transform(EmailReceptionist $model)
    {
        return $model->toArray();// @TODO correct response

        return $response;
    }
}
