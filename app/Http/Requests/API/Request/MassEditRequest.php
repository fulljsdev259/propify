<?php

namespace App\Http\Requests\API\Request;

use App\Models\Request;
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
            'ids' => [
                'required',
                'array',
                function ($attribute, $value, $fails) {
                    $requests = Request::whereIn('id', $value)->get();

                    if(count($value) != $requests->count()) {
                        return $fails('One ov selected request is not valid');
                    }
                    $this->merge(compact('requests'));
                }
            ],
            'attributes' => [
                'required',
                'array',
                function($attribute, $value, $fails) {
                    $rules =  Request::$rulesPut;
                    $rules['reminder_user_id'] = Rule::requiredIf(function () {
                        return $this->active_reminder == true;
                    });
                    $rules['days_left_due_date'] = $rules['reminder_user_id'];
                    $validator = Validator::make($value, $rules);
                    if ($validator->fails()) {
                        $messageBug = $validator->errors()->getMessageBag();
                        $messageBug->setFormat('One of attribute is not correct');
                        return $fails($messageBug);
                    }
                }
            ]
        ];

        return $rules;
    }
}
