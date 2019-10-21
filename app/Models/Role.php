<?php

namespace App\Models;

use Zizaco\Entrust\EntrustRole;


/**
 * App\Models\Role
 *
 * @SWG\Definition (
 *      definition="Role",
 *      required={"name"},
 *      @SWG\Property(
 *          property="id",
 *          description="id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="name",
 *          description="name",
 *          type="string"
 *      ),
 *     @SWG\Property(
 *          property="display_name",
 *          description="Human readable name for the Role. Not necessarily unique and optional.",
 *          type="string"
 *      ),
 *     @SWG\Property(
 *          property="description",
 *          description="A more detailed explanation of what the Role does. Also optional.",
 *          type="string"
 *      ),
 * )
 * @property int $id
 * @property string $name
 * @property string|null $display_name
 * @property string|null $description
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Permission[] $perms
 * @property-read int|null $perms_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\User[] $users
 * @property-read int|null $users_count
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Role newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Role newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Role query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Role whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Role whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Role whereDisplayName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Role whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Role whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Role whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Role extends EntrustRole
{
    /**
     * @param $permissionName
     */
    public function attachPermissionIfNotExits($permissionName)
    {
        if (! $this->hasPermission($permissionName)) {
            $permission = Permission::whereName($permissionName)->first();
            if ($permission) {
                $this->attachPermission($permission);
            }
        }
    }

    /**
     * @param $permissionName
     */
    public function detachPermissionIfExits($permissionName)
    {
        if ($this->hasPermission($permissionName)) {
            $permission = Permission::whereName($permissionName)->first();
            if ($permission) {
                $this->detachPermission($permission);
            }
        }
    }
}


