<?php

use Faker\Generator as Faker;

$factory->define(App\Models\Quarter::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'description' => $faker->paragraph,
        'count_of_buildings' => random_int(1, 20),
        'address_id' => \App\Models\Address::inRandomOrder()->first()->id,
        'internal_quarter_id' => $faker->word,
        'url' => $faker->url,
    ];
});
