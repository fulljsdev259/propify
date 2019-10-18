<?php

namespace App\Transformers;

use App\Models\Resident;

/**
 * Class ResidentSimpleTransformer.
 *
 * @package namespace App\Transformers;
 */
class ResidentSimpleTransformer extends BaseTransformer
{
    protected $defaultIncludes = [
//        'user',
    ];

    /**
     * Transform the Resident entity.
     *
     * @param Resident $model
     *
     * @return array
     */
    public function transform(Resident $model)
    {
        $response = [
            'id' => $model->id,
            'title' => $model->title,
            'company' => $model->company,
            'first_name' => $model->first_name,
            'last_name' => $model->last_name,
            'birth_date' => $model->birth_date->format('Y-m-d'),
            'mobile_phone' => $model->mobile_phone,
            'private_phone' => $model->private_phone,
            'work_phone' => $model->work_phone,
            'status' => $model->status,
            'resident_format' => $model->resident_format,
            'nation' => $model->nation,
        ];

        if ($model->user) {
            $response['user'] = (new UserTransformer)->transform($model->user);
        }

        return $response;
    }

    /**
     * Include Address
     *
     * @return \League\Fractal\Resource\Item
     */
    public function includeUser(Resident $model)
    {
        return $this->item($model->user, new UserTransformer);
    }
}
