<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RenameDistrictToQuarters extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::rename('districts', 'quarters');
        Schema::rename('district_property_manager', 'quarter_property_manager');
        Schema::rename('district_service_provider', 'quarter_service_provider');
        Schema::rename('district_post', 'quarter_post');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::rename('quarters', 'districts');
        Schema::rename('quarter_property_manager', 'district_property_manager');
        Schema::rename('quarter_service_provider', 'district_service_provider');
        Schema::rename('quarter_post', 'district_post');
    }
}