<?php

namespace App\Models;


/**
 * App\Models\AnnouncementEmailReceptionist
 *
 * @SWG\Definition (
 *      definition="AnnouncementEmailReceptionist",
 *      required={"content"},
 *      @SWG\Property(
 *          property="id",
 *          description="id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="pinboard_id",
 *          description="pinboard_id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="resident_ids",
 *          description="resident_ids",
 *          type="array",
 *          @SWG\Items(
 *              type="integer"
 *          )
 *      ),
 *      @SWG\Property(
 *          property="failed_resident_ids",
 *          description="failed_resident_ids",
 *          type="array",
 *          @SWG\Items(
 *              type="integer"
 *          )
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
 * @property int $pinboard_id
 * @property array|null $resident_ids
 * @property array|null $failed_resident_ids
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Pinboard $pinboard
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AnnouncementEmailReceptionist newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AnnouncementEmailReceptionist newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AnnouncementEmailReceptionist query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AnnouncementEmailReceptionist whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AnnouncementEmailReceptionist whereFailedResidentIds($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AnnouncementEmailReceptionist whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AnnouncementEmailReceptionist wherePinboardId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AnnouncementEmailReceptionist whereResidentIds($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AnnouncementEmailReceptionist whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class AnnouncementEmailReceptionist extends Model
{
    public $fillable = [
        'pinboard_id',
        'residents_data',
        'failed_resident_ids'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'pinboard_id' => 'integer',
        'residents_data' => 'array',
        'failed_resident_ids' => 'array'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     **/
    public function pinboard()
    {
        return $this->belongsTo(Pinboard::class, 'pinboard_id', 'id');
    }
}
