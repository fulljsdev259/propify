<?php

namespace App\Http\Requests\API\PropertyManager;

use App\Models\PropertyManager;
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
        return $this->can('add-property_manager');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = PropertyManager::$rules;
        $rules['position'] = 'in:' . implode(',', PropertyManager::Position);
        $rules['title'] = 'in:' . implode(',', PropertyManager::Title);
        return $rules;
    }
}
