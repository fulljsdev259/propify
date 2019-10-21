<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNewPermissionsInServiceProviders extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $role = \App\Models\Role::where('name', 'provider')->first();
        if ($role) {
            $role->attachPermissionIfNotExits('list-tag');
            $role->attachPermissionIfNotExits('view-tag');
            $role->attachPermissionIfNotExits('add-tag');
            $role->attachPermissionIfNotExits('edit-tag');
            $role->attachPermissionIfNotExits('delete-tag');
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('service_providers', function (Blueprint $table) {
            //
        });
    }
}
