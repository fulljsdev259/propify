<?php

namespace App\Models;


/**
 * @SWG\Definition(
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
 */
class AnnouncementEmailReceptionist extends Model
{
    public $fillable = [
        'pinboard_id',
        'resident_ids',
        'failed_resident_ids'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'pinboard_id' => 'integer',
        'resident_ids' => 'array',
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
