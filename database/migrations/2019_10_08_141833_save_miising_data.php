<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SaveMiisingData extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('residents')) {
            \App\Models\Resident::whereNull('nation')->get()->each(function ($resident) {
                $resident->nation =  \App\Models\Country::inRandomOrder()->first()->id;
                $resident->save();
            });
        }
        \App\Models\Building::where('contact_enable', 0)->get()->each(function ($building) {
            $building->contact_enable =  array_rand(\App\Models\Building::BuildingContactEnables);
            $building->save();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
