<?php

use App\Models\Settings;
use Illuminate\Database\Seeder;

class SettingsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $settings = new Settings();
        $settings->name = env('RL_NAME', 'Test Estate');
        $settings->email = env('RL_EMAIL', 'test@example.com');
        $settings->phone = env('RL_PHONE', '071112244');
        $settings->language = env('RL_LANG', 'en');
        $settings->cleanify_email = env('CLEANIFY_EMAIL', '');
        $settings->primary_color = '#6B0036';
        $settings->primary_color_lighter = '#c55a9059';
        $settings->accent_color = '#F7CA18';

        $settings->free_apartments_enable = false;
        $settings->opening_hours = json_encode($this->getOpeningHours());
        $settings->pinboard_receiver_ids = [];
        $settings->email_receptionist_ids = [];
        $settings->mail_host = env('MAIL_HOST', 'smtp.mailgun.org');
        $settings->mail_port = env('MAIL_PORT', 587);
        $settings->mail_username = env('MAIL_USERNAME');
        $settings->mail_password = env('MAIL_PASSWORD');
        $settings->mail_encryption = env('MAIL_ENCRYPTION', 'tls');
        $settings->mail_from_address = env('MAIL_FROM_ADDRESS', 'hello@example.com');
        $settings->mail_from_name = env('MAIL_FROM_NAME', 'Example');

        $address = factory(App\Models\Address::class, 1)->create()[0];
        $address->zip = 3172;
        $address->save();

        $settings->address_id = $address->id;

        $settings->save();
    }

    private function getOpeningHours()
    {
        $openingHoursList = [];

        $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];

        foreach ($days as $day) {
            $openingHours = new stdClass();
            $openingHours->day = $day;
            $openingHours->closed = false;
            $openingHours->start_hour = 8;
            $openingHours->start_min = 0;
            $openingHours->end_hour = 16;
            $openingHours->end_min = 50;
            if ($day > 4) {
                $openingHours->closed = false;
            }
            $openingHoursList[] = $openingHours;
        }

        return $openingHoursList;
    }
}
