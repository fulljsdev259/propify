<?php

use Faker\Generator as Faker;

$factory->define(App\Models\Building::class, function (Faker $faker) {
    $address = factory(\App\Models\Address::class)->create();
    $quarterId = $faker->boolean ?  \App\Models\Quarter::inRandomOrder()->first()->id : null;
    return [
        'description' => $faker->sentence(5),
        'label' => $faker->sentence(3),
        'address_id' => $address->id,
        'quarter_id' => $quarterId,
        'global_email_receptionist' => (bool)$quarterId,
        'floor_nr' => $faker->numberBetween(1, 30),
        'basement' => $faker->numberBetween(0, 1),
        'attic' => $faker->numberBetween(0, 1),
        'contact_enable' => array_rand(\App\Models\Building::BuildingContactEnables),
        'internal_building_id' => $faker->word,
        'under_floor' => random_int(1, 4) - 1,
    ];
});
