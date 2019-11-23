<?php

namespace App\Http\Requests\API\Resident;

use App\Models\Resident;
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
        return $this->can('edit-resident')
            || (!empty($this->resident_id) && $this->user()->resident && $this->resident_id  == $this->user()->resident->id);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'default_relation_id' => 'nullable|exists:relations,id',// @TODO check own or not
            'title' => 'string',
            'first_name' => 'string',
            'last_name' => 'string',
            'birth_date' => 'date',
            'status' => 'digits_between:1,2|numeric'
        ];
    }
}
