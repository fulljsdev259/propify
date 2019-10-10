<?php

use Faker\Generator as Faker;
use App\Models\Listing;
use App\Models\User;

$factory->define(Listing::class, function (Faker $f) {
    $u = [
        User::where('deleted_at', null)->inRandomOrder()->first(),
        User::where('email', 'resident@example.com')->first(),
    ][rand(0, 1)];
    $t = [Listing::TypeSell, Listing::TypeLend][rand(0, 1)];

    $statDate = $u->created_at;
    $now = now();
    $diffSec = $now->diffInSeconds($statDate);
    $now->subSeconds(random_int(1, $diffSec));

    $ret = [
        'user_id' => $u->id,
        'type' => $t,
        'status' => Listing::StatusPublished,
        'visibility' => Listing::VisibilityAll,
        'content' => $f->paragraph(),
        'title' => $f->sentence(),
        'published_at' => \Carbon\Carbon::now(),
        'contact' => implode(" ", [$f->firstName, $f->lastName, $f->phoneNumber]),
        'price' => '1.10',
        'created_at' => $now,
        'updated_at' => $now,
    ];

    if ($u->resident && $u->resident->building) {
        $ret['address_id'] = $u->resident->building->address_id;
        $ret['quarter_id'] = $u->resident->building->quarter_id;
    }

    return $ret;
});
