<?php

namespace App\Http\Requests\API\ServiceProvider;

use App\Models\ServiceProvider;
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
        return $this->can('add-provider');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        // @TODO address , user validation correctly
        return [
            'company_name' => 'required|string|max:255',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255',
            'phone' => 'required|string|max:255',
            'user' => 'required',
            'address' => 'required',
            'status' => [
                'required',
                $this->getInRuleByClassConstants(ServiceProvider::Status),
            ],
            'type' => [
                'nullable',
                $this->getInRuleByClassConstants(ServiceProvider::Type),
            ],
            'category' => [
                'required',
                $this->getInRuleByClassConstants(ServiceProvider::Category),
            ],
        ];
    }
}
