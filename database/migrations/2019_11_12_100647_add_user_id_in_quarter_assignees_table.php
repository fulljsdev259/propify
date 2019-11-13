<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddUserIdInQuarterAssigneesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('quarter_assignees', function (Blueprint $table) {
            $table->unsignedInteger('user_id')->after('quarter_id')->nullable();
            $table->unsignedTinyInteger('assignment_type')->after('assignee_type')->nullable();
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
            $table->dropColumn('user_id', 'assignment_type');
        });
    }
}
