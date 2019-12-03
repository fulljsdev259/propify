<?php

use Faker\Generator as Faker;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(App\Models\Unit::class, function (Faker $faker) {
    $roomNumberList = [
        1,
        1.5,
        2,
        2.5,
        3,
        3.5,
        4,
        4.5,
        5,
        5.5,
        6,
        6.5
    ];

    return [
        'type' => $faker->numberBetween(1, 2),
        'name' => $faker->sentence(3),
        'description' => $faker->sentence(5),
        'floor' => $faker->numberBetween(0, 30),
        'room_no' => $roomNumberList[$faker->numberBetween(0, count($roomNumberList)-1)],
        'monthly_rent_net' => rand(0, 100),
        'monthly_maintenance' => rand(0, 100),
        'sq_meter' => rand(0, 100),
        'monthly_rent_gross' => rand(0, 100),
    ];
});
