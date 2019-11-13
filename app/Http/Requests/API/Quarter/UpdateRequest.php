<?php

namespace App\Http\Requests\API\Quarter;

use App\Models\Quarter;
use App\Http\Requests\BaseRequest;
use App\Rules\WorkflowRule;

class UpdateRequest extends BaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->can('edit-quarter');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'string',
            'type' => $this->getInRuleByClassConstants(Quarter::Type),
            'types' => [
                'required',
                'array',
                'bail',
                function ($attribute, $value, $fails) {
                    $diff = array_diff($value, array_keys(Quarter::Type));
                    if ($diff) {
                        $fails(sprintf('This [%s] types is wrong', implode(', ', $diff)));
                    }
                }
            ],
            'workflows' => [
                new WorkflowRule()
            ]
        ];
    }
}
