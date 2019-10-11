<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class FillRequestCreatorColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (App\Models\User::withRole('administrator')->exists()) {
            \App\Models\Request::whereNull('creator_user_id')->get()->each(function ($request) {
                $request->update(['creator_user_id' => App\Models\User::withRole('administrator')->inRandomOrder()->first()->id]);
            });
        }
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
