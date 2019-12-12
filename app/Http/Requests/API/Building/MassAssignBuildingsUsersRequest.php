<?php

namespace App\Http\Requests\API\Building;

use App\Http\Requests\BaseRequest;
use App\Models\BuildingAssignee;
use App\Models\User;

class MassAssignBuildingsUsersRequest extends BaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->can('assign-building');
    }


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'building_ids' => 'required|array',
            'user_ids' => 'required|array',
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
