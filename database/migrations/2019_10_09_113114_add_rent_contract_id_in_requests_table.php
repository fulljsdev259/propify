<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddRentContractIdInRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('requests', function (Blueprint $table) {
            $table->integer('rent_contract_id')->unsigned()->after('tenant_id')->nullable();
            $table->foreign('rent_contract_id')->references('id')->on('tenant_rent_contracts')->onUpdate('RESTRICT')->onDelete('RESTRICT');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('requests', function (Blueprint $table) {
            $table->dropForeign('requests_rent_contract_id_foreign');
            $table->dropColumn('rent_contract_id');
        });
    }
}
