<?php

namespace App\Http\Requests\API\InternalNotice;

use App\Http\Requests\BaseRequest;
use App\Models\InternalNotice;
use Illuminate\Support\Facades\Auth;

class UpdateRequest extends BaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if (! $this->can('edit-internal_notice')) {
            return false;
        }

        return InternalNotice::where('user_id', Auth::id())->where('id', $this->route('internalNotice'))->exists();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'request_id' => 'required',
            'comment' => 'required'
        ];
    }
}
