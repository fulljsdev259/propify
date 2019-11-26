<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Str;

/**
 * App\Models\BuildingAssignee
 *
 * @SWG\Definition (
 *      definition="BuildingAssignee",
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
 * @property int $building_id
 * @property int $assignee_id
 * @property string $assignee_type
 * @property string|null $created_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\OwenIt\Auditing\Models\Audit[] $audits
 * @property-read int|null $audits_count
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingAssignee newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingAssignee newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingAssignee query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingAssignee whereAssigneeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingAssignee whereAssigneeType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingAssignee whereBuildingId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingAssignee whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingAssignee whereId($value)
 * @mixin \Eloquent
 */
class BuildingAssignee extends Assignee
{
    protected $table = 'building_assignees';


    public $fillable = [
        'building_id',
        'assignee_id',
        'user_id',
        'assignee_type',
        'created_at',
    ];

    protected $casts = [
        'building_id' => 'int',
        'assignee_id' => 'int',
        'user_id' => 'int',
        'assignee_type' => 'string',
        'created_at' => 'date',
    ];

    /**
     * {@inheritdoc}
     */
    public function transformAudit(array $data): array
    {
        if (AuditableModel::EventDeleted != $data['event']) {
            return $data;
        }
        $data['auditable_id'] = $this->building_id;
        $data['auditable_type'] = get_morph_type_of(\App\Models\Building::class);

        [$event, $olddata] = $this->getAuditData();
        $data['old_values'] = $olddata;
        $data['event'] = $event;
        return $data;
    }

}
