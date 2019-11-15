<?php

namespace App\Http\Requests\API\Workflow;

use App\Models\Request;
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
        return $this->can('add-quarter'); // @TODO discuss add add-workflow or keep this
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        // @TODO validate more detailed
        return [
            'quarter_id' => 'required|exists:quarters,id',
            'category_id' => [
                'required',
                $this->getInRuleByClassConstants(Request::Category)
            ],
            'title' => 'required|string',
            'building_ids' => [
                // @TODO check building belong quarter
                'required',
                'array'
            ],
            'to_user_ids' => [
                // @TODO check users exists and admins
                'required',
                'array'
            ],
            'cc_user_ids' => [
                // @TODO check users exists and admins
                'array'
            ],
        ];
    }
}
