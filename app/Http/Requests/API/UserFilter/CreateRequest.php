<?php

namespace App\Http\Requests\API\UserFilter;

use App\Http\Requests\BaseRequest;

class CreateRequest extends BaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true; // @TODO
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'user_id' => 'required|integer|exists:users,id',
            'title' => 'required|string',
            'menu' => 'required|string',
            'options_url' => 'required|string',
            'fields_data' => 'array'
        ];
    }
}
