<?php

namespace App\Http\Requests\API\Resident;

use App\Models\Resident;
use App\Http\Requests\BaseRequest;

class AddReviewRequest extends BaseRequest
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
            'resident_id' => 'required|exists:residents,id',
            'review' => 'string',
            'rating' => 'required|between:0,10',
        ];
    }
}
