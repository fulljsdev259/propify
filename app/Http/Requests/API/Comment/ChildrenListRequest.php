<?php

namespace App\Http\Requests\API\Comment;

use App\Http\Requests\BaseRequest;

class ChildrenListRequest extends BaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {

        // and comments from all other models
        return true;
    }
}
