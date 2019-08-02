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
        $managerRole = Role::where('name', 'manager')->first();
        $settings = $this->getSettings();

        $totalManagers = 200;
        $buildings = Building::inRandomOrder()->limit($totalManagers)->get();

        for ($i = 0; $i < $totalManagers; $i++) {
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

            $building = $buildings->random();
            $manager->buildings()->attach($building);
        }
    }

    private function getSettings()
    {
        $languages = config('app.locales');

        $settings = new UserSettings();
        $settings->language = array_rand($languages);
        $settings->summary = 'daily';
        $settings->admin_notification = 1;
        $settings->news_notification = 1;
        $settings->marketplace_notification = 1;
        $settings->service_notification = 1;

        return $settings;
    }
}
