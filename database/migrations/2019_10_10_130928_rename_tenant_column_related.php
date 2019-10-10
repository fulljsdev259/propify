<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RenameTenantColumnRelated extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('login_devices', function (Blueprint $table) {
            $table->renameColumn('tenant_id', 'resident_id');
        });

        Schema::table('pinboard_view', function (Blueprint $table) {
            $table->renameColumn('tenant_id', 'resident_id');
        });

        Schema::table('settings', function (Blueprint $table) {
            $table->renameColumn('tenant_logo', 'resident_logo');
        });

        Schema::table('tenant_rent_contracts', function (Blueprint $table) {
            $table->renameColumn('tenant_id', 'resident_id');
        });


        Schema::table('requests', function (Blueprint $table) {
            $table->renameColumn('tenant_id', 'resident_id');
            $table->renameColumn('rent_contract_id', 'contract_id');
        });

        Schema::table('announcement_email_receptionists', function (Blueprint $table) {
            $table->renameColumn('tenant_ids', 'resident_ids');
            $table->renameColumn('failed_tenant_ids', 'failed_resident_ids');
        });

        Schema::table('tenants', function (Blueprint $table) {
            $table->renameColumn('tenant_format', 'resident_format');
            $table->renameColumn('client_type', 'type');
            $table->renameColumn('default_rent_contract_id', 'default_contract_id');
        });

        Schema::rename('tenant_rent_contracts', 'contracts');
        Schema::rename('tenants', 'residents');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::rename('contracts', 'tenant_rent_contracts');
        Schema::rename('residents', 'tenants');

        Schema::table('login_devices', function (Blueprint $table) {
            $table->renameColumn('resident_id', 'tenant_id');
        });

        Schema::table('pinboard_view', function (Blueprint $table) {
            $table->renameColumn('resident_id', 'tenant_id');
        });

        Schema::table('settings', function (Blueprint $table) {
            $table->renameColumn('resident_logo', 'tenant_logo');
        });

        Schema::table('tenant_rent_contracts', function (Blueprint $table) {
            $table->renameColumn('resident_id', 'tenant_id');
        });


        Schema::table('requests', function (Blueprint $table) {
            $table->renameColumn('resident_id', 'tenant_id');
            $table->renameColumn('contract_id', 'rent_contract_id');
        });

        Schema::table('announcement_email_receptionists', function (Blueprint $table) {
            $table->renameColumn('resident_ids', 'tenant_ids');
            $table->renameColumn('failed_resident_ids', 'failed_tenant_ids');
        });

        Schema::table('tenants', function (Blueprint $table) {
            $table->renameColumn('resident_format', 'tenant_format');
            $table->renameColumn('type', 'client_type');
            $table->renameColumn('default_contract_id', 'default_rent_contract_id');
        });

    }
}
