<?php

namespace App\Http\Requests\API\Media;

use App\Http\Requests\BaseRequest;

class ResidentDeleteRequest extends BaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // @TODO ROLE RELATED
        return $this->can('edit-resident');
    }
}
