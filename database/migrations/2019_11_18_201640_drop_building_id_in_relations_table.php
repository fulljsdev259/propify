<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropBuildingIdInRelationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('relations', function (Blueprint $table) {
            $table->dropForeign('tenant_rent_contracts_building_id_foreign');
            $table->dropColumn('building_id');
            $table->unsignedInteger('quarter_id')->after('resident_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('relations', function (Blueprint $table) {
            $table->integer('building_id')->unsigned()->nullable()->after('resident_id');
            $table->dropColumn('quarter_id');
            $table->foreign('building_id', 'tenant_rent_contracts_building_id_foreign')->references('id')->on('buildings')->onUpdate('RESTRICT')->onDelete('RESTRICT');

        });
    }
}
