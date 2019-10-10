<?php

namespace App\Console\Commands;

use App\Models\Role;
use Illuminate\Console\Command;

class FixPermissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix-permissions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix role permissions';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $allPermissions = \App\Models\Permission::get();
        $residentPermissions = $allPermissions->whereIn('name', config('permissions.resident'));
        $managerPermissions = $allPermissions->whereIn('name', config('permissions.manager'));
        $providerPermissions = $allPermissions->whereIn('name', config('permissions.provider'));


        $role = \App\Models\Role::whereName('administrator')->first();
        if ($role) {
            $role->savePermissions($allPermissions);
        }

        $role = \App\Models\Role::whereName('resident')->first();
        if ($role) {
            $role->savePermissions($residentPermissions);
        }

        $role = \App\Models\Role::whereName('manager')->first();
        if ($role) {
            $role->savePermissions($managerPermissions);
        }

        $role = \App\Models\Role::whereName('provider')->first();
        if ($role) {
            $role->savePermissions($providerPermissions);
        }



        $perms = \App\Models\Permission::with('roles')->get();
        foreach ($perms as $perm) {
            echo $perm->name  .": ". PHP_EOL;
            foreach ($perm->roles as $role) {
                echo '    ' . $role->name  .PHP_EOL;
            }
            echo PHP_EOL;
        }

        echo '---------------------------------' . PHP_EOL;

        $roles = Role::with('perms')->get();
        foreach ($roles as $role) {
            echo $role->name  .": ". PHP_EOL;
            foreach ($role->perms as $perm) {
                echo '    ' . $perm->name  .PHP_EOL;
            }
            echo PHP_EOL;
        }
    }
}
