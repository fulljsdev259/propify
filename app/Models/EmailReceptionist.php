<?php

namespace App\Models;

use App\Traits\HasCategoryMediaTrait;
use App\Traits\UniqueIDFormat;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia\HasMedia;

/**
 * App\Models\EmailReceptionist
 *
 * @SWG\Definition (
 *      definition="EmailReceptionist",
 *      required={"name", "description"},
 *      @SWG\Property(
 *          property="id",
 *          description="id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="property_manager_id",
 *          description="property_manager_id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="model_id",
 *          description="model_id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="model_type",
 *          description="model_type",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="category",
 *          description="category",
 *          type="integer"
 *      ),
 * )
 * */
class EmailReceptionist extends Model
{
    /**
     * @var array
     */
    public $fillable = [
        'property_manager_id',
        'model_id',
        'model_type',
        'category',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function property_manager()
    {
        return $this->belongsTo(PropertyManager::class);
    }
}
