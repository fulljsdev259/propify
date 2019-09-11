<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTenantRentContractsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tenant_rent_contracts', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('tenant_id')->unsigned();
            $table->unsignedInteger('building_id')->nullable();
            $table->unsignedInteger('unit_id')->nullable();
            $table->smallInteger('type')->nullable()->default(\App\Models\TenantRentContract::TypePrivate);
            $table->smallInteger('duration')->nullable()->default(\App\Models\TenantRentContract::DurationUnlimited);
            $table->smallInteger('status')->nullable()->default(\App\Models\TenantRentContract::StatusActive);
            $table->string('tenant_rent_contract_format')->nullable();
            $table->smallInteger('deposit_type')->nullable()->default(\App\Models\TenantRentContract::DepositTypeBankDepot);
            $table->smallInteger('deposit_status')->nullable()->default(\App\Models\TenantRentContract::DepositStatusYes);
            $table->integer('deposit_amount')->nullable()->default(0);
            $table->integer('net_rent')->nullable()->default(0);
            $table->integer('operating_cost')->nullable()->default(0);
            $table->integer('gross_rent')->nullable()->default(0);
            $table->integer('parking_price')->nullable()->default(0);
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->string('pdf')->nullable();
            $table->timestamps();
            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->foreign('building_id')->references('id')->on('buildings');
            $table->foreign('unit_id')->references('id')->on('units');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tenant_rent_contracts');
    }
}
