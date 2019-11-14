<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeQuerterAssigneesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('quarter_assignees', function (Blueprint $table) {
            $table->text('assignment_types')->after('assignee_type')->nullable();
        });

        \App\Models\QuarterAssignee::get()->each(function (\App\Models\QuarterAssignee $quarterAssignee) {
            $quarterAssignee->assignment_types = [$quarterAssignee->assignment_type];
            $quarterAssignee->save();
        });

        Schema::table('quarter_assignees', function (Blueprint $table) {
            $table->dropColumn( 'assignment_type');
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
            $table->unsignedTinyInteger('assignment_type')->after('assignee_type')->nullable();
            $table->dropColumn( 'assignment_types');
        });
    }
}
