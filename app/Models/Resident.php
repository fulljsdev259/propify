<?php

namespace App\Models;

use App\Traits\HashId;
use App\Traits\RequestRelation;
use App\Traits\UniqueIDFormat;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use OwenIt\Auditing\AuditableObserver;
use PDF;
use phpDocumentor\Reflection\Types\Self_;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use App\Traits\HasMediaTrait;
use Illuminate\Support\Facades\Storage;

/**
 * App\Models\Resident
 *
 * @SWG\Definition (
 *      definition="Resident",
 *      required={"first_name", "birthdate"},
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
 *     @SWG\Property(
 *          property="default_relation_id",
 *          description="default_relation_id",
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
 *          property="title",
 *          description="title",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="company",
 *          description="company",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="first_name",
 *          description="first_name",
 *          type="string"
 *      ),
 *     @SWG\Property(
 *          property="last_name",
 *          description="last_name",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="birth_date",
 *          description="birth_date",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="mobile_phone",
 *          description="mobile_phone",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="private_phone",
 *          description="private_phone",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="work_phone",
 *          description="work_phone",
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
 * @property int|null $user_id
 * @property int|null $default_relation_id
 * @property int|null $address_id
 * @property int|null $country_id
 * @property int|null $rating
 * @property string $resident_format
 * @property string|null $nation
 * @property string|null $review
 * @property string $title
 * @property string|null $company
 * @property string $first_name
 * @property string $last_name
 * @property \Illuminate\Support\Carbon $birth_date
 * @property string|null $mobile_phone
 * @property string|null $private_phone
 * @property string|null $work_phone
 * @property int $status
 * @property string|null $activation_code
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\Address|null $address
 * @property-read \Illuminate\Database\Eloquent\Collection|\OwenIt\Auditing\Models\Audit[] $audits
 * @property-read int|null $audits_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Relation[] $relations
 * @property-read int|null $relations_count
 * @property-read \App\Models\Country|null $country
 * @property-read \App\Models\Relation|null $default_relation
 * @property-read mixed $name
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Media[] $media
 * @property-read int|null $media_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Request[] $requests
 * @property-read int|null $requests_count
 * @property-read \App\Models\UserSettings $settings
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Resident allRequestStatusCount()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Resident newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Resident newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Resident query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Resident whereActivationCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Resident whereAddressId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Resident whereBirthDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Resident whereBuildingId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Resident whereCompany($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Resident whereCountryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Resident whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Resident whereDefaultRelationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Resident whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Resident whereFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Resident whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Resident whereLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Resident whereMobilePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Resident whereNation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Resident wherePrivatePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Resident whereRating($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Resident whereRentEnd($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Resident whereRentStart($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Resident whereResidentFormat($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Resident whereReview($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Resident whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Resident whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Resident whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Resident whereUnitId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Resident whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Resident whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Resident whereWorkPhone($value)
 * @mixin \Eloquent
 */
class Resident extends AuditableModel implements HasMedia
{
    use HasMediaTrait, UniqueIDFormat, HashId, RequestRelation;

    const TitleCompany = 'company';
    const TitleMr = 'mr';
    const TitleMrs = 'mrs';
    const Title = [
        self::TitleMr,
        self::TitleMrs,
        self::TitleCompany
    ];

    const StatusActive = 1;
    const StatusInActive = 2;
    const Status = [
        self::StatusActive => 'active',
        self::StatusInActive => 'not_active',
    ];

    const TypeResident = 1;
    const TypeOwner = 2;
    const TypeFormerResident = 3;
    const Type = [
        self::TypeResident => 'tenant',
        self::TypeOwner => 'owner',
        self::TypeFormerResident => 'former_resident',
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'default_relation_id' => 'exists:relations,id',// @TODO check own or not
        'title' => 'required|string',
        'first_name' => 'required|string',
        'last_name' => 'required|string',
        'birth_date' => 'date',
        'status' => 'digits_between:1,2|numeric'
    ];

    /**
     * @var string
     */
    public $table = 'residents';

    /**
     * @var array
     */
    public $fillable = [
        'user_id',
        'default_relation_id',
        'address_id',
        'title',
        'company',
        'first_name',
        'last_name',
        'birth_date',
        'mobile_phone',
        'private_phone',
        'work_phone',
        'status',
        'resident_format',
        'review',
        'rating',
        'nation',
        'country_id',
    ];

    protected $appends = ['created_by'];

    protected $dates = ['deleted_at'];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'user_id' => 'integer',
        'address_id' => 'integer',
        'country_id' => 'integer',
        'title' => 'string',
        'company' => 'string',
        'first_name' => 'string',
        'last_name' => 'string',
        'birth_date' => 'date',
        'mobile_phone' => 'string',
        'private_phone' => 'string',
        'work_phone' => 'string',
        'status' => 'integer',
        'resident_format' => 'string',
        'review' => 'string',
        'rating' => 'integer',
        'nation' => 'string',
    ];


    const templateMap = [
        'first_name' => 'resident.first_name',
        'last_name' => 'resident.last_name',
        'email' => 'user.email',
    ];

    /**
     * @var array
     */
    protected $permittedExtensions = [
        'pdf',
        'doc',
        'docx',
        'xls',
        'xlsx',
        'png',
        'jpg',
        'jpeg'
    ];

    public static function boot()
    {
        parent::boot();

        static::created(function ($resident) {
            $old = AuditableObserver::$restoring;
            AuditableObserver::$restoring = true;

            $resident->activation_code = $resident->shortHashId($resident->id);
            $resident->save();

            AuditableObserver::$restoring = $old;
        });

        static::deleting(function ($resident) {
            $resident->user->settings()->forceDelete();
            $resident->user()->forceDelete();
        });
    }

    /**
     * @return BelongsTo
     **/
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function settings()
    {
        return $this->hasOne(UserSettings::class, 'user_id', 'user_id');
    }

    /**
     * @return BelongsTo
     **/
    public function address()
    {
        return $this->belongsTo(Address::class, 'address_id', 'id');
    }

    public function registerMediaCollections()
    {
        // @TODO check used or not
        $this->addMediaCollection('documents');
    }

    /**
     * @return BelongsTo
     **/
    public function country()
    {
        return $this->belongsTo(Country::class, 'country_id', 'id');
    }

    /**
     * @return HasMany
     **/
    public function requests()
    {
        return $this->hasMany(Request::class, 'resident_id', 'id');
    }

    /**
     * @return HasMany
     */
    public function relations()
    {
        return $this->hasMany(Relation::class);
    }


    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function garant_relations()
    {
        return $this->belongsToMany(Relation::class, 'relation_garant_resident', 'resident_id', 'relation_id');
    }

    /**
     * @return HasMany
     */
    public function pinboard_views()
    {
        return $this->hasMany(PinboardView::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function default_relation()
    {
        return $this->belongsTo(Relation::class);
    }


    public function active_relations()
    {
        return $this->relations()->where('relations.status', Relation::StatusActive);
    }

    /**
     * @param $resident_id
     * @param $language
     */
    public function setCredentialsPDF()
    {
        $settings = Settings::firstOrFail();
        $data = [
            'resident' => $this,
            'settings' => $settings,
            'url' => url('/activate'),
            'code' => $this->activation_code,
            'language'  => $this->settings->language
        ];

        $pdf = PDF::loadView('pdfs.residentCredentialsXtended', $data);

        Storage::disk('resident_credentials')->put($this->pdfXFileName(), $pdf->output());
        $pdf = PDF::loadView('pdfs.residentCredentials', $data);
        Storage::disk('resident_credentials')->put($this->pdfFilename(), $pdf->output());
    }

    public function pdfXFileName()
    {
        $language  = $this->settings->language;
        return $this->id . '-' . $language . '-X.pdf';
    }

    public function pdfFileName()
    {
        $language  = $this->settings->language;
        return $this->id . '-' . $language . '.pdf';
    }

    public function getNameAttribute()
    {
        return $this->title . " " . $this->first_name . " " . $this->last_name;
    }

    /**
     * @return string
     */
    public function getCreatedByAttribute()
    {
        return $this->created_at ? $this->created_at->format('d.m.Y') : '';
    }

    /**
     * @TODO find more clever way
     *
     * @param array $data
     * @return array
     */
    public function transformAudit(array $data): array
    {
        $oldValues = $data['old_values'];
        $newValues = $data['new_values'];

        if (
            key_exists('nation', $oldValues) && key_exists('nation', $newValues)
            && empty($oldValues['nation']) && empty($newValues['nation'])
        ) {
            unset($oldValues['nation'], $newValues['nation']);
        }

        $data['old_values'] = $oldValues;
        $data['new_values'] = $newValues;

        return parent::transformAudit($data); // TODO: Change the autogenerated stub
    }
}
