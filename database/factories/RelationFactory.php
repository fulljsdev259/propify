<?php

use App\Models\Resident;
use Faker\Generator as Faker;

$factory->define(App\Models\Relation::class, function (Faker $faker) {
    $unit = \App\Models\Unit::has('building')->with('building:id')->inRandomOrder()->first(['id', 'building_id']);
    $randSec = random_int(1, 31536000);
    $startDate = now()->subSeconds($randSec);
    $endDate = $faker->boolean ? now()->subSeconds(random_int(1, $randSec)) : null;

    return [
        'resident_id' => \App\Models\Resident::inRandomOrder()->value('id'),
        'quarter_id' => $unit->quarter->id ?? \App\Models\Quarter::inRandomOrder()->value('id'),
        'unit_id' => $unit->id,
        'type' => array_rand(\App\Models\Relation::Type),
        'status' => array_rand(\App\Models\Relation::Status),
        'deposit_type' => array_rand(\App\Models\Relation::DepositType),
        'deposit_status' => array_rand(\App\Models\Relation::DepositStatus),
        'deposit_amount' => random_int(1, 100),
        'start_date' => $startDate,
        'end_date' => $endDate,
        'monthly_rent_net' => random_int(1, 100),
        'monthly_rent_gross' => random_int(1, 100),
        'monthly_maintenance' => random_int(1, 100),
    ];
});
