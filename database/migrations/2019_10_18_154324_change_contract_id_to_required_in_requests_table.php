<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeContractIdToRequiredInRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \App\Models\Request::whereNull('contract_id')->with('resident.contracts')->get()->each(function ($request) {
            $contract = $request->resident->contracts->first();
            if (empty($contract)) {
                $request->delete();
            } else {
                $request->update(['contract_id' => $contract->id]);
            }
        });
        Schema::table('requests', function (Blueprint $table) {
            $table->integer('contract_id')->unsigned()->change();
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
            $table->integer('contract_id')->unsigned()->nullable()->change();
        });
    }
}
