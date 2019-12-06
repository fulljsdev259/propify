<?php

use Illuminate\Database\Seeder;

class QuartersTableSeeder extends Seeder
{
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

        for ($i = 1; $i <= 10; $i++) {
            $address = factory(App\Models\Address::class)->create();
            $quarter = factory(App\Models\Quarter::class)->create([
                'name' => 'Quarter ' . $i,
                'types' => [array_rand(\App\Models\Quarter::Type)],
                'address_id' => $address->id
            ]);
        }
    }
}
