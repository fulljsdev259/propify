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
class QuarterAssignee extends Assignee
{
    protected $table = 'quarter_assignees';

    public $fillable = [
        'quarter_id',
        'user_id',
        'created_at',
    ];

    protected $dates = [
        'created_at'
    ];

    protected $casts = [
        'quarter_id' => 'int',
        'user_id' => 'int',
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

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function quarter()
    {
        return $this->belongsTo(Quarter::class);
    }
}
