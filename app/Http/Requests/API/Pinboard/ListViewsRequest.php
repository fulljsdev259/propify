<?php

namespace App\Http\Requests\API\Pinboard;

use App\Http\Requests\BaseRequest;

class ListViewsRequest extends BaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->can('list_views-pinboard') || $this->user()->resident;
    }
}
