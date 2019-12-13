<?php

namespace App\Traits;


use App\Models\Building;

trait BuildingRelation
{
    /**
     * @return mixed
     */
    public function buildings()
    {
        return $this->belongsToMany(Building::class, 'building_assignees', 'user_id', 'building_id', 'user_id');
    }
}
