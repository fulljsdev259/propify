<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\InternalNotice
 *
 * @SWG\Definition (
 *      definition="InternalNotice",
 *      required={"request_id", "user_id", "comment"},
 *      @SWG\Property(
 *          property="id",
 *          description="id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="request_id",
 *          description="request_id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="user_id",
 *          description="user_id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="user_ids",
 *          description="user_ids",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="comment",
 *          description="comment",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="created_at",
 *          description="created_at",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="updated_at",
 *          description="updated_at",
 *          type="string",
 *          format="date-time"
 *      )
 * )
 * @property int $id
 * @property int $request_id
 * @property int $user_id
 * @property array|null $user_ids
 * @property string $comment
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\User $user
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\InternalNotice newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\InternalNotice newQuery()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\InternalNotice onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\InternalNotice query()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\InternalNotice whereComment($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\InternalNotice whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\InternalNotice whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\InternalNotice whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\InternalNotice whereManagerIds($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\InternalNotice whereRequestId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\InternalNotice whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\InternalNotice whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\InternalNotice withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\InternalNotice withoutTrashed()
 * @mixin \Eloquent
 */
class InternalNotice extends AuditableModel
{
    use SoftDeletes;

    /**
     * @var string
     */
    protected $table = 'internal_notices';

    /**
     * @var array
     */
    protected $dates = ['deleted_at'];

    /**
     * @var array
     */
    public $fillable = [
        'request_id',
        'user_id',
        'user_ids',
        'comment'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'request_id' => 'integer',
        'user_id' => 'integer',
        'comment' => 'string',
        'user_ids' => 'array'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
}
