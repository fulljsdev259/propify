<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\Translation
 *
 * @SWG\Definition (
 *      definition="Translation",
 *      required={"object_type", "object_id", "language", "name", "value"},
 *      @SWG\Property(
 *          property="id",
 *          description="id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="object_type",
 *          description="object_type",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="object_id",
 *          description="object_id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="language",
 *          description="language",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="name",
 *          description="name",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="value",
 *          description="value",
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
 * @property string $object_type
 * @property int $object_id
 * @property string $language
 * @property string $name
 * @property string $value
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\OwenIt\Auditing\Models\Audit[] $audits
 * @property-read int|null $audits_count
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Translation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Translation newQuery()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Translation onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Translation query()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Translation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Translation whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Translation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Translation whereLanguage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Translation whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Translation whereObjectId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Translation whereObjectType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Translation whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Translation whereValue($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Translation withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Translation withoutTrashed()
 * @mixin \Eloquent
 */
class Translation extends AuditableModel
{
    use SoftDeletes;

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'object_type' => 'required',
        'object_id' => 'required',
        'language' => 'required',
        'name' => 'required',
        'value' => 'required'
    ];

    public $table = 'translations';
    public $fillable = [
        'object_type',
        'object_id',
        'language',
        'name',
        'value'
    ];
    protected $dates = ['deleted_at'];
    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'object_type' => 'string',
        'object_id' => 'integer',
        'language' => 'string',
        'name' => 'string',
        'value' => 'string'
    ];


}
