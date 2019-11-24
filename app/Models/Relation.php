<?php

namespace App\Models;

use App\Traits\UniqueIDFormat;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\Relation
 *
 * @SWG\Definition (
 *      definition="Relation",
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
 *          property="quarter_id",
 *          description="quarter_id",
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
 *          property="status",
 *          description="status",
 *          type="integer"
 *      ),
 *     @SWG\Property(
 *          property="relation_format",
 *          description="relation_format",
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
 * @property int $id
 * @property int $resident_id
 * @property int|null $quarter_id
 * @property int|null $unit_id
 * @property int|null $status
 * @property string|null $relation_format
 * @property int|null $type
 * @property int|null $deposit_type
 * @property int|null $deposit_status
 * @property int|null $deposit_amount
 * @property float|null $monthly_rent_net
 * @property float|null $monthly_rent_gross
 * @property float|null $monthly_maintenance
 * @property \Illuminate\Support\Carbon|null $start_date
 * @property \Illuminate\Support\Carbon|null $end_date
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\OwenIt\Auditing\Models\Audit[] $audits
 * @property-read int|null $audits_count
 * @property-read \App\Models\Quarter|null $quarter
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Media[] $media
 * @property-read int|null $media_count
 * @property-read \App\Models\Resident $resident
 * @property-read \App\Models\Unit|null $unit
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Relation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Relation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Relation query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Relation whereBuildingId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Relation whereRelationFormat($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Relation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Relation whereDepositAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Relation whereDepositStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Relation whereDepositType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Relation whereEndDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Relation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Relation whereMonthlyMaintenance($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Relation whereMonthlyRentGross($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Relation whereMonthlyRentNet($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Relation whereResidentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Relation whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Relation whereStartDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Relation whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Relation whereUnitId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Relation whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Relation extends AuditableModel
{
    use UniqueIDFormat;

    const StatusActive = 1;
    const StatusInActive = 2;
    const StatusCanceled = 3;
    const Status = [
        self::StatusActive => 'active',
        self::StatusInActive => 'inactive',
        self::StatusCanceled => 'canceled',
    ];
    const StatusColorCode = [
        self::StatusActive => '#878810',
        self::StatusInActive => '#c8a331',
        self::StatusCanceled => '#6b0036',
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

    const TypeResident = 1;
    const TypeOwner = 2;
    const TypeFormerResident = 3;
    const Type = [
        self::TypeResident => 'tenant',
        self::TypeOwner => 'owner',
        self::TypeFormerResident => 'former_resident',
    ];

    /**
     * @var array
     */
    protected $permittedExtensions = [
        'pdf',
    ];

    protected $table = 'relations';

    /**
     * @var array
     */
    public $fillable = [
        'resident_id',
        'quarter_id',
        'unit_id',
        'type',
        'relation_format',
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
        'quarter_id' => 'integer',
        'unit_id' => 'integer',
        'type' => 'integer',
        'status' => 'integer',
        'relation_format' => 'string',
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

        self::saving(function (self $item) {
            // if not set end date or end date is today status is active
            // if set end date is future status canceled
            // if set end date is past status inactive
            if (empty($item->end_date) || $item->end_date->isToday()) {
                $item->status = self::StatusActive;
            } elseif($item->end_date->isFuture()) {
                $item->status = self::StatusCanceled;
            } else {
                $item->status = self::StatusInActive;
            }
        });
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
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function garant_residents()
    {
        return $this->belongsToMany(Resident::class, 'relation_garant_resident', 'relation_id', 'resident_id');
    }

    /**
     * @return BelongsTo
     **/
    public function quarter()
    {
        return $this->belongsTo(Quarter::class, 'quarter_id', 'id');
    }

    /**
     * @TODO delete
     * @return BelongsTo
     **/
    public function  building()
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

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function requests()
    {
        return $this->hasMany(Request::class);
    }

    public function media()
    {
        return $this->belongsToMany(Media::class, 'media_relation');
    }

    /**
     * @param array $data
     * @return array
     */
    public function transformAudit(array $data): array
    {
        if (self::EventDeleted == $data['event']) {
            $data['auditable_id'] = $data['old_values']['resident_id'];
        } else {
            $data['auditable_id'] = self::where('id', $data['auditable_id'])->value('resident_id');
        }
        $data['event'] = 'relation_' . $data['event'];
        $data['auditable_type'] = get_morph_type_of(Resident::class);
        return parent::transformAudit($data); // TODO: Change the autogenerated stub
    }
}
