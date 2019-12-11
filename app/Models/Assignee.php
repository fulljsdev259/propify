<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Str;

/**
 * Class Assignee
 * @package App\Models
 */
class Assignee extends AuditableModel
{
    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @var array
     */
    public $auditEvents = [
        AuditableModel::EventDeleted
    ];

    // @TODO make more good way
    protected function getAuditData()
    {
        return [ AuditableModel::EventUserUnassigned, ['ids' => [$this->user_id]]];
    }
}
