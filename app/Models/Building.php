<?php

namespace App\Models;

use App\Traits\HasCategoryMediaTrait;
use App\Traits\RequestRelation;
use App\Traits\UniqueIDFormat;
use Chelout\RelationshipEvents\Concerns\HasBelongsToManyEvents;
use Chelout\RelationshipEvents\Concerns\HasMorphedByManyEvents;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia\HasMedia;

/**
 * App\Models\Building
 *
 * @SWG\Definition (
 *      definition="Building",
 *      required={"name", "floor_nr"},
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
 *          property="description",
 *          description="description",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="label",
 *          description="label",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="address_id",
 *          description="address_id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="floor_nr",
 *          description="floor_nr",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="under_floor",
 *          description="under_floor",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="basement",
 *          description="basement",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="attic",
 *          description="attic",
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
 * @property int|null $quarter_id
 * @property int $address_id
 * @property string|null $internal_building_id
 * @property string $building_format
 * @property float|null $latitude
 * @property float|null $longitude
 * @property int $contact_enable
 * @property string $name
 * @property string|null $description
 * @property string|null $label
 * @property int $floor_nr
 * @property int $under_floor
 * @property bool $basement
 * @property bool $attic
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Resident[] $activeResidents
 * @property-read int|null $active_residents_count
 * @property-read \App\Models\Address $address
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\BuildingAssignee[] $assignees
 * @property-read int|null $assignees_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\OwenIt\Auditing\Models\Audit[] $audits
 * @property-read int|null $audits_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Contract[] $contracts
 * @property-read int|null $contracts_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Resident[] $inActiveResidents
 * @property-read int|null $in_active_residents_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\PropertyManager[] $lastPropertyManagers
 * @property-read int|null $last_property_managers_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Resident[] $lastResidents
 * @property-read int|null $last_residents_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Media[] $media
 * @property-read int|null $media_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\PropertyManager[] $propertyManagers
 * @property-read int|null $property_managers_count
 * @property-read \App\Models\Quarter|null $quarter
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Request[] $requests
 * @property-read int|null $requests_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Resident[] $residents
 * @property-read int|null $residents_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\ServiceProvider[] $service_providers
 * @property-read int|null $service_providers_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Unit[] $units
 * @property-read int|null $units_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\User[] $users
 * @property-read int|null $users_count
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Building allRequestStatusCount()
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Building newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Building newQuery()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Building onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Building query()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Building whereAddressId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Building whereAttic($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Building whereBasement($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Building whereBuildingFormat($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Building whereContactEnable($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Building whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Building whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Building whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Building whereFloorNr($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Building whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Building whereInternalBuildingId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Building whereLabel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Building whereLatitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Building whereLongitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Building whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Building whereQuarterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Building whereUnderFloor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Building whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Building withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Building withoutTrashed()
 * @mixin \Eloquent
 */
class Building extends AuditableModel implements HasMedia
{
    use SoftDeletes,
        HasCategoryMediaTrait,
        UniqueIDFormat,
        HasBelongsToManyEvents,
        HasMorphedByManyEvents,
        RequestRelation;

    const ContactEnablesBasedSettings = 1;
    const ContactEnablesShow = 2;
    const ContactEnablesHide = 3;

    const BuildingContactEnables = [
        self::ContactEnablesBasedSettings => 'reference_settings',
        self::ContactEnablesShow => 'show',
        self::ContactEnablesHide => 'hide',
    ];

    public $table = 'buildings';

    protected $dates = ['deleted_at'];

    public $fillable = [
        'name',
        'description',
        'label',
        'address_id',
        'quarter_id',
        'floor_nr',
        'basement',
        'attic',
        'building_format',
        'longitude',
        'latitude',
        'contact_enable',
        'internal_building_id',
        'under_floor'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'name' => 'string',
        'description' => 'string',
        'label' => 'string',
        'address_id' => 'integer',
        'under_floor' => 'integer',
        'quarter_id' => 'integer',
        'floor_nr' => 'integer',
        'contact_enable' => 'integer',
        'basement' => 'boolean',
        'attic' => 'boolean',
        'building_format' => 'string',
        'internal_building_id' => 'string',
    ];

    protected $permittedExtensions = [
        'pdf',
        'doc',
        'docx',
        'xls',
        'xlsx'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'name' => 'required',
        'floor_nr' => 'required',
        'under_floor' => 'numeric|between:0,3'
    ];

    protected $auditEvents = [
        AuditableModel::EventCreated,
        AuditableModel::EventUpdated,
        AuditableModel::EventDeleted,
        AuditableModel::EventUserAssigned => 'getAttachedEventAttributes',
        AuditableModel::EventManagerAssigned => 'getAttachedEventAttributes',
        AuditableModel::EventProviderAssigned => 'getAttachedEventAttributes',
        AuditableModel::EventUserUnassigned => 'getDetachedEventAttributes',
        AuditableModel::EventManagerUnassigned => 'getDetachedEventAttributes',
        AuditableModel::EventProviderUnassigned => 'getDetachedEventAttributes',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     **/
    public function address()
    {
        return $this->hasOne(Address::class, 'id', 'address_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function quarter()
    {
        return $this->belongsTo(Quarter::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function units()
    {
        return $this->hasMany(Unit::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     **/
    public function service_providers()
    {
        return $this->belongsToMany(ServiceProvider::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public function propertyManagers()
    {
        return $this->morphedByMany(PropertyManager::class, 'assignee', 'building_assignees', 'building_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public function users()
    {
        return $this->morphedByMany(User::class, 'assignee', 'building_assignees', 'building_id');
    }

    public function assignees()
    {
        return $this->hasMany(BuildingAssignee::class, 'building_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public function lastPropertyManagers()
    {
        return $this->propertyManagers()->orderBy('id', 'DESC');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function requests()
    {
        return $this->hasManyThrough(Request::class, Contract::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function contracts()
    {
        return $this->hasMany(Contract::class);
    }
}
