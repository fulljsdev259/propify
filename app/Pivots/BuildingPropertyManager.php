<?php

namespace App\Pivots;

use App\Models\Building;
use App\Models\PropertyManager;
use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * App\Pivots\BuildingPropertyManager
 *
 * @property-read \App\Models\Building $building
 * @property-read \App\Models\PropertyManager $propertyManager
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Building[] $quarters
 * @property-read int|null $quarters_count
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Pivots\BuildingPropertyManager newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Pivots\BuildingPropertyManager newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Pivots\BuildingPropertyManager query()
 * @mixin \Eloquent
 */
class BuildingPropertyManager extends Pivot
{

    public function building()
    {
        return $this->belongsTo(Building::class);
    }

    public function propertyManager()
    {
        return $this->belongsTo(PropertyManager::class);
    }

    public function quarters()
    {
        return $this->hasManyThrough(Building::class, PropertyManager::class);
    }
}
