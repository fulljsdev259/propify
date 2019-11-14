<?php

namespace App\Http\Requests\API\Quarter;

use App\Models\Quarter;
use App\Http\Requests\BaseRequest;
use App\Rules\WorkflowRule;

class CreateRequest extends BaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->can('add-quarter');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|string',
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
