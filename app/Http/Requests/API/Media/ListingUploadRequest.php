<?php

namespace App\Http\Requests\API\Media;

use App\Models\Listing;
use App\Http\Requests\BaseRequest;

class ListingUploadRequest extends BaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $u = \Auth::user();
        if ($u->can('edit-listing')) {
            return true;
        }
        return Listing::where('id', $this->route('id'))
            ->where('user_id', $u->id)->exists();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return $this->getMediaRules(Listing::class);
    }
}
