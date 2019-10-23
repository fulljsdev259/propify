<?php

use App\Models\Request;
use Faker\Generator as Faker;

$factory->define(App\Models\Request::class, function (Faker $faker) {

    $resident = (new App\Models\Resident)->where('unit_id', '>', 0)->inRandomOrder()->first();
    $status = $faker->randomElement(array_keys(Request::Status));
    $priority = $faker->randomElement(array_keys(Request::Priority));
    $qualification = $faker->randomElement(array_keys(Request::Qualification));
    $solvedDate = ($status == Request::StatusDone) ? now() : null;
    $randCategory = array_rand(Request::Category);

    return [
        'creator_user_id' => App\Models\User::withRole('administrator')->inRandomOrder()->first()->id ?? null,
        'category' => $randCategory,
        'sub_category' => ! empty(Request::CategorySubCategory[$randCategory]) ? array_rand(array_keys(Request::CategorySubCategory[$randCategory])) : null,
        'resident_id' => $resident->id,
        'unit_id' => $resident->unit_id,
        'title' => $faker->sentence(4),
        'description' => $faker->text,
        'status' => $status,
        'priority' => $priority,
        'qualification' => $qualification,
        'due_date' => $faker->dateTimeBetween('-30 days', '40 days'),
        'solved_date' => $solvedDate,
        'visibility' => $faker->numberBetween(1, 3),
    ];
});
