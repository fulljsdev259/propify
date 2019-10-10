<?php

namespace App\Http\Requests\API\RentContract;

use App\Http\Requests\BaseRequest;

class ShowRequest extends BaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->can('view-resident'); // @TODO add new rule list-rent_contract
    }
}
