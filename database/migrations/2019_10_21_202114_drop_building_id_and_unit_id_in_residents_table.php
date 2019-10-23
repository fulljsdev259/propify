<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropBuildingIdAndUnitIdInResidentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('residents', function (Blueprint $table) {

            $table->dropForeign('tenants_address_id_foreign');
            $table->dropForeign('tenants_building_id_foreign');
            $table->dropForeign('tenants_unit_id_foreign');
            $table->dropColumn('building_id', 'unit_id', 'address_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('residents', function (Blueprint $table) {
            $table->integer('address_id')->unsigned()->nullable()->index('tenants_address_id_foreign');
            $table->integer('building_id')->unsigned()->nullable()->index('tenants_building_id_foreign');
            $table->integer('unit_id')->unsigned()->nullable()->index('tenants_unit_id_foreign');
            $table->foreign('address_id', 'tenants_address_id_foreign')->references('id')->on('loc_addresses')->onUpdate('RESTRICT')->onDelete('RESTRICT');
            $table->foreign('building_id', 'tenants_building_id_foreign')->references('id')->on('buildings')->onUpdate('RESTRICT')->onDelete('RESTRICT');
            $table->foreign('unit_id', 'tenants_unit_id_foreign')->references('id')->on('units')->onUpdate('RESTRICT')->onDelete('RESTRICT');
        });
    }
}
