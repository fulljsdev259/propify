<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropQuarterServiceProviderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::drop('quarter_service_provider');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::create('quarter_service_provider', function(Blueprint $table)
        {
            $table->integer('quarter_id')->unsigned()->index('district_service_provider_district_id_foreign');
            $table->integer('service_provider_id')->unsigned()->index('district_service_provider_service_provider_id_foreign');
            $table->foreign('quarter_id')->references('id')->on('quarters')->onUpdate('RESTRICT')->onDelete('CASCADE');

        });
    }
}
