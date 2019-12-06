<?php

use Faker\Generator as Faker;

$factory->define(App\Models\ServiceProvider::class, function (Faker $faker, array $attr) {
    $serviceCategories = [
        'electrician',
        'heating_company',
        'lift,sanitary',
        'key_service',
        'caretaker',
        'real_estate_service',
    ];
    $randomCat = $faker->numberBetween(1, count($serviceCategories)-1);

    $category = isset($attr['category']) ? $attr['category']: $serviceCategories[$randomCat];
    $user_id = isset($attr['user_id']) ? $attr['user_id'] : 1;
    $address_id = isset($attr['address_id']) ? $attr['address_id'] : 1;

    return [
        'user_id' => $user_id,
        'address_id' => $address_id,      
        'category' => $category,
        'company_name' => $faker->name,
        'first_name' => $faker->firstName,
        'last_name' => $faker->lastName,
        'email' => $faker->email,
        'phone' => $faker->phoneNumber,
        'mobile_phone' => $faker->phoneNumber,
        'title' => \Illuminate\Support\Arr::random(\App\Models\ServiceProvider::Title),
        'status' => array_rand(\App\Models\ServiceProvider::Status),
        'type' => array_rand(\App\Models\ServiceProvider::Type),
    ];
});
 