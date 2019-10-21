<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropUnitIdInRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('requests', function (Blueprint $table) {
            $table->dropForeign('service_requests_unit_id_foreign');
            $table->dropColumn('unit_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

        Schema::disableForeignKeyConstraints();
        Schema::table('requests', function (Blueprint $table) {
            $table->integer('unit_id')->after('category')->unsigned()->default(0)->index('service_requests_unit_id_foreign');
            $table->foreign('unit_id', 'service_requests_unit_id_foreign')->references('id')->on('units')->onUpdate('RESTRICT')->onDelete('RESTRICT');
        });
        Schema::enableForeignKeyConstraints();
    }
}
