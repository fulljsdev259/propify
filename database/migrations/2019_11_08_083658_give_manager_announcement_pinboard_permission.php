<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class GiveManagerAnnouncementPinboardPermission extends Migration
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
            $role->attachPermissionIfNotExits('announcement-pinboard');
            $role->attachPermissionIfNotExits('view-buildings_statistics');
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
