<?php

namespace App\Transformers;

use App\Models\Country;

/**
 * Class StateTransformer.
 *
 * @package namespace App\Transformers;
 */
class CountryTransformer extends BaseTransformer
{
    /**
     * @param Country $model
     * @return array
     */
    public function transform(Country $model)
    {
        $response = [
            'id' => $model->id,
            'code' => $model->code,
            'alpha_3' => $model->alpha_3,
            'name' => get_translated_filed($model, 'name'),
        ];

        return $response;
    }
}
