<?php

namespace App\Http\Requests;

use InfyOm\Generator\Request\APIRequest;

class BaseRequest extends APIRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [];
    }

    /**
     * @param $permission
     * @return mixed
     */
    protected function can($permission)
    {
        return $this->user()->can($permission);
    }

    /**
     * @param $class
     * @return array
     */
    protected function getFileCategoryRules($class)
    {
        $permittedExtensions = (new $class())->getPermittedExtensions();

        $categories = \ConstFileCategories::MediaCategories;
        $rules = [];
        foreach ($categories as $category) {
            $requiredWithout = implode('_upload,', array_diff($categories, [$category])) . '_upload';
            $rules[$category . '_upload'] = [
                'required_without_all:' . $requiredWithout,
                'string',
                'base_mimes:' . implode(',', $permittedExtensions)
            ];
        }
        return $rules;
    }

    /**
     * @param $class
     * @return array
     */
    protected function getMediaRules($class)
    {
        $permittedExtensions = (new $class())->getPermittedExtensions();
        return [
            'media' => [
                'required',
                'string',
                'base_mimes:' . implode(',', $permittedExtensions)
            ]
        ];
    }
}
