<?php

namespace App\Models;


/**
 * App\Models\PinboardView
 *
 * @SWG\Definition (
 *      definition="PinboardView",
 *      required={},
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
 *          property="resident_id",
 *          description="resident_id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="views",
 *          description="views",
 *          type="integer",
 *          format="int32"
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
 * @property int $resident_id
 * @property int $views
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Pinboard $pinboard
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PinboardView newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PinboardView newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PinboardView query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PinboardView whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PinboardView whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PinboardView wherePinboardId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PinboardView whereResidentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PinboardView whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PinboardView whereViews($value)
 * @mixin \Eloquent
 */
class PinboardView extends Model
{
    public $table = 'pinboard_view';

    public $fillable = [
        'resident_id',
        'pinboard_id',
        'views'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     **/
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     **/
    public function pinboard()
    {
        return $this->belongsTo(Pinboard::class);
    }
}
