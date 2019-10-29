<?php

namespace App\Models;

use App\Helpers\Helper;
use App\Traits\BaseModelTrait;
use App\Traits\BuildingRelation;
use App\Traits\OldChagesAttribute;
use App\Traits\RequestRelation;
use BeyondCode\Comments\Contracts\Commentator;
use Cog\Contracts\Love\Liker\Models\Liker as LikerContract;
use Cog\Laravel\Love\Liker\Models\Traits\Liker;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use OwenIt\Auditing\Contracts\Auditable;
use Zizaco\Entrust\Traits\EntrustUserTrait;

/**
 * App\Models\User
 *
 * @SWG\Definition (
 *      definition="User",
 *      required={"name"},
 *      @SWG\Property(
 *          property="id",
 *          description="id",
 *          type="integer",
 *          format="int32"
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
 *      @SWG\Property(
 *          property="phone",
 *          description="phone",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="avatar",
 *          description="avatar",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="email_verified_at",
 *          description="email_verified_at",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="password",
 *          description="password",
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
 * @property string $title
 * @property string $name
 * @property string|null $phone
 * @property string|null $avatar
 * @property string $email
 * @property string|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property string|null $last_login_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\OwenIt\Auditing\Models\Audit[] $audits
 * @property-read int|null $audits_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Autologin[] $autologins
 * @property-read int|null $autologins_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Building[] $buildings
 * @property-read int|null $buildings_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\Laravel\Passport\Client[] $clients
 * @property-read int|null $clients_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Conversation[] $conversations
 * @property-read int|null $conversations_count
 * @property-read mixed $autologin_url
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-read int|null $notifications_count
 * @property-read \App\Models\PropertyManager $propertyManager
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Request[] $requests
 * @property-read int|null $requests_count
 * @property-read \App\Models\Resident $resident
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Role[] $roles
 * @property-read int|null $roles_count
 * @property-read \App\Models\ServiceProvider $serviceProvider
 * @property-read \App\Models\UserSettings $settings
 * @property-read \Illuminate\Database\Eloquent\Collection|\Laravel\Passport\Token[] $tokens
 * @property-read int|null $tokens_count
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User allRequestStatusCount()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereAvatar($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereLastLoginAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User withRole($role)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User withRoles($roles)
 * @mixin \Eloquent
 */
class User extends Authenticatable implements LikerContract, Commentator, Auditable
{
    use EntrustUserTrait,
        HasApiTokens,
        Notifiable,
        Liker,
        BuildingRelation,
        RequestRelation,
        \App\Traits\Auditable,
        OldChagesAttribute,
        BaseModelTrait;

    const Title = [
        'mr',
        'mrs',
        'company',
    ];

    public $table = 'users';

    protected $dates = ['deleted_at'];

    public $fillable = [
        'name',
        'email',
        'phone',
        'avatar',
        'email_verified_at',
        'last_login_at',
        'password'
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'name' => 'string',
        'email' => 'string',
        'phone' => 'string',
        'password' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users',
        'password' => 'required|string|min:6|confirmed',
    ];

    /**
     * Validation rulesUpdate
     *
     * @var array
     */
    public static $rulesUpdate = [
        'name' => 'required|string|max:255',
        //'email' => 'required|string|email|max:255',
        'image_upload' => 'sometimes|string',
        'password_old' => 'sometimes|string',
        'password' => 'sometimes|required|string|min:6',
        'password_confirmation' => 'sometimes|required_with:password|same:password',
    ];

    /**
     * Validation rulesChangePassword
     *
     * @var array
     */
    public static $rulesChangePassword = [
        'password_old' => 'required|string',
        'password' => 'required|string|min:6|confirmed',
    ];

    /**
     * Validation rulesChangePassword
     *
     * @var array
     */
    public static $rulesUpload = [
        'image_upload' => 'required',
    ];

    public static function boot()
    {
        parent::boot();

        static::deleting(function($user) {
            $user->settings()->forceDelete();
        });
    }

    public function restore() {
        $this->sfRestore();
        Cache::tags(Config::get('entrust.role_user_table'))->flush();
    }

    /**
     * Check if a comment for a specific model needs to be approved.
     * @param mixed $model
     * @return bool
     */
    public function needsCommentApproval($model): bool
    {
        return false;
    }

    /**
    * @return \Illuminate\Database\Eloquent\Relations\HasOne
    **/
    public function settings()
    {
        return $this->hasOne(UserSettings::class);
    }

    /**
    * @return \Illuminate\Database\Eloquent\Relations\HasOne
    **/
    public function resident()
    {
        return $this->hasOne(Resident::class);
    }

    /**
    * @return \Illuminate\Database\Eloquent\Relations\HasOne
    **/
    public function propertyManager()
    {
        return $this->hasOne(PropertyManager::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     **/
    public function serviceProvider()
    {
        return $this->hasOne(ServiceProvider::class);
    }

    /**
    * @return \Illuminate\Database\Eloquent\Relations\MorphMany
    **/
    public function notifications()
    {
        return $this->morphMany(\Illuminate\Notifications\DatabaseNotification::class, 'notifiable')
                    ->orderByRaw('-read_at','DESC')
                    ->orderBy('created_at','DESC');
    }

    public function scopeWithRoles($query, array $roles)
    {
        return $query->whereHas('roles', function ($query) use ($roles) {
            $query->whereIn('name', $roles);
        });
    }

    public function conversations()
    {
        return $this->belongsToMany(Conversation::class);
    }

    public function autologins()
    {
        return $this->hasMany(Autologin::class);
    }

    public function getAutologinUrlAttribute()
    {
        $a = new Autologin();

        $a->redirect = $this->redirect ?? "/dashboard";
        $a->token = Helper::randStr(100);
        $a->user_id = $this->id;
        $a->save();

        return $a->url;
    }
}
