<?php

namespace App\Http\Requests\API\Media;

use App\Http\Requests\BaseRequest;
use App\Models\Building;
use Illuminate\Support\Facades\Validator;

class BuildingUploadRequest extends BaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->can('edit-building');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $permittedExtensions = (new Building())->getPermittedExtensions();

        $categories = Building::BuildingMediaCategories;
        $rules = [];
        foreach ($categories as $category) {
            $requiredWithout = implode('_upload,', array_diff($categories, [$category])) . '_upload';
            $rules[$category . '_upload'] = [
                sprintf('required_without_all:%s|string', $requiredWithout),
                'base_mimes:' . implode(',', $permittedExtensions)
            ];
        }


        return $rules;
    }
}
