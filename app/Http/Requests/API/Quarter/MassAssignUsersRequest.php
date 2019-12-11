<?php

namespace App\Http\Requests\API\Quarter;

use App\Http\Requests\BaseRequest;
use App\Models\BuildingAssignee;

class MassAssignUsersRequest extends BaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if (empty($this->data)) {
            $this->merge(['data' => $this->toArray()]);
        }
        return $this->can('assign-quarter');
    }


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'data' => [
                'required',
                function ($attribute, $value, $fails) {
                    foreach ($value as $index => $data) {
                        if (! is_array($data)){
                            $message = __('validation.array', ['attribute' => $index]);
                            return $fails($message);
                        }

                        if (empty($data['user_id'])) {
                            $message = __('validation.required', ['attribute' => $this->getAttributeByKey($index, 'user_id')]);
                            return $fails($message);
                        }
                    }

                    $userIds = collect($value)->pluck('user_id');
                    $buildingAssignees = BuildingAssignee::whereIn('user_id', $userIds)
                        ->select('user_id', 'building_id')
                        ->with('user:id,name', 'building:id,building_format')
                        ->whereHas('building', function ($q) {
                            $q->where('quarter_id', $this->route('id'));
                        })
                        ->get();
                    if ($buildingAssignees->isNotEmpty()) {
                        return $fails($this->getAlreadyAssignedBuildingMessage($buildingAssignees));
                    }
                }
            ]
        ];
    }

    public function messages()
    {
        return [
            'data.required' => 'Need pass latest one array contains user_id key'
        ];
    }

    /**
     * @param $key
     * @param $name
     * @return string
     */
    protected function getAttributeByKey($key, $name)
    {
        return $key . '.' . $name;
    }

    protected function getAlreadyAssignedBuildingMessage($buildingAssignees)
    {
        $messages = '';
        foreach ($buildingAssignees->groupBy('building.building_format') as $buildingFormat => $items) {
            $users = $items->pluck('user.name')->implode(', ');
            $messages .= sprintf('In %s building already assigned %s users. ', $buildingFormat, $users);
        }

        $messages .= 'Please firstly unassign them in building and then again try assign to quarter';
        return $messages;
    }
}
