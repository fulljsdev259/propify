<?php

namespace App\Models;

use App\Traits\BuildingRelation;
use App\Traits\RequestRelation;
use App\Traits\UniqueIDFormat;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

/**
 * App\Models\ServiceProvider
 *
 * @SWG\Definition (
 *      definition="ServiceProvider",
 *      required={"name"},
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
 *          property="address_id",
 *          description="address_id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="category",
 *          description="category",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="type",
 *          description="type",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="status",
 *          description="status",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="title",
 *          description="title",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="fisrt_name",
 *          description="fisrt_name",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="last_name",
 *          description="last_name",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="company_name",
 *          description="company_name",
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
 * @property int $address_id
 * @property string $service_provider_format
 * @property string $category
 * @property string $name
 * @property string $email
 * @property string $phone
 * @property string $mobile_phone
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\Address $address
 * @property-read \Illuminate\Database\Eloquent\Collection|\OwenIt\Auditing\Models\Audit[] $audits
 * @property-read int|null $audits_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Building[] $buildings
 * @property-read int|null $buildings_count
 * @property-read array|string|null $category_name_de
 * @property-read array|string|null $category_name_en
 * @property-read array|string|null $category_name_fr
 * @property-read array|string|null $category_name_it
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Quarter[] $quarters
 * @property-read int|null $quarters_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Request[] $requests
 * @property-read int|null $requests_count
 * @property-read \App\Models\UserSettings $settings
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ServiceProvider allRequestStatusCount()
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ServiceProvider newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ServiceProvider newQuery()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ServiceProvider onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ServiceProvider query()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ServiceProvider whereAddressId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ServiceProvider whereCategory($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ServiceProvider whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ServiceProvider whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ServiceProvider whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ServiceProvider whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ServiceProvider whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ServiceProvider wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ServiceProvider whereServiceProviderFormat($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ServiceProvider whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ServiceProvider whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ServiceProvider withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ServiceProvider withoutTrashed()
 * @mixin \Eloquent
 */
class ServiceProvider extends AuditableModel
{
    use Notifiable;
    use SoftDeletes;
    use UniqueIDFormat;
    use RequestRelation;
    use BuildingRelation;

    public $table = 'service_providers';

    const TitleMr = 'mr';
    const TitleMrs = 'mrs';
    const Title = [
        self::TitleMr,
        self::TitleMrs,
    ];

    const StatusActive = 1;
    const StatusInActive = 2;
    const Status = [
        self::StatusActive => 'active',
        self::StatusInActive => 'inactive',
    ];

    const CategoryElectrician = 1;
    const CategoryHeatingCompany = 2;
    const CategoryLift = 3;
    const CategorySanitary = 4;
    const CategoryKeyService = 5;
    const CategoryCaretaker = 6;
    const CategoryRealEstateService = 7;
    const CategoryTuGu = 8;
    const CategoryArchitect = 9;
    const ServiceProviderExternalRealEstateCompany = 10;

    const Category = [
        self::CategoryElectrician => 'electrician',
        self::CategoryHeatingCompany => 'heating_company',
        self::CategoryLift => 'lift',
        self::CategorySanitary => 'sanitary',
        self::CategoryKeyService => 'key_service',
        self::CategoryCaretaker => 'caretaker',
        self::CategoryRealEstateService => 'real_estate_service',
        self::CategoryTuGu => 'tu-gu',
        self::CategoryArchitect => 'architect',
        self::ServiceProviderExternalRealEstateCompany => 'external_real_estate_company'
    ];

    const TypeTuGu = 1;
    const TypeBusinessPerson = 2;
    const Type = [
        self::TypeTuGu => 'tu-gu',
        self::TypeBusinessPerson => 'business_person',
    ];

    public $fillable = [
        'user_id',
        'address_id',
        'category',
        'title',
        'first_name',
        'last_name',
        'company_name',
        'email',
        'phone',
        'type',
        'status',
        'mobile_phone',
    ];
    protected $dates = ['deleted_at'];
    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'user_id' => 'integer',
        'address_id' => 'integer',
        'category' => 'string',
        'type' => 'integer',
        'status' => 'integer',
        'title' => 'string',
        'first_name' => 'string',
        'last_name' => 'string',
        'company_name' => 'string',
        'email' => 'string',
        'phone' => 'string',
        'service_provider_format' => 'string',
        'mobile_phone'=> 'string'
    ];

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

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function settings()
    {
        return $this->hasOne(UserSettings::class, 'user_id', 'user_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     **/
    public function address()
    {
        return $this->hasOne(Address::class, 'id', 'address_id');
    }

    /**
     * @return mixed
     */
    public function quarters()
    {
        return $this->morphToMany(Quarter::class, 'assignee', 'quarter_assignees', 'assignee_id', 'quarter_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     **/
    public function pinboards()
    {
        return $this->belongsToMany(Pinboard::class, 'pinboard_service_provider', 'service_provider_id', 'pinboard_id');
    }

    // @TODO remove
    public function setCategoryAttribute($value)
    {
        $this->attributes['category'] = array_flip(ServiceProvider::Category)[$value] ?? $value;
    }

    /**
     * @return array|string|null
     */
    public function getCategoryNameEnAttribute()
    {
        return $this->categoryTranslation('en');
    }

    /**
     * @return array|string|null
     */
    public function getCategoryNameDeAttribute()
    {
        return $this->categoryTranslation('de');
    }

    /**
     * @return array|string|null
     */
    public function getCategoryNameFrAttribute()
    {
        return $this->categoryTranslation('fr');
    }

    /**
     * @return array|string|null
     */
    public function getCategoryNameItAttribute()
    {
        return $this->categoryTranslation('it');
    }

    protected function categoryTranslation($lang)
    {
        if (!empty(ServiceProvider::Category[$this->attributes['category']])) {
            return __('models.service.category.' . ServiceProvider::Category[$this->attributes['category']], [], $lang);
        }
        
        return '';
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
