<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Str;

/**
 * App\Models\RequestAssignee
 *
 * @SWG\Definition (
 *      definition="RequestAssignee",
 *      @SWG\Property(
 *          property="id",
 *          description="id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="item_id",
 *          description="related assigner id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="edit_id",
 *          description="@TODO must be delete",
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
 *          description="emai;",
 *          type="string"
 *      ),
 * )
 * @property int $id
 * @property int $request_id
 * @property int $assignee_id
 * @property string $assignee_type
 * @property string|null $created_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\OwenIt\Auditing\Models\Audit[] $audits
 * @property-read int|null $audits_count
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\RequestAssignee newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\RequestAssignee newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\RequestAssignee query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\RequestAssignee whereAssigneeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\RequestAssignee whereAssigneeType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\RequestAssignee whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\RequestAssignee whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\RequestAssignee whereRequestId($value)
 * @mixin \Eloquent
 */
class RequestAssignee extends Assignee
{
    protected $table = 'request_assignees';

    public $fillable = [
        'request_id',
        'assignee_id',
        'user_id',
        'assignee_type',
        'sent_email',
        'created_at',
    ];

    protected $casts = [
        'sent_email' => 'boolean'
    ];

    /**
     * {@inheritdoc}
     */
    public function transformAudit(array $data): array
    {
        if (AuditableModel::EventDeleted != $data['event']) {
            return $data;
        }
        $data['auditable_id'] = $this->request_id;
        $data['auditable_type'] = get_morph_type_of(\App\Models\Request::class);

        [$event, $olddata] = $this->getAuditData();
        $data['old_values'] = $olddata;
        $data['event'] = $event;
        return $data;
    }
}
