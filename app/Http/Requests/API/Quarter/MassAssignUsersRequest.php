<?php

namespace App\Http\Requests\API\Quarter;

use App\Http\Requests\BaseRequest;

class MassAssignUsersRequest extends BaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if (empty($this->data)) {
            $this->merge(['data' => $this->toArray()]);
        }
        return $this->can('assign-quarter');
    }


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'data' => [
                'required',
                function ($attribute, $value, $fails) {
                    foreach ($value as $index => $data) {
                        if (! is_array($data)){
                            $message = __('validation.array', ['attribute' => $index]);
                            return $fails($message);
                        }

                        if (empty($data['user_id'])) {
                            $message = __('validation.required', ['attribute' => $this->getAttributeByKey($index, 'user_id')]);
                            return $fails($message);
                        }
                    }
                }
            ]
        ];
    }

    public function messages()
    {
        return [
            'data.required' => 'Need pass latest one array contains user_id key'
        ];
    }

    /**
     * @param $key
     * @param $name
     * @return string
     */
    protected function getAttributeByKey($key, $name)
    {
        return $key . '.' . $name;
    }
}
