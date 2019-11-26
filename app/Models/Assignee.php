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
        $model = Relation::$morphMap[$this->assignee_type] ?? $this->assignee_type;
        if (! class_exists($model)) {
            return [];
        }
        $eventConvig = [
            User::class => AuditableModel::EventUserUnassigned,
            ServiceProvider::class => AuditableModel::EventProviderUnassigned,
            PropertyManager::class => AuditableModel::EventManagerUnassigned,
        ];

        return [$eventConvig[$model], ['ids' => [$this->assignee_id]]];
    }
}
