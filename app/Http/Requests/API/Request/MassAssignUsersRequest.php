<?php

namespace App\Http\Requests\API\Request;

use App\Http\Requests\BaseRequest;

class MassAssignUsersRequest extends BaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->can('assign-request');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        // @TODO validate
        return [

        ];
    }
}
