<?php

namespace App\Models;

use App\Traits\HashId;
use App\Traits\RequestRelation;
use App\Traits\UniqueIDFormat;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use OwenIt\Auditing\AuditableObserver;
use PDF;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;
use Illuminate\Support\Facades\Storage;

/**
 * @SWG\Definition(
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
 *          property="default_contract_id",
 *          description="default_contract_id",
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
 *          property="building_id",
 *          description="building_id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="unit_id",
 *          description="unit_id",
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
 *          property="rent_start",
 *          description="rent_start",
 *          type="string",
 *          format="date"
 *      ),
 *      @SWG\Property(
 *          property="rent_end",
 *          description="rent_end",
 *          type="string",
 *          format="date"
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
    const StatusNotActive = 2;
    const Status = [
        self::StatusActive => 'active',
        self::StatusNotActive => 'not_active',
    ];

    const TypeResident = 1;
    const TypeOwner = 2;
    const Type = [
        self::TypeResident => 'resident',
        self::TypeOwner => 'owner',
    ];
    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'default_contract_id' => 'exists:resident_contracts,id',// @TODO check own or not
        'title' => 'required|string',
        'first_name' => 'required|string',
        'last_name' => 'required|string',
        'birth_date' => 'date',
        'rent_start' => 'date',
        'rent_end' => 'date|after_or_equal:rent_start',
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
        'default_contract_id',
        'address_id',
        'building_id',
        'unit_id',
        'title',
        'company',
        'first_name',
        'last_name',
        'birth_date',
        'mobile_phone',
        'private_phone',
        'work_phone',
        'status',
        'rent_start',
        'rent_end',
        'resident_format',
        'review',
        'rating',
        'nation',
        'country_id',
        'type',
    ];

    protected $dates = ['deleted_at', 'rent_start', 'rent_end'];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'user_id' => 'integer',
        'address_id' => 'integer',
        'building_id' => 'integer',
        'country_id' => 'integer',
        'unit_id' => 'integer',
        'title' => 'string',
        'company' => 'string',
        'first_name' => 'string',
        'last_name' => 'string',
        'birth_date' => 'date',
        'rent_start' => 'date',
        'rent_end' => 'date',
        'mobile_phone' => 'string',
        'private_phone' => 'string',
        'work_phone' => 'string',
        'status' => 'integer',
        'resident_format' => 'string',
        'review' => 'string',
        'rating' => 'integer',
        'type' => 'integer',
        'nation' => 'string',
    ];

    const templateMap = [
        'first_name' => 'resident.first_name',
        'last_name' => 'resident.last_name',
        'email' => 'user.email',
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

    /**
     * @return BelongsTo
     **/
    public function unit()
    {
        return $this->belongsTo(Unit::class, 'unit_id', 'id');
    }

    public function registerMediaCollections()
    {
        $this->addMediaCollection('documents');
    }

    /**
     * @return BelongsTo
     **/
    public function building()
    {
        return $this->belongsTo(Building::class, 'building_id', 'id');
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
    public function contracts()
    {
        return $this->hasMany(Contract::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function default_contract()
    {
        return $this->belongsTo(Contract::class);
    }

    public function homeless()
    {
        return ! $this->contracts()
            ->where('resident_contracts.status', Contract::StatusActive)
            ->whereNotNull('resident_contracts.building_id')->count();
    }

    public function active_contracts_with_building()
    {
        return $this->contracts()
            ->where('resident_contracts.status', Contract::StatusActive)
            ->whereNotNull('resident_contracts.building_id');
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
}
