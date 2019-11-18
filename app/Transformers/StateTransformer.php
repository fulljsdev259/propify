<?php

namespace App\Transformers;

use App\Models\State;
use App\Repositories\UserRepository;
use Auth;
use Config;

/**
 * Class StateTransformer.
 *
 * @package namespace App\Transformers;
 */
class StateTransformer extends BaseTransformer
{
    /**
     * @param State $model
     * @return array
     */
    public function transform(State $model)
    {
        $response = [
            'id' => $model->id,
            'country_id' => $model->country_id,
            'code' => $model->code,
            'name' => get_translated_filed($model, 'name'),
            'abbreviation' => $model->abbreviation,
        ];

        return $response;
    }
}
