<?php

namespace App\Http\Requests\API\Media;

use App\Http\Requests\BaseRequest;
use App\Models\Resident;

/**
 * Class ResidentUploadRequest
 * @package App\Http\Requests\API\Media
 */
class ResidentUploadRequest extends BaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->can('edit-resident'); // @TODO correct permission
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return $this->getMediaRules(Resident::class);
    }
}
