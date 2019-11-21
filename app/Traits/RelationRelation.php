<?php

namespace App\Traits;

use App\Models\Relation;
use Illuminate\Support\Str;

trait RelationRelation
{
    /**
     * @return mixed
     */
    public function relations()
    {
        return $this->hasMany(Relation::class);
    }

    /**
     * @return mixed
     */
    public function active_relations()
    {
        return $this->relations()->where('relations.status', Relation::StatusActive);
    }

    /**
     * @return mixed
     */
    public function inactive_relations()
    {
        return $this->relations()->where('relations.status', Relation::StatusInActive);
    }

    /**
     * @return mixed
     */
    public function canceled_relations()
    {
        return $this->relations()->where('relations.status', Relation::StatusCanceled);
    }

    /**
     * @param $q
     * @return mixed
     */
    public function scopeRelationsStatusCount($q)
    {
        $withCount = [];
        foreach (Relation::Status as $value) {
            $withCount[] = Str::snake($value) . '_relations';
        }
        return $q->withCount($withCount);
    }

    /**
     * @return array
     */
    public function getRelationStatusCounts()
    {
        $statusCounts = [];
        $attributes = $this->getAttributes();

        $isExistOne = false;
        foreach (Relation::Status as $value) {
            $attribute = Str::snake($value) . '_relations_count';

            if (key_exists($attribute, $attributes)) {
                $isExistOne = true;
            }
            $statusCounts[$attribute] = $attributes[$attribute] ?? 0;
        }

        if (! $isExistOne) {
            return [];
        }

        if (key_exists('relations_count', $attributes)) {
            $statusCounts['relations_count'] = $this->relations_count;
        } else {
            $statusCounts['relations_count'] = array_sum($statusCounts);
        }

        return $statusCounts;
    }

    /**
     * @return int
     */
    public function getTotalRelationsCountAttribute()
    {
        $attributes = $this->getAttributes();
        if (key_exists('relations_count', $attributes)) {
            return $this->relations_count;
        }

        $count = 0;
        foreach (Relation::Status as $value) {
            $attribute = Str::snake($value) . '_relations_count';
            $count += $attributes[$attribute] ?? 0;
        }

        return $count;
    }
}
