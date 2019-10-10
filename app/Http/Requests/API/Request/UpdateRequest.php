<?php

namespace App\Http\Requests\API\Request;

use App\Models\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\BaseRequest;
use Illuminate\Validation\Rule;

class UpdateRequest extends BaseRequest
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
        $user = Auth::user();
        if ($user->can('edit-request_resident')) {
            return Request::$rulesPutResident;
        }

        if ($user->can('edit-request_service')) {
            return Request::$rulesPutService;
        }
        $putRoles =  Request::$rulesPut;
        $putRoles['reminder_user_id'] = Rule::requiredIf(function () {
            return $this->active_reminder == true;
        });
        $putRoles['days_left_due_date'] = $putRoles['reminder_user_id'];

        return $putRoles;
    }
}
