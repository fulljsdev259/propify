<?php

namespace App\Http\Requests\API\Relation;

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
        return $this->can('add-resident'); // @TODO use correct permission
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'resident_id' => 'required|integer|exists:residents,id',
            'building_id' => 'required|integer|exists:buildings,id',
            'unit_id' => 'required|integer|exists:units,id',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'status' => 'digits_between:1,2|numeric',
            'deposit_type' => 'digits_between:1,4|numeric',
            'deposit_status' => 'digits_between:1,2|numeric',
            'deposit_amount' => 'numeric',
        ];
    }
}
