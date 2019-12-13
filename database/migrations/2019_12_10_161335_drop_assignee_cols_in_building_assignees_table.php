<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropAssigneeColsInBuildingAssigneesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('building_assignees', function (Blueprint $table) {
            $table->dropColumn('assignee_id', 'assignee_type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('building_assignees', function (Blueprint $table) {
            $table->integer('assignee_id')->unsigned()->after('building_id');
            $table->string('assignee_type', 191)->after('assignee_id');
        });
    }
}
