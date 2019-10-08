<?php

use App\Models\Role;
use App\Models\User;
use App\Models\UserSettings;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;

class ServiceProvidersTableSeeder extends Seeder
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
        $serviceRole = Role::where('name', 'provider')->first();
        $settings = $this->getSettings();

        $serviceCategories = \App\Models\ServiceProvider::ServiceProviderCategory;

        $providerCount = 200;
        $categoryCount = floor($providerCount / count($serviceCategories));
        foreach ($serviceCategories as $category) {
            for ($i  = 0; $i < $categoryCount; $i++) {
                $email = $faker->email;
                $date = $this->getRandomTime();

                $attr = [
                    'name' => $faker->name,
                    'email' => $email,
                    'phone' => $faker->phoneNumber,
                    'password' => bcrypt($email),
                ];
                $attr = array_merge($attr, $this->getDateColumns($date));

                $user = factory(User::class)->create($attr);
                $user->attachRole($serviceRole);
                $user->settings()->save($settings->replicate());

                //create User
                $date = $this->getRandomTime($user->created_at);
                $address = factory(App\Models\Address::class)->create($this->getDateColumns($date));
                $attr = [
                    'category' => $category,
                    'user_id' => $user->id,
                    'address_id' => $address->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                ];
                $date = $this->getRandomTime($address->created_at);
                $attr = array_merge($attr, $this->getDateColumns($date));

                factory(App\Models\ServiceProvider::class)->create($attr);
            }
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
