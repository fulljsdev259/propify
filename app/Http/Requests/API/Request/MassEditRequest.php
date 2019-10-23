<?php

namespace App\Http\Requests\API\Request;

use App\Models\PropertyManager;
use App\Models\Request;
use App\Models\ServiceProvider;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\BaseRequest;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class MassEditRequest extends BaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->can('edit-request');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'request_ids' => [
                'bail',
                'required',
                'array',
                function ($attribute, $value, $fails) {
                    $requests = Request::whereIn('id', $value)->get();

                    if(count($value) != $requests->count()) {
                        return $fails('One of selected request is not valid');
                    }
                    $this->merge(compact('requests'));
                }
            ],
            'property_manager_ids' => [
                'bail',
                'required_without_all:status,service_provider_ids',
                'array',
                function ($attribute, $value, $fails) {
                    $managers = PropertyManager::whereIn('id', $value)->get(['id']);

                    if(count($value) != $managers->count()) {
                        return $fails('One of selected property manager is not valid');
                    }
                    $this->merge(compact('managers'));
                }
            ],
            'service_provider_ids' => [
                'bail',
                'required_without_all:status,property_manager_ids',
                'array',
                function ($attribute, $value, $fails) {
                    $providers = ServiceProvider::whereIn('id', $value)->get(['id']);

                    if(count($value) != $providers->count()) {
                        return $fails('One of selected service_providers is not valid');
                    }
                    $this->merge(compact('providers'));
                }
            ],
            'status' => [
                'required_without_all:service_provider_ids,property_manager_ids',
                'in:' . implode(',', array_keys(Request::Status)),
            ],
        ];

        return $rules;
    }
}
