<?php

namespace App\Models;

class Workflow extends Model
{
    public $fillable = [
        'quarter_id',
        'category_id',
        'title',
        'building_ids',
        'to_user_ids',
        'cc_user_ids',
    ];

    public $casts = [
        'quarter_id' => 'int',
        'category_id' => 'int',
        'title' => 'string',
        'building_ids' => 'array',
        'to_user_ids' => 'array',
        'cc_user_ids' => 'array',
    ];

}
