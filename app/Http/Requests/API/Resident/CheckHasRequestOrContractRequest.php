<?php

namespace App\Http\Requests\API\Resident;

use App\Models\Resident;
use App\Http\Requests\BaseRequest;

class CheckHasRequestOrContractRequest extends BaseRequest
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
}
