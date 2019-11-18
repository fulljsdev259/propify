<?php

use App\Models\Request;
use Faker\Generator as Faker;

$factory->define(App\Models\Request::class, function (Faker $faker) {

    $resident = (new App\Models\Resident)->inRandomOrder()->with('relations')->first();
    $status = $faker->randomElement(array_keys(Request::Status));
    $priority = $faker->randomElement(array_keys(Request::Priority));
    $qualification = $faker->randomElement(array_keys(Request::Qualification));
    $solvedDate = ($status == Request::StatusDone) ? now() : null;
    $randCategory = array_rand(Request::Category);
    
    return [
        'creator_user_id' => App\Models\User::withRole('administrator')->inRandomOrder()->first()->id ?? null,
        'category_id' => $randCategory,
        'sub_category_id' => ! empty(Request::CategorySubCategory[$randCategory]) ? \Illuminate\Support\Arr::random(Request::CategorySubCategory[$randCategory]) : null,
        'resident_id' => $resident->id,
        'relation_id' => $resident->relations->first()->id ?? null,
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
