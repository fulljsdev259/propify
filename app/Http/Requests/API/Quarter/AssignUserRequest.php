<?php

namespace App\Http\Requests\API\Quarter;

use App\Http\Requests\BaseRequest;
use App\Models\Quarter;

class AssignUserRequest extends BaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->can('assign-quarter');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'user_id' => 'required|integer',
            'role' => 'required|string|in:administrator,manager,provider',
        ];
    }
}
