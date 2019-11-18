<?php

namespace App\Http\Requests\API\Resident;

use App\Models\Resident;
use App\Http\Requests\BaseRequest;

class UpdateRequest extends BaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->can('edit-resident')
            || (!empty($this->resident_id) && $this->user()->resident && $this->resident_id  == $this->user()->resident->id);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'type' => [
                'in:' . implode(',', array_keys(Resident::Type)),
                function ($attribute, $value, $fails) {
                    $residentId = $this->route('id');
                    $resident = Resident::withCount('requests', 'contracts')->find($residentId);

                    if (empty($resident)) {
                        return $fails(__('models.resident.errors.not_found'));
                    }

                    if ($resident->type == $value) {
                        return true;
                    }

                    if ($resident->requests_count && $resident->contracts_count) {
                        return $fails(__('models.resident.errors.not_allowed_change_type_has_request_contract', $resident->only('contracts_count', 'requests_count')));
                    }

                    if ($resident->contracts_count) {
                        return $fails(__('models.resident.errors.not_allowed_change_type_has_contract', $resident->only('contracts_count')));
                    }

                    if ($resident->requests_count) {
                        return $fails(__('models.resident.errors.not_allowed_change_type_has_request', $resident->only('requests_count')));
                    }
                }
            ],
            'tenant_type' => [
                'nullable',
                // @TODO validate properly
            ],
            'default_contract_id' => 'nullable|exists:contracts,id',// @TODO check own or not
            'title' => 'string',
            'first_name' => 'string',
            'last_name' => 'string',
            'birth_date' => 'date',
            'status' => 'digits_between:1,2|numeric'
        ];
    }
}
