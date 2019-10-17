<?php

namespace App\Http\Requests\API\Media;

use App\Models\Pinboard;
use App\Http\Requests\BaseRequest;

class PinboardUploadRequest extends BaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $u = \Auth::user();
        if ($u->can('edit-pinboard')) {
            return true;
        }
        return Pinboard::where('id', $this->route('id'))
            ->where('user_id', $u->id)->exists();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return $this->getMediaRules(Pinboard::class);
    }
}
