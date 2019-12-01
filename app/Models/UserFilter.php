<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\UserFilter
 *
 * @SWG\Definition (
 *      definition="UserFilter",
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
 *      @SWG\Property(
 *          property="title",
 *          description="title",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="menu",
 *          description="menu",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="options_url",
 *          description="options_url",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="fields_data",
 *          description="fields_data",
 *          type="array",
 *          @SWG\Items()
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
class UserFilter extends Model
{
    public $fillable = [
        'user_id',
        'title',
        'menu',
        'options_url',
        'fields_data'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'user_id' => 'integer',
        'title' => 'string',
        'module' => 'string',
        'options_url' => 'string',
        'fields_data' => 'array',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     **/
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
