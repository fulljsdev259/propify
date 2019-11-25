<?php

namespace App\Http\Requests\API\Resident;

use App\Http\Requests\BaseRequest;
use App\Models\Resident;

class CreateRequest extends BaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->can('add-resident');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'title' => 'required|string',
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'birth_date' => 'date',
            'status' => 'digits_between:1,2|numeric',
        ];
    }
}
