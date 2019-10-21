<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\UserSettings
 *
 * @SWG\Definition (
 *      definition="UserSettings",
 *      required={"language", "summary", "pinboard_notification", "listing_notification", "service_notification"},
 *      @SWG\Property(
 *          property="id",
 *          description="id",
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
 *          property="language",
 *          description="language",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="summary",
 *          description="summary",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="admin_notification",
 *          description="admin_notification",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="pinboard_notification",
 *          description="pinboard_notification",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="listing_notification",
 *          description="listing_notification",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="service_notification",
 *          description="service_notification",
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
 * @property int $user_id
 * @property string $language
 * @property string $summary
 * @property bool $admin_notification
 * @property bool $pinboard_notification
 * @property bool $listing_notification
 * @property bool $service_notification
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\Resident $resident
 * @property-read \App\Models\User $user
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserSettings newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserSettings newQuery()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\UserSettings onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserSettings query()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserSettings whereAdminNotification($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserSettings whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserSettings whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserSettings whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserSettings whereLanguage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserSettings whereListingNotification($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserSettings wherePinboardNotification($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserSettings whereServiceNotification($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserSettings whereSummary($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserSettings whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserSettings whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\UserSettings withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\UserSettings withoutTrashed()
 * @mixin \Eloquent
 */
class UserSettings extends Model
{
    use SoftDeletes;

    public $table = 'user_settings';


    protected $dates = ['deleted_at'];


    public $fillable = [
        'user_id',
        'language',
        'summary',
        'admin_notification',
        'pinboard_notification',
        'listing_notification',
        'service_notification'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'user_id' => 'integer',
        'language' => 'string',
        'summary' => 'string',
        'admin_notification' => 'boolean',
        'pinboard_notification' => 'boolean',
        'listing_notification' => 'boolean',
        'service_notification' => 'boolean'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'language' => 'required',
        'summary' => 'required',
        'pinboard_notification' => 'required',
        'listing_notification' => 'required',
        'service_notification' => 'required'
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
    public function resident()
    {
        return $this->belongsTo(Resident::class, 'user_id', 'user_id');
    }
}
