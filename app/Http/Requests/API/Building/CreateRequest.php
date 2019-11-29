<?php

namespace App\Http\Requests\API\Building;

use App\Http\Requests\BaseRequest;
use App\Models\Building;

class CreateRequest extends BaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->can('add-building');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'floor_nr' => 'required',
            'under_floor' => 'numeric|between:0,3',
            'types' => [
                'array',
                'bail',
                function ($attribute, $value, $fails) {
                    $diff = array_diff($value, array_keys(Building::Type));
                    if ($diff) {
                        $fails(sprintf('This [%s] types is wrong', implode(', ', $diff)));
                    }
                }
            ],
        ];
    }
}
