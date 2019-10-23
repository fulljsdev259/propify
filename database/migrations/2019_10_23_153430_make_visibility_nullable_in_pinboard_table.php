<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MakeVisibilityNullableInPinboardTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pinboard', function (Blueprint $table) {
            $table->integer('visibility')->unsigned()->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pinboard', function (Blueprint $table) {

            $table->integer('visibility')->unsigned()->default(1)->change();
        });
    }
}
