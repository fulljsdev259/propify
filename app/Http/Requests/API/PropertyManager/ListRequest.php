<?php

namespace App\Http\Requests\API\PropertyManager;

use App\Http\Requests\BaseRequest;
use App\Models\PropertyManager;

class ListRequest extends BaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if ($this->can('list-property_manager')) {
            return true;
        }

        return PropertyManager::where('user_id', $this->user()->id)->exists();
    }
}
