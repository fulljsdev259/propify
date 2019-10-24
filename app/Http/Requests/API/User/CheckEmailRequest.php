<?php

namespace App\Http\Requests\API\User;

use App\Models\User;
use App\Http\Requests\BaseRequest;

class CheckEmailRequest extends BaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->can('add-user');
    }
}
