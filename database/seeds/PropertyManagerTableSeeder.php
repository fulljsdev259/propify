<?php

use App\Models\Building;
use App\Models\PropertyManager;
use App\Models\Role;
use App\Models\User;
use App\Models\UserSettings;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;

class PropertyManagerTableSeeder extends Seeder
{
    use \Traits\TimeTrait;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (!App::environment('local')) {
            return;
        }

        $faker = Faker::create();
        $settings = $this->getSettings();

        $totalManagers = 200;
//        $buildings = Building::inRandomOrder()->limit($totalManagers)->get();        // @TODO DELETE
        $quarters = \App\Models\Quarter::inRandomOrder()->limit($totalManagers)->get();
        $managerRoles = Role::whereIn('name', PropertyManager::Type)->get();
        for ($i = 0; $i < $totalManagers; $i++) {
            $managerRole = $managerRoles->random();
            $managerData = factory(PropertyManager::class)->make()->toArray();
            $email = $faker->email;
            $attr = [
                'name' => sprintf('%s %s', $managerData['first_name'], $managerData['last_name']),
                'email' => $email,
                'phone' => $faker->phoneNumber,
                'password' => bcrypt($email),
            ];
            $date = $this->getRandomTime();
            $attr = array_merge($attr, $this->getDateColumns($date));
            $user = factory(User::class, 1)->create($attr)->first();

            $user->attachRole($managerRole);

            $user->settings()->save($settings->replicate());
            $managerData['user_id'] = $user->id;
            $managerData['title'] = $user->title;

            $date = $this->getRandomTime($user->created_at);
            $managerData = array_merge($managerData, $this->getDateColumns($date));
            $manager = factory(PropertyManager::class)->create($managerData);

            // @TODO DELETE
//            $building = $buildings->random();
//            $manager->buildings()->attach([$building->id => [
//                'created_at' => now(),
//                'user_id' => $user->id
//            ]]);

            $quarter = $quarters->random();
            $manager->quarters()->attach([$quarter->id => [
                'created_at' => now(),
                'user_id' => $user->id
            ]]);
        }
    }

    private function getSettings()
    {
        $languages = config('app.locales');

        $settings = new UserSettings();
        $settings->language = array_rand($languages);
        $settings->summary = 'daily';
        $settings->admin_notification = 1;
        $settings->pinboard_notification = 1;
        $settings->listing_notification = 1;
        $settings->service_notification = 1;

        return $settings;
    }
}
