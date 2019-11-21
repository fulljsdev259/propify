<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFiertNameLastNameInServiceProvidersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('service_providers', function (Blueprint $table) {
            $table->string('title')->nullable()->after('type');
            $table->string('first_name')->nullable()->after('title');
            $table->string('last_name')->nullable()->after('first_name');
            $table->renameColumn('name', 'company_name');
        });

        \App\Models\ServiceProvider::get()->each(function ($item) {
            $names = explode(' ', $item->company_name);
            $item->first_name = array_shift($names);
            $item->last_name = implode(' ', $names);
            $item->save();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('service_providers', function (Blueprint $table) {
            $table->dropColumn('title', 'first_name', 'last_name');
            $table->renameColumn('company_name', 'name');
        });
    }
}
