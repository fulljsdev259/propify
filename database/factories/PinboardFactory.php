<?php

use Faker\Generator as Faker;
use App\Models\Pinboard;
use App\Models\User;

$factory->define(App\Models\Pinboard::class, function (Faker $faker) {
    $us = [
        User::where('deleted_at', null)->inRandomOrder()->first(),
        User::where('email', 'resident@example.com')->first(),
    ];
    $u = $us[rand(0, 1)];
    $statDate = $u->created_at;
    $now = now();
    $diffSec = $now->diffInSeconds($statDate);
    $now->subSeconds(random_int(1, $diffSec));
    $announcement = $faker->boolean;
    $type = $announcement ? Pinboard::TypeAnnouncement : Pinboard::TypePost;
    $ret = [
        'user_id' => $u->id,
        'type' => $type,
        'status' => Pinboard::StatusNew,
        'visibility' => Pinboard::VisibilityAll,
        'content' => $faker->paragraph(),
        'notify_email' => false,
        'announcement' => $announcement,
        'created_at' => $now,
        'updated_at' => $now,
    ];

    if ($announcement) {
        $subType = array_rand(Pinboard::SubType[$type]);
        $category = null;

        if ($subType == Pinboard::SubTypeMaintenance) {
            $category = array_rand(Pinboard::Category);
        }
        $executionStart = clone $now;
        $executionEnd = clone $executionStart;


        $ret['sub_type'] = $subType;
        $ret['category'] = $category;
        $ret['execution_period'] = Pinboard::ExecutionPeriodManyDay;
        $ret['execution_start'] = $executionStart;
        $ret['execution_end'] = $executionEnd->addDays(random_int(1, 10));
    }


    return $ret;
});
