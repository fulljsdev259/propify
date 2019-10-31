<?php

namespace App\Transformers;

use App\Models\EmailReceptionist;
use Illuminate\Support\Collection;

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
    }

    /**
     * @param Collection $items
     * @return array
     */
    public function transformEmailReceptionists(Collection $items)
    {

        $response = [];
        foreach ($items->groupBy('category') as $category => $_items) {
            $response[] = [
                'category' => $category,
                'property_managers' => $_items->pluck('property_manager'),
            ];
        }

        return $response;
    }
}
