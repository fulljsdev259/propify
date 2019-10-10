<?php

namespace App\Http\Requests\API\Contract;

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
        return $this->can('view-resident'); // @TODO add new rule list-contract
    }
}
