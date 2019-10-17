<?php

namespace App\Models;

use App\Traits\AddressOwnerAudit;
use App\Traits\RequestRelation;
use App\Traits\UniqueIDFormat;
use Chelout\RelationshipEvents\Concerns\HasBelongsToManyEvents;
use Chelout\RelationshipEvents\Concerns\HasMorphedByManyEvents;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;

/**
 * @SWG\Definition(
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
 */
class Building extends AuditableModel implements HasMedia
{
    use SoftDeletes,
        HasMediaTrait,
        UniqueIDFormat,
        AddressOwnerAudit,
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
        'internal_building_id'
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
        'floor_nr' => 'required'
    ];

    protected $auditEvents = [
        AuditableModel::EventCreated => 'getCreatedEventAttributesIncludeAddress',
        AuditableModel::EventUpdated => 'getUpdatedEventAttributesIncludeAddress',
        AuditableModel::EventDeleted,
        AuditableModel::EventUserAssigned => 'getAttachedEventAttributes',
        AuditableModel::EventManagerAssigned => 'getAttachedEventAttributes',
        AuditableModel::EventUserUnassigned => 'getDetachedEventAttributes',
        AuditableModel::EventManagerUnassigned => 'getDetachedEventAttributes',
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
    public function serviceProviders()
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
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     **/
    public function residents()
    {
        return $this->hasMany(Resident::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function activeResidents()
    {
        return $this->residents()
            ->where('residents.status', Resident::StatusActive);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function inActiveResidents()
    {
        return $this->residents()
            ->where('residents.status', Resident::StatusNotActive);
    }


    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     **/
    public function lastResidents()
    {
        return $this->hasMany(Resident::class)->orderBy('id', 'DESC');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function requests()
    {
        return $this->hasManyThrough(Request::class, Resident::class);
    }

    public function registerMediaCollections()
    {
        foreach (\ConstFileCategories::MediaCategories as $category)  {
            $this->addMediaCollection($category);
        }
    }
}
