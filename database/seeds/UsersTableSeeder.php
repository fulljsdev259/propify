<?php

use App\Models\Role;
use App\Models\User;
use App\Models\UserSettings;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // @TODO make correspond PM-s , and fix in _assignees table
        $superAdminRole = Role::where('name', 'administrator')->first();

        $attr = [
            'name' => 'Super Admin',
            'email' => 'dev@example.com',
            'phone' => '5296711335',
            'password' => bcrypt('dev@example.com'),
        ];
        $user = factory(User::class, 1)->create($attr)->first();
        $this->saveManager($user);

        $settings = $this->getSettings();
        $user->settings()->save($settings->replicate());
        $user->attachRole($superAdminRole);

        $attr = [
            'name' => 'Propify',
            'email' => 'admin@propify.ch',
            'phone' => '5296711335',
            'password' => bcrypt('adprop19-1'),
        ];
        $user = factory(User::class, 1)->create($attr)->first();
        $this->saveManager($user);

        $settings = $this->getSettings();
        $user->settings()->save($settings->replicate());
        $user->attachRole($superAdminRole);

        if (App::environment('local')) {
            $roles = Role::where('name', '!=', 'administrator')->get();
            factory(App\Models\User::class, 20)->create()->each(function ($user) use ($roles, $settings) {
                $settings->id = 0;
                $user->settings()->save($settings->replicate());

                $user->attachRole($roles->random());
            });
        }
    }

    protected function saveManager($user)
    {
        $nameParts = explode(' ' ,$user->name);
        $firstName = array_shift($nameParts);
        $lastName = implode(' ',$nameParts);
        $manager = \App\Models\PropertyManager::create([
                'first_name'  => $firstName,
                'lsat_name' => $lastName,
                'title' => $user->title,
                'type' => \App\Models\PropertyManager::TypeAdministrator,
                'user_id' => $user->id,
            ]);
    }

    private function getSettings()
    {
        $settings = new UserSettings();
        $settings->language = 'en';
        $settings->summary = 'daily';
        $settings->admin_notification = 1;
        $settings->pinboard_notification = 1;
        $settings->listing_notification = 1;
        $settings->service_notification = 1;

        return $settings;
    }
}
