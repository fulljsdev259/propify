<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropBuildingServiceProviderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::drop('building_service_provider');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::create('building_service_provider', function(Blueprint $table)
        {
            $table->integer('building_id')->unsigned()->index('building_service_provider_building_id_foreign');
            $table->integer('service_provider_id')->unsigned()->index('building_service_provider_service_provider_id_foreign');
            $table->foreign('building_id')->references('id')->on('buildings')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->foreign('service_provider_id')->references('id')->on('service_providers')->onUpdate('CASCADE')->onDelete('CASCADE');

        });
    }
}
