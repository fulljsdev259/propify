<?php

namespace App\Http\Requests\API\Resident;

use App\Models\Resident;
use App\Http\Requests\BaseRequest;

class UpdateLoggedInRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return Resident::$rules;
    }
}
