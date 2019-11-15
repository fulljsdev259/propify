<?php

namespace App\Http\Requests\API\Workflow;

use App\Http\Requests\BaseRequest;

class ViewRequest extends BaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->can('view-unit');// @TODO discuss add add-workflow or keep this
    }
}
