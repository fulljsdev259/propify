<?php

namespace App\Http\Requests\API\Dashboard;

use App\Http\Requests\BaseRequest;

class BuildingStatisticRequest extends BaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->can('view-buildings_statistics');
    }
}
