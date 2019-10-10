<?php

namespace App\Http\Requests\API\Request;

use App\Models\Request;
use App\Models\RequestCategory;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\BaseRequest;

/**
 * Class CreateRequest
 * @package App\Http\Requests\API\Request
 */
class CreateRequest extends BaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->can('add-request');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $user = Auth::user();
        if ($user->resident) { // @TODO @TODO
            return Request::$rulesPostResident;
        }

        $rules = Request::$rulesPost;
        $requestCategories = RequestCategory::where('has_qualifications', 1)->pluck('id');
        $rules['qualification'] = 'required_if:category_id,'  . $requestCategories->implode(',') .  '|integer';// todo improve permit only specific integers
        return $rules;
    }
}
