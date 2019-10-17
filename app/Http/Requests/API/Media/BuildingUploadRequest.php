<?php

namespace App\Http\Requests\API\Media;

use App\Http\Requests\BaseRequest;
use App\Models\Building;

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
        return $this->getFileCategoryRules(Building::class);
    }
}
