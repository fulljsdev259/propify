<?php

namespace App\Models;

use App\Traits\HasCategoryMediaTrait;
use App\Traits\UniqueIDFormat;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia\HasMedia;

/**
 * @SWG\Definition(
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
 */
class Quarter extends AuditableModel implements HasMedia
{
    use SoftDeletes, UniqueIDFormat, HasCategoryMediaTrait;

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'name' => 'required|string',
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
    ];

    protected $permittedExtensions = [
        'pdf',
        'doc',
        'docx',
        'xls',
        'xlsx'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public function propertyManagers()
    {
        return $this->morphedByMany(PropertyManager::class, 'assignee', 'quarter_assignees', 'quarter_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public function users()
    {
        return $this->morphedByMany(User::class, 'assignee', 'quarter_assignees', 'quarter_id');
    }

    public function assignees()
    {
        return $this->hasMany(QuarterAssignee::class, 'quarter_id');
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

}
