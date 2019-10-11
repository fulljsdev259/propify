<?php

namespace App\Http\Requests\API\Media;

use Illuminate\Support\Facades\Auth;
use App\Http\Requests\BaseRequest;

class RequestDeleteRequest extends BaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $user = Auth::user();

        if (!$user->can(['edit-request_resident', 'edit-request_service', 'edit-request'])) {
            return false;
        }

        return true;
    }
}
