<?php

namespace App\Models;

use App\Traits\Translatable;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\Template
 *
 * @SWG\Definition (
 *      definition="Template",
 *      required={"name"},
 *      @SWG\Property(
 *          property="id",
 *          description="id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="category_id",
 *          description="category_id",
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
 *          property="system",
 *          description="system",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="name",
 *          description="name",
 *          type="bool"
 *      ),
 *      @SWG\Property(
 *          property="subject",
 *          description="subject",
 *          type="string"
 *      ),
 *     @SWG\Property(
 *          property="body",
 *          description="body",
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
 * @property int $category_id
 * @property int $type
 * @property string $name
 * @property string $subject
 * @property string|null $body
 * @property int $default
 * @property bool $system
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\OwenIt\Auditing\Models\Audit[] $audits
 * @property-read int|null $audits_count
 * @property-read \App\Models\TemplateCategory $category
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Translation[] $translations
 * @property-read int|null $translations_count
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Template newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Template newQuery()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Template onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Template query()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Template whereBody($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Template whereCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Template whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Template whereDefault($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Template whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Template whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Template whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Template whereSubject($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Template whereSystem($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Template whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Template whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Template withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Template withoutTrashed()
 * @mixin \Eloquent
 */
class Template extends AuditableModel
{
    use SoftDeletes;
    use Translatable;

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'name' => 'required',
        'category_id' => 'required',
        'subject' => 'required',
        'type' => 'required'
    ];

    public $table = 'templates';

    public $fillable = [
        'category_id',
        'name',
        'subject',
        'body',
        'type'
    ];

    protected $dates = ['deleted_at'];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'category_id' => 'integer',
        'type' => 'integer',
        'name' => 'string',
        'subject' => 'string',
        'body' => 'string',
        'system' => 'boolean'
    ];

    public function translatable()
    {
        return [
            'subject',
            'body',
        ];
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     **/
    public function category()
    {
        return $this->hasOne(TemplateCategory::class, 'id', 'category_id');
    }
}
