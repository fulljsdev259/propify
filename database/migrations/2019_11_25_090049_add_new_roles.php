<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Models\Role;
use App\Models\Permission;

class AddNewRoles extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(4 != Role::count()) {
            return;
        }

        // old roles count is 4 only that case add this new roles
        $allPermissions = Permission::get();
        $initialLettingPermissions = $allPermissions->whereIn('name', config('permissions.manager'));
        $marketingPermissions = $allPermissions->whereIn('name', config('permissions.manager'));
        $siteSupervisionPermissions = $allPermissions->whereIn('name', config('permissions.manager'));

        $ILCUser = new Role();
        $ILCUser->name = 'initial_letting';
        $ILCUser->display_name = 'Initial letting';
        $ILCUser->description = '';
        $ILCUser->save();
        $ILCUser->attachPermissions($initialLettingPermissions);

        $MLCUser = new Role();
        $MLCUser->name = 'marketing';
        $MLCUser->display_name = 'Marketing';
        $MLCUser->description = '';
        $MLCUser->save();
        $MLCUser->attachPermissions($marketingPermissions);

        $SLCUser = new Role();
        $SLCUser->name = 'site_supervision';
        $SLCUser->display_name = 'Site supervision';
        $SLCUser->description = '';
        $SLCUser->save();
        $SLCUser->attachPermissions($siteSupervisionPermissions);
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
