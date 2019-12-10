<?php

namespace App\Models;

use App\Traits\HasCategoryMediaTrait;
use App\Traits\RelationRelation;
use App\Traits\RequestRelation;
use App\Traits\UniqueIDFormat;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia\HasMedia;

/**
 * App\Models\Quarter
 *
 * @SWG\Definition (
 *      definition="Quarter",
 *      required={"name", "description"},
 *      @SWG\Property(
 *          property="id",
 *          description="id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="address_id",
 *          description="address_id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="internal_quarter_id",
 *          description="internal_quarter_id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="name",
 *          description="name",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="count_of_buildings",
 *          description="count_of_buildings",
 *          type="integer"
 *      ),
 *      @SWG\Property(
 *          property="type",
 *          description="type",
 *          type="integer"
 *      ),
 *      @SWG\Property(
 *          property="description",
 *          description="description",
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
 * @property int|null $address_id
 * @property string|null $internal_quarter_id
 * @property int|null $count_of_buildings
 * @property string $quarter_format
 * @property string $name
 * @property string|null $description
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\Address $address
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\QuarterAssignee[] $assignees
 * @property-read int|null $assignees_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\OwenIt\Auditing\Models\Audit[] $audits
 * @property-read int|null $audits_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Building[] $buildings
 * @property-read int|null $buildings_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Media[] $media
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Relation[] relations
 * @property-read int|null $media_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\PropertyManager[] $propertyManagers
 * @property-read int|null $property_managers_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\User[] $users
 * @property-read int|null $users_count
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Quarter newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Quarter newQuery()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Quarter onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Quarter query()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Quarter whereAddressId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Quarter whereCountOfBuildings($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Quarter whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Quarter whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Quarter whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Quarter whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Quarter whereInternalQuarterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Quarter whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Quarter whereQuarterFormat($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Quarter whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Quarter withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Quarter withoutTrashed()
 * @mixin \Eloquent
 */
class Quarter extends AuditableModel implements HasMedia
{
    use SoftDeletes, UniqueIDFormat, HasCategoryMediaTrait, RelationRelation, RequestRelation;

    const TypeRent = 1;
    const TypeSell = 2;
    const TypeBuildingLand = 3;
    const TypeTrade = 4;
    const TypeMultiStoreyCarPark = 5;

    const Type = [
        self::TypeRent => 'rent',
        self::TypeSell => 'sell',
        self::TypeBuildingLand => 'building_land',
        self::TypeTrade => 'trade',
        self::TypeMultiStoreyCarPark => 'multi-storey_car_park',
    ];

    const AssignmentTypeFortimoEmployees = 1;
    const AssignmentTypeManagement = 2;
    const AssignmentTypeCaretaker = 3;
    const AssignmentTypeAdministration = 4;

    // @TODO delete
    const AssignmentType = [
        self::AssignmentTypeFortimoEmployees => 'fortimo_employees',
        self::AssignmentTypeManagement => 'management',
        self::AssignmentTypeCaretaker => 'caretaker',
        self::AssignmentTypeAdministration => 'administration',
    ];

    /**
     * @var string
     */
    public $table = 'quarters';

    /**
     * @var array
     */
    public $fillable = [
        'name',
        'description',
        'quarter_format',
        'count_of_buildings',
        'address_id',
        'internal_quarter_id',
        'types',
        'url',
    ];
    /**
     * @var array
     */
    protected $dates = ['deleted_at'];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'address_id' => 'integer',
        'name' => 'string',
        'description' => 'string',
        'quarter_format' => 'string',
        'internal_quarter_id' => 'string',
        'count_of_buildings' => 'integer',
        'types' => 'array',
        'url' => 'string',
    ];

    protected $permittedExtensions = [
        'pdf',
        'doc',
        'docx',
        'xls',
        'xlsx'
    ];
    /**
     * @var array
     */
    protected $auditEvents = [
        AuditableModel::EventCreated,
        AuditableModel::EventUpdated,
        AuditableModel::EventDeleted,
        AuditableModel::EventManagerAssigned => 'getAttachedEventAttributes',
        AuditableModel::EventManagerUnassigned => 'getDetachedEventAttributes',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function propertyManagers()
    {
        return $this->belongsToMany(PropertyManager::class, 'quarter_assignees', 'quarter_id', 'user_id', 'id', 'user_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function property_managers()
    {
        return $this->belongsToMany(PropertyManager::class, 'quarter_assignees', 'quarter_id', 'user_id', 'id', 'user_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'quarter_assignees', 'quarter_id', 'user_id', 'id', 'user_id');
    }


    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function assignees()
    {
        return $this->hasMany(QuarterAssignee::class, 'quarter_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function units()
    {
        return $this->hasMany(Unit::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function email_receptionists()
    {
        return $this->hasMany(EmailReceptionist::class, 'model_id')->where('model_type', get_morph_type_of($this));
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     **/
    public function address()
    {
        return $this->hasOne(Address::class, 'id', 'address_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function buildings()
    {
        return $this->hasMany(Building::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function workflows()
    {
        return $this->hasMany(Workflow::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function requests()
    {
        return $this->hasManyThrough(Request::class, Relation::class);
    }
}
