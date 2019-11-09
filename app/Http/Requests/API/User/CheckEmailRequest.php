<?php

namespace App\Http\Requests\API\User;

use App\Models\PropertyManager;
use App\Http\Requests\BaseRequest;

class CheckEmailRequest extends BaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if ($this->can('add-user')) {
            return true;
        }

        return PropertyManager::where('user_id', $this->user()->id)->exists();
    }
}
