<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Str;

/**
 * App\Models\QuarterAssignee
 *
 * @SWG\Definition (
 *      definition="QuarterAssignee",
 *      @SWG\Property(
 *          property="id",
 *          description="id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="edit_id",
 *          description="edit_id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="type",
 *          description="related assigner type",
 *          type="string",
 *          format="int32",
 *          enum={"user", "provider", "manager"}
 *      ),
 *      @SWG\Property(
 *          property="name",
 *          description="name",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="email",
 *          description="email",
 *          type="string"
 *      ),
 * )
 * @property int $id
 * @property int $quarter_id
 * @property int $assignee_id
 * @property string $assignee_type
 * @property string|null $created_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\OwenIt\Auditing\Models\Audit[] $audits
 * @property-read int|null $audits_count
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\QuarterAssignee newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\QuarterAssignee newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\QuarterAssignee query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\QuarterAssignee whereAssigneeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\QuarterAssignee whereAssigneeType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\QuarterAssignee whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\QuarterAssignee whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\QuarterAssignee whereQuarterId($value)
 * @mixin \Eloquent
 */
class QuarterAssignee extends AuditableModel
{
    protected $table = 'quarter_assignees';

    public $timestamps = false;

    public $fillable = [
        'quarter_id',
        'assignee_id',
        'assignee_type',
        'created_at',
    ];

    public $auditEvents = [
        AuditableModel::EventDeleted
    ];

    /**
     * {@inheritdoc}
     */
    public function transformAudit(array $data): array
    {
        if (AuditableModel::EventDeleted != $data['event']) {
            return $data;
        }
        $data['auditable_id'] = $this->quarter_id;
        $data['auditable_type'] = get_morph_type_of(\App\Models\Quarter::class);

        [$event, $olddata] = $this->getAuditData();
        $data['old_values'] = $olddata;
        $data['event'] = $event;
        return $data;
    }

    // @TODO make more good way
    protected function getAuditData()
    {
        $model = Relation::$morphMap[$this->assignee_type] ?? $this->assignee_type;
        $config = [
            User::class => ['name'],
            ServiceProvider::class => ['name'],
            PropertyManager::class => ['first_name', 'last_name'],
        ];
        if (! class_exists($model)) {
            return [];
        }
        $eventConvig = [
            User::class => AuditableModel::EventUserUnassigned,
            ServiceProvider::class => AuditableModel::EventProviderUnassigned,
            PropertyManager::class => AuditableModel::EventManagerUnassigned,
        ];

        $instance = new $model();
        $columns = array_merge([$instance->getKeyName()], $config[$model] ?? []);
        try {
            $instance = $instance->where($instance->getKeyName(), $this->assignee_id)->withTrashed()->first($columns);
        } catch (\Exception $e) {
            $instance = $instance->where($instance->getKeyName(), $this->assignee_id)->first($columns);
        }

        if (empty($instance)) {
            return [$eventConvig[$model] ?? 'deleted', []];
        }

        $attributes = $instance->getAttributes();
        $prefix = Str::singular($instance->getTable()) . '_';

        $oldData = [];
        foreach ($attributes as $attribute => $value) {
            $oldData[$prefix . $attribute] = $value;
        }

        return [$eventConvig[$model] ?? 'deleted', $oldData];
    }
}
