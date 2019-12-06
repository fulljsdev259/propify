<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTypeColumnInRequestsAssigneesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('request_assignees', function (Blueprint $table) {
            $table->unsignedTinyInteger('type')->default(1)->after('assignee_type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('request_assignees', function (Blueprint $table) {
            $table->dropColumn('type');
        });
    }
}
