<?php

use App\Models\Resident;
use App\Models\User;
use Faker\Generator as Faker;

$factory->define(App\Models\PropertyManager::class, function (Faker $faker, array $attr) {
    $title = Resident::Title[$faker->numberBetween(0, count(Resident::Title) - 1)];
    $user_id = isset($attr['user_id']) ? $attr['user_id'] : User::withRole('manager')->inRandomOrder()->first();

    return [
        'user_id' => $user_id,
        'description' => $faker->sentence(4),
        'title' => $title,
        'first_name' => $faker->firstName,
        'last_name' => $faker->lastName,
        'profession' => $faker->jobTitle,
        'slogan' => $faker->paragraph,
        'xing_url' => $faker->url,
        'linkedin_url' => $faker->url,
    ];
});

