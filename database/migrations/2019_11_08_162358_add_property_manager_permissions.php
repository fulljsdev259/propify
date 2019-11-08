<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPropertyManagerPermissions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        $role = \App\Models\Role::where('name', 'manager')->first();
        if ($role) {
            $role->attachPermissionIfNotExits('view-property_manager');
            $role->attachPermissionIfNotExits('edit-property_manager');
            $role->attachPermissionIfNotExits('delete-property_manager');
            $role->attachPermissionIfNotExits('assign-property_manager');
            $role->detachPermissionIfExits('list-audit');
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
