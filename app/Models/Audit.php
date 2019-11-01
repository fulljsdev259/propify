<?php

namespace App\Models;

use OwenIt\Auditing\Models\Audit as BaseAudit;

/**
 * Class Address
 * @package App\Models
 */
class Audit extends BaseAudit
{
    /**
     * {@inheritdoc}
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
