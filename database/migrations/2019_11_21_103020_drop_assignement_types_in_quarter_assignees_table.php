<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropAssignementTypesInQuarterAssigneesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('quarter_assignees', function (Blueprint $table) {
            $table->dropColumn('assignment_types');
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
            $table->text('assignment_types')->after('assignee_type')->nullable();
        });
    }
}
