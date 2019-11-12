<?php

namespace App\Models;

class Workflow extends Model
{
    public $fillable = [
        'quarter_id',
        'building_ids',
        'cc_property_manager_ids',
        'bcc_property_manager_ids',
    ];

    public $casts = [
        'quarter_id' => 'int',
        'building_ids' => 'array',
        'cc_property_manager_ids' => 'array',
        'bcc_property_manager_ids' => 'array',
    ];

}
