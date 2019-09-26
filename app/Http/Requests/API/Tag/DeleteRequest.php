<?php

namespace App\Http\Requests\API\Tag;

use App\Models\Tag;
use App\Http\Requests\BaseRequest;

class DeleteRequest extends BaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true; // @TODO
        return $this->can('delete-tag');
    }
}
