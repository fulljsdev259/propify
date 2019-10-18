<?php

namespace App\Models;

use App\Traits\UniqueIDFormat;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use App\Traits\HasMediaTrait;

/**
 * @SWG\Definition(
 *      definition="Contract",
 *      required={"first_name", "birthdate"},
 *      @SWG\Property(
 *          property="id",
 *          description="id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="resident_id",
 *          description="resident_id",
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
 *          type="integer"
 *      ),
 *     @SWG\Property(
 *          property="duration",
 *          description="duration",
 *          type="integer"
 *      ),
 *     @SWG\Property(
 *          property="status",
 *          description="status",
 *          type="integer"
 *      ),
 *     @SWG\Property(
 *          property="contract_format",
 *          description="contract_format",
 *          type="integer"
 *      ),
 *     @SWG\Property(
 *          property="deposit_type",
 *          description="deposit_type",
 *          type="integer"
 *      ),
 *     @SWG\Property(
 *          property="deposit_status",
 *          description="deposit_status",
 *          type="integer"
 *      ),
 *     @SWG\Property(
 *          property="deposit_amount",
 *          description="deposit_amount",
 *          type="integer"
 *      ),
 *      @SWG\Property(
 *          property="monthly_rent_net",
 *          description="monthly_rent_net",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="monthly_rent_gross",
 *          description="monthly_rent_gross",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="monthly_maintenance",
 *          description="monthly_maintenance",
 *          type="integer",
 *          format="int32"
 *      ),
 *     @SWG\Property(
 *          property="start_date",
 *          description="start_date",
 *          type="string",
 *          format="date"
 *      ),
 *      @SWG\Property(
 *          property="end_date",
 *          description="end_date",
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
class Contract extends AuditableModel implements HasMedia
{
    use HasMediaTrait, UniqueIDFormat;

    const TypePrivate = 1;
    const TypeBusiness = 2;
    const TypeParkingSlot = 3;
    const Type = [
        self::TypePrivate => 'private',
        self::TypeBusiness => 'business',
        self::TypeParkingSlot => 'parking_slot'
    ];

    const DurationUnlimited = 1;
    const DurationLimited = 2;
    const Duration = [
        self::DurationUnlimited => 'unlimited',
        self::DurationLimited => 'limited',
    ];
    
    const StatusActive = 1;
    const StatusInactive = 2;
    const Status = [
        self::StatusActive => 'active',
        self::StatusInactive => 'inactive',
    ];

    const DepositTypeBankDepositt = 1;
    const DepositTypeBankGuarantee = 2;
    const DepositTypeInsurance = 3;
    const DepositTypeOther = 4;
    const DepositType = [
        self::DepositTypeBankDepositt => 'bank_deposit',
        self::DepositTypeBankGuarantee => 'bank_guarantee',
        self::DepositTypeInsurance => 'insurance',
        self::DepositTypeOther => 'other'
    ];

    const DepositStatusYes = 1;
    const DepositStatusNo = 2;
    const DepositStatus = [
        self::DepositStatusYes => 'yes',
        self::DepositStatusNo => 'no',
    ];

    /**
     * @var array
     */
    protected $permittedExtensions = [
        'pdf',
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'resident_id' => 'required|integer|exists:residents,id',
        'building_id' => 'required|integer|exists:buildings,id',
        'unit_id' => 'required|integer|exists:units,id',
        'start_date' => 'date',
        'end_date' => 'nullable|date|after_or_equal:start_date',
        'status' => 'digits_between:1,2|numeric',
        'type' => 'digits_between:1,3|numeric',
        'duration' => 'digits_between:1,2|numeric',
        'deposit_type' => 'digits_between:1,4|numeric',
        'deposit_status' => 'digits_between:1,2|numeric',
        'deposit_amount' => 'numeric',
    ];

    protected $table = 'contracts';

    /**
     * @var array
     */
    public $fillable = [
        'resident_id',
        'building_id',
        'unit_id',
        'type',
        'duration',
        'status',
        'contract_format',
        'deposit_type',
        'deposit_status',
        'deposit_amount',
        'start_date',
        'end_date',
        'monthly_rent_net',
        'monthly_rent_gross',
        'monthly_maintenance',
    ];

    /**
     * @var array
     */
    protected $dates = ['deleted_at', 'start_date', 'end_date'];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'resident_id' => 'integer',
        'building_id' => 'integer',
        'unit_id' => 'integer',
        'type' => 'integer',
        'duration' => 'integer',
        'status' => 'integer',
        'contract_formats' => 'string',
        'deposit_type' => 'integer',
        'deposit_status' => 'integer',
        'deposit_amount' => 'integer',
        'monthly_rent_net' => 'float',
        'monthly_rent_gross' => 'float',
        'monthly_maintenance' => 'float',
    ];

    protected static function boot()
    {
        parent::boot(); // TODO: Change the autogenerated stub

        // @TODO delete related media
    }

    /**
     * @return BelongsTo
     **/
    public function resident()
    {
        return $this->belongsTo(Resident::class, 'resident_id', 'id');
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
    public function unit()
    {
        return $this->belongsTo(Unit::class, 'unit_id', 'id');
    }
}
