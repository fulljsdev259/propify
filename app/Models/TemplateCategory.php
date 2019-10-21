<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\TemplateCategory
 *
 * @SWG\Definition (
 *      definition="TemplateCategory",
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
 *      @SWG\Property(
 *          property="description",
 *          description="description",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="tags",
 *          description="tags",
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
 * @property string $description
 * @property string $subject
 * @property string $body
 * @property array|null $tag_map
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property int $type
 * @property int $system
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\TemplateCategory[] $categories
 * @property-read int|null $categories_count
 * @property-read \App\Models\TemplateCategory|null $parentCategory
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TemplateCategory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TemplateCategory newQuery()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\TemplateCategory onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TemplateCategory query()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TemplateCategory whereBody($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TemplateCategory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TemplateCategory whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TemplateCategory whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TemplateCategory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TemplateCategory whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TemplateCategory whereParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TemplateCategory whereSubject($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TemplateCategory whereSystem($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TemplateCategory whereTagMap($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TemplateCategory whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TemplateCategory whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\TemplateCategory withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\TemplateCategory withoutTrashed()
 * @mixin \Eloquent
 */
class TemplateCategory extends Model
{
    use SoftDeletes;

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'name' => 'required',
        'description' => 'required'
    ];

    public $table = 'template_categories';

    public $fillable = [
        'parent_id',
        'name',
        'description',
        'tag_map'
    ];

    protected $dates = ['deleted_at'];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'parent_id' => 'integer',
        'name' => 'string',
        'description' => 'string',
        'tag_map' => 'array'
    ];

    const TypeEmail = 1;
    const TypeCommunication = 2;

    const Type = [
        self::TypeEmail => 'email',
        self::TypeCommunication => 'communication',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     **/
    public function categories()
    {
        return $this->hasMany(TemplateCategory::class, 'parent_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     **/
    public function parentCategory()
    {
        return $this->belongsTo(TemplateCategory::class, 'parent_id', 'id');
    }
}
