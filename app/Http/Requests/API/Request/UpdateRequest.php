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
        if ($user->resident) {
            return [
                'resident_id' => 'exists:residents,id',
                'relation_id' => 'exists:relations,id',
                'title' => 'string',
                'description' => 'string',
                'status' => 'integer',
//        'priority' => 'required|integer',
//        'internal_priority' => 'integer',
                'visibility' => 'nullable|integer',
            ];
        }

        $putRoles = [

            'resident_id' => 'exists:residents,id',
            'relation_id' => 'exists:relations,id',
            'title' => 'string',
            'description' => 'string',
//        'priority' => 'integer',
//        'internal_priority' => 'integer',
            'status' => 'integer',
            'due_date' => 'date',
            'category_id' => 'integer',
            'sub_category_id' => 'nullable|integer',
            'visibility' => 'nullable|integer',
//            'active_reminder' => 'boolean',
        ];
        $putRoles['reminder_user_ids'] = [
            Rule::requiredIf(function () {
                return $this->active_reminder == true;
            }),
            'nullable',
            'array'
        ];
        $putRoles['days_left_due_date'] = Rule::requiredIf(function () {
            return $this->active_reminder == true;
        });

        return $putRoles;
    }
}
