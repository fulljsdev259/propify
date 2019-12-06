<?php

namespace App\Models;

use App\Traits\BuildingRelation;
use App\Traits\RequestRelation;
use App\Traits\UniqueIDFormat;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\PropertyManager
 *
 * @SWG\Definition (
 *      definition="PropertyManager",
 *      required={""},
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
 *          property="type",
 *          description="type",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="mobile_phone",
 *          description="mobile_phone",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="title",
 *          description="title",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="first_name",
 *          description="first_name",
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
 * @property int $user_id
 * @property int $type
 * @property string $property_manager_format
 * @property string|null $description
 * @property string $mobile_phone
 * @property string $title
 * @property string $status
 * @property string $first_name
 * @property string $last_name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\OwenIt\Auditing\Models\Audit[] $audits
 * @property-read int|null $audits_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Building[] $buildings
 * @property-read int|null $buildings_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Quarter[] $quarters
 * @property-read int|null $quarters_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Request[] $requests
 * @property-read int|null $requests_count
 * @property-read \App\Models\UserSettings $settings
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PropertyManager allRequestStatusCount()
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PropertyManager newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PropertyManager newQuery()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\PropertyManager onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PropertyManager query()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PropertyManager whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PropertyManager whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PropertyManager whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PropertyManager whereFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PropertyManager whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PropertyManager whereLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PropertyManager wherePropertyManagerFormat($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PropertyManager whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PropertyManager whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PropertyManager whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PropertyManager whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\PropertyManager withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\PropertyManager withoutTrashed()
 * @mixin \Eloquent
 */
class PropertyManager extends AuditableModel
{
    use SoftDeletes, UniqueIDFormat, RequestRelation, BuildingRelation;

    public $table = 'property_managers';

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        //'description' => 'sometimes|string',
        'first_name' => 'required|string',
        'last_name' => 'required|string',
    ];

    public static $rulesUpdate = [
        //'description' => 'sometimes|string',
        'first_name' => 'required|string',
        'last_name' => 'required|string',
    ];

    const Title = [
        'mr',
        'mrs',
    ];

    const TypeManager = 1;
    const TypeAdministrator = 2;
    const TypeInitialLetting = 3;
    const TypeMarketing = 4;
    const TypeSiteSupervision = 5;

    const Type = [
        self::TypeManager => 'manager',
        self::TypeAdministrator => 'administrator',
        self::TypeInitialLetting => 'initial_letting',
        self::TypeMarketing => 'marketing',
        self::TypeSiteSupervision => 'site_supervision',
    ];

    const StatusActive = 1;
    const StatusInactive = 2;
	
	const Status = [
		self::StatusActive => 'active',
		self::StatusInactive => 'inactive',
	];

	const StatusColorCode = [
		self::StatusActive => '#878810',
		self::StatusInactive => '#c8a331',
	];


    public $fillable = [
        'description',
        'user_id',
        'type',
        'mobile_phone',
        'title',
        'status',
        'first_name',
        'last_name',
        'property_manager_format',
    ];

    protected $dates = ['deleted_at'];

    /**
     * @var array
     */
    protected $auditEvents = [
        AuditableModel::EventCreated,
        AuditableModel::EventUpdated,
        AuditableModel::EventDeleted,
        AuditableModel::EventQuarterAssigned => 'getAttachedEventAttributes',
        AuditableModel::EventBuildingAssigned => 'getAttachedEventAttributes',
        AuditableModel::EventQuarterUnassigned => 'getDetachedEventAttributes',
        AuditableModel::EventBuildingUnassigned => 'getDetachedEventAttributes',
    ];

    protected $auditExclude = [
        'type',
    ];


    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'description' => 'string',
        'user_id' => 'integer',
        'type' => 'integer',
        'mobile_phone' => 'string',
        'title' => 'string',
        'status' => 'integer',
        'first_name' => 'string',
        'last_name' => 'string',
        'property_manager_format' => 'string',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     **/
    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    /**
     * @return mixed
     */
    public function settings()
    {
        return $this->hasOne(UserSettings::class, 'user_id', 'user_id');
    }

    /**
     * @return mixed
     */
    public function quarters()
    {
        return $this->morphToMany(Quarter::class, 'assignee', 'quarter_assignees', 'assignee_id', 'quarter_id');
    }

    /**
     * @return string
     */
    public function getNameAttribute()
    {
        if (! empty( $this->attributes['name'])) {
            return $this->attributes['name'];
        }
        return $this->attributes['first_name'] . ' ' . $this->attributes['last_name'];
    }
}
