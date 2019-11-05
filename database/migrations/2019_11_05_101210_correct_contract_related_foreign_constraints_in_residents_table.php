<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CorrectContractRelatedForeignConstraintsInResidentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('residents', function (Blueprint $table) {
            $table->dropForeign('tenants_default_rent_contract_id_foreign');
            $table->foreign('default_contract_id', 'tenants_default_rent_contract_id_foreign')->references('id')->on('contracts')->onUpdate('RESTRICT')->onDelete('set null');
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
        Schema::table('residents', function (Blueprint $table) {
            $table->dropForeign('tenants_default_rent_contract_id_foreign');
            $table->foreign('default_contract_id', 'tenants_default_rent_contract_id_foreign')->references('id')->on('contracts')->onUpdate('RESTRICT')->onDelete('RESTRICT');
        });
        Schema::enableForeignKeyConstraints();
    }
}
