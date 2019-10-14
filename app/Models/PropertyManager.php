<?php

namespace App\Models;

use App\Traits\BuildingRelation;
use App\Traits\RequestRelation;
use App\Traits\UniqueIDFormat;
use Chelout\RelationshipEvents\Concerns\HasBelongsToManyEvents;
use Chelout\RelationshipEvents\Concerns\HasMorphedByManyEvents;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @SWG\Definition(
 *      definition="PropertyManager",
 *      required={""},
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
 *          property="type",
 *          description="type",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="title",
 *          description="title",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="first_name",
 *          description="first_name",
 *          type="string"
 *      ),
 *     @SWG\Property(
 *          property="profession",
 *          description="profession",
 *          type="string"
 *      ),
 *     @SWG\Property(
 *          property="slogan",
 *          description="slogan",
 *          type="string"
 *      ),
 *     @SWG\Property(
 *          property="xing_url",
 *          description="xing_url",
 *          type="string"
 *      ),
 *     @SWG\Property(
 *          property="linkedin_url",
 *          description="linkedin_url",
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
class PropertyManager extends AuditableModel
{
    use SoftDeletes, UniqueIDFormat, RequestRelation, BuildingRelation;

    public $table = 'property_managers';

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        //'description' => 'sometimes|string',
        'first_name' => 'required|string',
        'last_name' => 'required|string',
    ];

    public static $rulesUpdate = [
        //'description' => 'sometimes|string',
        'first_name' => 'required|string',
        'last_name' => 'required|string',
    ];

    const Title = [
        'mr',
        'mrs',
    ];

    const TypeAdministrator = 1;
    const TypeManager = 2;

    const Type = [
        self::TypeAdministrator => 'administrator',
        self::TypeManager => 'manager',
    ];

    public $fillable = [
        'description',
        'user_id',
        'type',
        'title',
        'first_name',
        'last_name',
        'profession',
        'slogan',
        'xing_url',
        'linkedin_url',
        'property_manager_format',
    ];

    protected $dates = ['deleted_at'];

    /**
     * @var array
     */
    protected $auditEvents = [
        AuditableModel::EventCreated,
        AuditableModel::EventUpdated,
        AuditableModel::EventDeleted,
        AuditableModel::EventQuarterAssigned => 'getAttachedEventAttributes',
        AuditableModel::EventBuildingAssigned => 'getAttachedEventAttributes',
        AuditableModel::EventQuarterUnassigned => 'getDetachedEventAttributes',
        AuditableModel::EventBuildingUnassigned => 'getDetachedEventAttributes',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'description' => 'string',
        'user_id' => 'integer',
        'type' => 'integer',
        'title' => 'string',
        'first_name' => 'string',
        'last_name' => 'string',
        'profession' => 'string',
        'slogan' => 'string',
        'xing_url' => 'string',
        'linkedin_url' => 'string',
        'property_manager_format' => 'string',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     **/
    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    /**
     * @return mixed
     */
    public function settings()
    {
        return $this->hasOne(UserSettings::class, 'user_id', 'user_id');
    }

    /**
     * @return mixed
     */
    public function quarters()
    {
        return $this->morphToMany(Quarter::class, 'assignee', 'quarter_assignees', 'assignee_id', 'quarter_id');
    }
}
