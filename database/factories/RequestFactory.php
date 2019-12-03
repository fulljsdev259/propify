<?php

use App\Models\Request;
use Faker\Generator as Faker;

$factory->define(App\Models\Request::class, function (Faker $faker) {

    $resident = (new App\Models\Resident)->inRandomOrder()->with('relations')->first();
    $status = $faker->randomElement(array_keys(Request::Status));
    $priority = $faker->randomElement(array_keys(Request::Priority));
    $solvedDate = ($status == Request::StatusDone) ? now() : null;
    $randCategory = array_rand(Request::Category);
    $subCategory = ! empty(Request::CategorySubCategory[$randCategory]) ? \Illuminate\Support\Arr::random(Request::CategorySubCategory[$randCategory]) : null;

    $data = get_category_details($randCategory);
    $data['sub_categories'] = get_sub_category_details($subCategory);
    $qualificationCategory = (!empty($data['qualification_category']) || !empty($data['sub_categories']['qualification_category']))
        ? array_rand(Request::QualificationCategory) : null;
    $action = (!empty($data['action']) || !empty($data['sub_categories']['action']))
        ? array_rand(Request::Action)  : null;
    $location =  (!empty($data['location']) || !empty($data['sub_categories']['location']))
        ?  array_rand(Request::Location) : null;
    $component =  (!empty($data['component']) || !empty($data['sub_categories']['component']))
        ? ($faker->boolean ? 1 : 0) : null;
    $capturePhase = (!empty($data['capture_phase']) || !empty($data['sub_categories']['capture_phase']))
        ?  array_rand(Request::CapturePhase) : null;
    $room = (!empty($data['room']) || !empty($data['sub_categories']['room']))
        ?  array_rand(Request::Room) : null;
    return [
        'creator_user_id' => App\Models\User::withRole('administrator')->inRandomOrder()->first()->id ?? null,
        'category_id' => $randCategory,
        'sub_category_id' => $subCategory,
        'resident_id' => $resident->id,
        'relation_id' => $resident->relations->first()->id ?? null,
        'title' => $faker->sentence(4),
        'description' => $faker->text,
        'status' => $status,
        'priority' => $priority,
        'due_date' => $faker->dateTimeBetween('-30 days', '40 days'),
        'solved_date' => $solvedDate,
        'visibility' => $faker->numberBetween(1, 3),
        'qualification_category' => $qualificationCategory,
        'is_public' => $faker->boolean ? 1 : 0,
        'percentage' => random_int(1, 101) - 1,
        'cost_impact' => random_int(1, 101) - 1,
        'capture_phase' => $capturePhase,
        'action' => $action,
        'room' => $room,
        'location' => $location,
        'component' => $component,
    ];
});
