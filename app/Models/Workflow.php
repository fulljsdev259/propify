<?php

namespace App\Models;

/**
 * Class Workflow
 * @package App\Models
 *
 * * @SWG\Definition (
 *      definition="Workflow",
 *      required={"title", "quarter"},
 *      @SWG\Property(
 *          property="id",
 *          description="id",
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
 *          property="category_id",
 *          description="category_id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="title",
 *          description="title",
 *          type="string",
 *      ),
 *      @SWG\Property(
 *          property="building_ids",
 *          description="building_ids",
 *          type="array",
 *          @SWG\Items()
 *      ),
 *      @SWG\Property(
 *          property="to_user_ids",
 *          description="to_user_ids",
 *          type="array",
 *          @SWG\Items()
 *      ),
 *      @SWG\Property(
 *          property="cc_user_ids",
 *          description="cc_user_ids",
 *          type="array",
 *          @SWG\Items()
 *      ),
 * )
 */
class Workflow extends AuditableModel
{
    public $fillable = [
        'quarter_id',
        'category_id',
        'title',
        'building_ids',
        'to_user_ids',
        'cc_user_ids',
    ];

    public $casts = [
        'quarter_id' => 'int',
        'category_id' => 'int',
        'title' => 'string',
        'building_ids' => 'array',
        'to_user_ids' => 'array',
        'cc_user_ids' => 'array',
    ];

    public function quarter()
    {
        return $this->belongsTo(Quarter::class);
    }

}
