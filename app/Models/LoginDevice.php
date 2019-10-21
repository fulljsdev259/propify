<?php

namespace App\Models;

/**
 * App\Models\LoginDevice
 *
 * @property int $id
 * @property int $user_id
 * @property int|null $resident_id
 * @property int $mobile
 * @property int $desktop
 * @property int $tablet
 * @property string $created_by
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\LoginDevice newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\LoginDevice newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\LoginDevice query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\LoginDevice whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\LoginDevice whereDesktop($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\LoginDevice whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\LoginDevice whereMobile($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\LoginDevice whereResidentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\LoginDevice whereTablet($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\LoginDevice whereUserId($value)
 * @mixin \Eloquent
 */
class LoginDevice extends Model
{
    public $timestamps = false;

    public $fillable = [
        'user_id',
        'resident_id',
        'mobile',
        'desktop',
        'tablet',
        'created_by',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     **/
    public function user()
    {
        return $this->belongsTo(User::class, 'id', 'user_id');
    }
}
