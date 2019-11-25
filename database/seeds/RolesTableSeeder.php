<?php

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $allPermissions = Permission::get();
        $residentPermissions = $allPermissions->whereIn('name', config('permissions.resident'));
        $managerPermissions = $allPermissions->whereIn('name', config('permissions.manager'));
        $providerPermissions = $allPermissions->whereIn('name', config('permissions.provider'));
        $initialLettingPermissions = $allPermissions->whereIn('name', config('permissions.manager'));
        $marketingPermissions = $allPermissions->whereIn('name', config('permissions.manager'));
        $siteSupervisionPermissions = $allPermissions->whereIn('name', config('permissions.manager'));


        $RLCAdmin = new Role();
        $RLCAdmin->name = 'administrator';
        $RLCAdmin->display_name = 'Real estate company';
        $RLCAdmin->description = '';
        $RLCAdmin->save();
        $RLCAdmin->attachPermissions($allPermissions);

        $RLCManager = new Role();
        $RLCManager->name = 'manager';
        $RLCManager->display_name = 'Real Estate Employee';
        $RLCManager->description = '';
        $RLCManager->save();
        $RLCManager->attachPermissions($managerPermissions);

        $RLCService = new Role();
        $RLCService->name = 'provider';
        $RLCService->display_name = 'RLC Third part Service';
        $RLCService->description = '';
        $RLCService->save();
        $RLCService->attachPermissions($providerPermissions);

        $RLCUser = new Role();
        $RLCUser->name = 'resident';
        $RLCUser->display_name = 'Users (residents)';
        $RLCUser->description = '';
        $RLCUser->save();
        $RLCUser->attachPermissions($residentPermissions);
        
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
}
