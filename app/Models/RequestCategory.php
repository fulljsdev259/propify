<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\RequestCategory
 *
 * @SWG\Definition (
 *      definition="RequestCategory",
 *      required={"name", "description"},
 *      @SWG\Property(
 *          property="id",
 *          description="id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="parent_id",
 *          description="parent_id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="name",
 *          description="name",
 *          type="string"
 *      ),
 *     @SWG\Property(
 *          property="acquisition",
 *          description="acquisition",
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
 * @property int|null $parent_id
 * @property string $name
 * @property string $name_de
 * @property string $name_fr
 * @property string $name_it
 * @property int|null $room
 * @property int|null $location
 * @property string|null $description
 * @property int $has_qualifications
 * @property int $acquisition
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\RequestCategory[] $categories
 * @property-read int|null $categories_count
 * @property-read \App\Models\RequestCategory|null $parentCategory
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\RequestCategory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\RequestCategory newQuery()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\RequestCategory onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\RequestCategory query()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\RequestCategory whereAcquisition($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\RequestCategory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\RequestCategory whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\RequestCategory whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\RequestCategory whereHasQualifications($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\RequestCategory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\RequestCategory whereLocation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\RequestCategory whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\RequestCategory whereNameDe($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\RequestCategory whereNameFr($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\RequestCategory whereNameIt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\RequestCategory whereParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\RequestCategory whereRoom($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\RequestCategory whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\RequestCategory withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\RequestCategory withoutTrashed()
 * @mixin \Eloquent
 */
class RequestCategory extends Model
{
    use SoftDeletes;

    public $table = 'request_categories';


    protected $dates = ['deleted_at'];


    public $fillable = [
        'parent_id',
        'name',
        'name_de',
        'name_fr',
        'name_it',
        'room',
        'location',
        'description',
        'has_qualifications',
        'acquisition'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'parent_id' => 'integer',
        'name' => 'string',
        'name_de'=> 'string',
        'name_fr'=> 'string',
        'name_it'=> 'string',
        'room'=> 'integer',
        'location'=> 'integer',
        'description' => 'string',
        'acquisition' => 'integer',
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'name' => 'required|string',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     **/
    public function categories()
    {
        return $this->hasMany(RequestCategory::class, 'parent_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     **/
    public function parentCategory()
    {
        return $this->belongsTo(RequestCategory::class, 'parent_id', 'id');
    }
}
