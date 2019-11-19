<?php

namespace App\Http\Requests\API\Media;

use App\Http\Requests\BaseRequest;

/**
 * Class ResidentDeleteRequest
 * @package App\Http\Requests\API\Media
 */
class ResidentDeleteRequest extends BaseRequest
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
}
