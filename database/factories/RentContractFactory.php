<?php

use App\Models\Resident;
use Faker\Generator as Faker;

$factory->define(App\Models\RentContract::class, function (Faker $faker) {
    $unit = \App\Models\Unit::has('building')->with('building:id')->inRandomOrder()->first(['id', 'building_id']);
    $duration = array_rand(\App\Models\RentContract::Duration);
    $randSec = random_int(1, 31536000);
    $startDate = now()->subSeconds($randSec);
    $endDate = (\App\Models\RentContract::DurationLimited == $duration) ? now()->subSeconds(random_int(1, $randSec)) : null;

    return [
        'resident_id' => \App\Models\Resident::inRandomOrder()->value('id'),
        'building_id' => $unit->building->id,
        'unit_id' => $unit->id,
        'type' => array_rand(\App\Models\RentContract::Type),
        'duration'=> $duration,
        'status' => array_rand(\App\Models\RentContract::Status),
        'deposit_type' => array_rand(\App\Models\RentContract::DepositType),
        'deposit_status' => array_rand(\App\Models\RentContract::DepositStatus),
        'deposit_amount' => random_int(1, 100),
        'start_date' => $startDate,
        'end_date' => $endDate,
        'monthly_rent_net' => random_int(1, 100),
        'monthly_rent_gross' => random_int(1, 100),
        'monthly_maintenance' => random_int(1, 100),
    ];
});
