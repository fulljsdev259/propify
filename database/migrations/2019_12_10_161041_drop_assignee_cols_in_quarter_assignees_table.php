<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropAssigneeColsInQuarterAssigneesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('quarter_assignees', function (Blueprint $table) {
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
        Schema::table('quarter_assignees', function (Blueprint $table) {
            $table->integer('assignee_id')->unsigned()->after('quarter_id');
            $table->string('assignee_type', 191)->after('assignee_id');
        });
    }
}
