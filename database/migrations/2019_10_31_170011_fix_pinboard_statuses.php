<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class FixPinboardStatuses extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        update_db_fields(\App\Models\Pinboard::class, 'status', 4, 1);
        update_db_fields(\App\Models\Pinboard::class, 'status', 2, 1);
        update_db_fields(\App\Models\Pinboard::class, 'status', 3, 2);
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
