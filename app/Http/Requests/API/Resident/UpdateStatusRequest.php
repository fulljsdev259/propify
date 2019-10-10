<?php

namespace App\Http\Requests\API\Resident;

use App\Http\Requests\BaseRequest;

class UpdateStatusRequest extends BaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->can('edit-resident');
    }
}
