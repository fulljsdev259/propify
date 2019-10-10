<?php

namespace App\Models;

use App\Traits\UniqueIDFormat;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @SWG\Definition(
 *      definition="Unit",
 *      required={"name", "floor"},
 *      @SWG\Property(
 *          property="id",
 *          description="id",
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
 *          property="type",
 *          description="type",
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
 *          property="floor",
 *          description="floor",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="room_no",
 *          description="room_no",
 *          type="string",
 *          format="string"
 *      ),
 *      @SWG\Property(
 *          property="basement",
 *          description="basement",
 *          type="integer",
 *          format="int32"
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
class Unit extends AuditableModel
{
    use SoftDeletes, UniqueIDFormat;

    public $table = 'units';

    protected $dates = ['deleted_at'];

    const TypeApartment = 1;
    const TypeBusiness = 2;
    const TypeHobbyRoom = 3;
    const TypeStoreroom = 4;
    const TypeUndergroundParkingSpace = 5;
    const TypeOutdoorParking = 6;
    const TypeMotorbikePitch = 7;

    const Type = [
        self::TypeApartment => 'apartment',
        self::TypeBusiness => 'business',
        self::TypeHobbyRoom => 'hobby_room',
        self::TypeStoreroom => 'storeroom',
        self::TypeUndergroundParkingSpace => 'underground_parking_space',
        self::TypeOutdoorParking => 'outdoor_parking',
        self::TypeMotorbikePitch => 'motorbike_pitch',
    ];

    public $fillable = [
        'building_id',
        'type',
        'name',
        'description',
        'floor',
        'monthly_rent_net',
        'monthly_rent_gross',
        'monthly_maintenance',
        'room_no',
        'basement',
        'attic',
        'unit_format',
        'sq_meter'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'building_id' => 'integer',
        'type' => 'integer',
        'name' => 'string',
        'description' => 'string',
        'floor' => 'integer',
        'monthly_rent_net' => 'float',
        'monthly_rent_gross' => 'float',
        'monthly_maintenance' => 'float',
        'room_no' => 'float',
        'basement' => 'boolean',
        'attic' => 'boolean',
        'unit_format' => 'string',
        'sq_meter' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'building_id' => 'required|integer',
        'type' => 'required|integer',
        'name' => 'required|string',
        'floor' => 'required'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     **/
    public function building()
    {
        return $this->belongsTo(Building::class, 'building_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     **/
    public function resident()
    {
        return $this->hasOne(Resident::class, 'unit_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function residents()
    {
        return $this->hasMany(Resident::class, 'unit_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function contracts()
    {
        return $this->hasMany(Contract::class);
    }

    public function getSqMeterAttribute($attribute)
    {
        return 0 == $attribute ? '' : $attribute;
    }
}
