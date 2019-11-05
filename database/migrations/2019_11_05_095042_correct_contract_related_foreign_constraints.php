<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CorrectContractRelatedForeignConstraints extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('requests', function(Blueprint $table)
        {
            $table->dropForeign('requests_rent_contract_id_foreign');
            $table->dropForeign('service_requests_tenant_id_foreign');
            $table->integer('resident_id')->unsigned()->default(0)->nullable()->change();
            $table->foreign('resident_id', 'service_requests_tenant_id_foreign')->references('id')->on('residents')->onUpdate('RESTRICT')->onDelete('set null');

            $table->integer('contract_id')->unsigned()->nullable()->change();
            $table->foreign('contract_id', 'requests_rent_contract_id_foreign')->references('id')->on('contracts')->onUpdate('RESTRICT')->onDelete('set null');
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
        Schema::table('requests', function(Blueprint $table)
        {
            $table->dropForeign('service_requests_tenant_id_foreign');
            $table->dropForeign('requests_rent_contract_id_foreign');

            $table->foreign('contract_id', 'requests_rent_contract_id_foreign')->references('id')->on('contracts')->onUpdate('RESTRICT')->onDelete('RESTRICT');
            $table->foreign('resident_id', 'service_requests_tenant_id_foreign')->references('id')->on('residents')->onUpdate('RESTRICT')->onDelete('CASCADE');
        });
        Schema::enableForeignKeyConstraints();
    }
}
