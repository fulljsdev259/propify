<?php

namespace App\Transformers;

use App\Models\Address;
use League\Fractal\TransformerAbstract;

/**
 * Class AddressTransformer.
 *
 * @package namespace App\Transformers;
 */
class AddressTransformer extends BaseTransformer
{
    /**
     * @param Address $model
     * @return array
     */
    public function transform(Address $model)
    {
        $response = $this->getAttributesIfExists($model, [
            'id',
            'country_id',
            'city',
            'street',
            'zip',
            'house_num'
        ]);
        $response['country'] = 'Switzerland';

        if ($model->relationExists('state') || $model->state) { // @TODO delete $model->state and each case if need select also state
            $response['state'] = (new StateTransformer)->transform($model->state);
        }

        return $response;
    }

    /**
     * Transform Request to Address entity.
     *
     * @param array $input
     *
     * @return array
     */
    public function transformRequest(array $input)
    {
        $address = $input['address'] ?? [];
        if (!count($address)) {
            return [];
        }

        $address['state_id'] = $address['state']['id'];
        unset($address['state']);
        unset($address['country']);

        return $address;
    }

}
