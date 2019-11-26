<?php

namespace App\Http\Requests\API\Request;

use App\Models\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\BaseRequest;
use Illuminate\Validation\Rule;

/**
 * Class CreateRequest
 * @package App\Http\Requests\API\Request
 */
class CreateRequest extends BaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->can('add-request');
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
                'relation_id' => 'required|exists:relations,id',
                'title' => 'required|string',
                'description' => 'required|string',
                'category_id' => 'required|integer',
                'sub_category_id' => 'nullable|integer',
//        'priority' => 'required|integer',
//        'internal_priority' => 'integer',
                'visibility' => 'nullable|integer',
            ];
        }

        $rules = [
            'resident_id' => 'required|exists:residents,id',
            'relation_id' => 'required|exists:relations,id',
            'title' => 'required|string',
            'description' => 'required|string',
//        'priority' => 'required|integer',
//        'internal_priority' => 'integer',
            'due_date' => 'required|date',
            'category_id' => 'required|integer',
            'sub_category_id' => 'nullable|integer',
            'visibility' => 'nullable|integer',
        ];


        $rules['reminder_user_ids'] = [
            'nullable',
            Rule::requiredIf(function () {
                return $this->active_reminder == true;
            }),
            'array'
        ];
        $rules['days_left_due_date'] = Rule::requiredIf(function () {
            return $this->active_reminder == true;
        });

        return $rules;
    }
}
