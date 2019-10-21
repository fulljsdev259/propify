<?php

namespace App\Models;

use Zizaco\Entrust\EntrustPermission;


/**
 * App\Models\Permission
 *
 * @SWG\Definition (
 *      definition="Permission",
 *      required={"name"},
 *      @SWG\Property(
 *          property="id",
 *          description="id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="name",
 *          description="Unique name for the permission, used for looking up permission information in the application layer.",
 *          type="string"
 *      ),
 *     @SWG\Property(
 *          property="display_name",
 *          description="Human readable name for the permission.",
 *          type="string"
 *      ),
 *     @SWG\Property(
 *          property="description",
 *          description="A more detailed explanation of the Permission.",
 *          type="string"
 *      ),
 * )
 * @property int $id
 * @property string $name
 * @property string|null $display_name
 * @property string|null $description
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Role[] $roles
 * @property-read int|null $roles_count
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Permission newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Permission newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Permission query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Permission whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Permission whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Permission whereDisplayName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Permission whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Permission whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Permission whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Permission extends EntrustPermission
{
    protected $fillable = [
        'name', 'display_name', 'description'
    ];

    protected $hidden = ['created_at', 'pivot', 'updated_at'];
}
