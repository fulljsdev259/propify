<?php

namespace App\Http\Requests\API\Resident;

use App\Models\Resident;
use App\Http\Requests\BaseRequest;
use Illuminate\Validation\Rule;

class UpdateDefaultContractRequest extends BaseRequest
{

    public function authorize()
    {
        return $this->user()->resident;
    }


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
             'default_contract_id' => [
                 'required',
                 Rule::exists('contracts', 'id')->where(function ($query) {
                     $query->where('resident_id', $this->user()->resident->id);
                 }),
             ]
        ];
    }
}
