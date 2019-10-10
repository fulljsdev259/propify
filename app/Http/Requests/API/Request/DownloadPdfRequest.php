<?php

namespace App\Http\Requests\API\Resident;

use App\Http\Requests\BaseRequest;

class DownloadPdfRequest extends BaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->can('download_pdf-request');
    }
}
