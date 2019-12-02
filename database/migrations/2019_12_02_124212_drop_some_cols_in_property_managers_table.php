<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropSomeColsInPropertyManagersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('property_managers', function (Blueprint $table) {
            $table->dropColumn('slogan', 'linkedin_url', 'xing_url', 'mobile_phone', 'profession');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('property_managers', function (Blueprint $table) {
            $table->string('slogan', 191)->nullable();
            $table->string('linkedin_url', 191)->nullable();
            $table->string('xing_url', 191)->nullable();
            $table->string('mobile_phone')->nullable();
            $table->string('profession', 191)->nullable();
        });
    }
}
