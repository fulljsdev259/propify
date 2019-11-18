<?php

namespace App\Http\Requests\API\Unit;

use App\Models\Unit;
use App\Http\Requests\BaseRequest;

class UpdateRequest extends BaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->can('edit-unit');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
         return [
            'building_id' => 'required_without:quarter_id|integer|nullable',
            'quarter_id' => 'required_without:building_id|integer|nullable',
            'type' => 'required|integer',
            'name' => 'required|string',
            'floor' => 'required'
        ];
    }
}
