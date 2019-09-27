<?php

namespace App\Http\Requests\API\Tenant;

use App\Http\Requests\BaseRequest;

class SendCredentialsRequest extends BaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->can('send_credentials-tenant');
    }
}
