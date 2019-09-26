<?php

namespace App\Http\Requests\API\Media;

use App\Models\Post;
use App\Http\Requests\BaseRequest;

class PostUploadRequest extends BaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $u = \Auth::user();
        if ($u->can('edit-post')) {
            return true;
        }
        return Post::where('id', $this->route('id'))
            ->where('user_id', $u->id)->exists();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'media' => 'required|string',
        ];
    }
}
