<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAssignementTypeInBuildingAssignees extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('building_assignees', function (Blueprint $table) {
            $table->unsignedInteger('user_id')->after('building_id')->nullable();
            $table->text('assignment_types')->after('assignee_type')->nullable();
        });

        \App\Models\BuildingAssignee::get()->each(function (\App\Models\BuildingAssignee $buildingAssignee) {
            if ($buildingAssignee->assignee_type == get_morph_type_of(\App\Models\PropertyManager::class)) {
                $buildingAssignee->user_id = \App\Models\PropertyManager::find($buildingAssignee->assignee_id)->user_id ?? null;
                $buildingAssignee->assignment_types = [\App\Models\Quarter::AssignmentTypeFortimoEmployees];
                $buildingAssignee->save();
            }
            if ($buildingAssignee->assignee_type == get_morph_type_of(\App\Models\ServiceProvider::class)) {
                $buildingAssignee->user_id = \App\Models\ServiceProvider::find($buildingAssignee->assignee_id)->user_id ?? null;
                $buildingAssignee->assignment_type = [\App\Models\Quarter::AssignmentTypeFortimoEmployees];
                $buildingAssignee->save();
            }
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
            $table->dropColumn('user_id', 'assignment_types');
        });
    }
}
