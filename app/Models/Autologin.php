<?php

namespace App\Models;

/**
 * App\Models\Autologin
 *
 * @SWG\Definition (
 *      definition="Autologin",
 *      required={},
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
 *          property="used",
 *          description="used",
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
 * @property string $token
 * @property string $redirect
 * @property int $used
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $url
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Autologin newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Autologin newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Autologin query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Autologin whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Autologin whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Autologin whereRedirect($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Autologin whereToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Autologin whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Autologin whereUsed($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Autologin whereUserId($value)
 * @mixin \Eloquent
 */
class Autologin extends Model
{
    public $table = 'autologins';
    protected $dates = [];
    const Fillable = [];
    public $fillable = self::Fillable;

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [];

    /**
     * Validation rules
     *
     * @var array
     */
    public static function rules()
    {
        return [];
    }
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     **/
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getUrlAttribute()
    {
        return config('app.url') . config('autologin.link') . '?token=' . $this->token;
    }
}
