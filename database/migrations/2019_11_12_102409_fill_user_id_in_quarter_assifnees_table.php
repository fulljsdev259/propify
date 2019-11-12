<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class FillUserIdInQuarterAssifneesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \App\Models\QuarterAssignee::get()->each(function (\App\Models\QuarterAssignee $quarterAssignee) {
            if ($quarterAssignee->assignee_type == get_morph_type_of(\App\Models\PropertyManager::class)) {
                $quarterAssignee->user_id = \App\Models\PropertyManager::find($quarterAssignee->assignee_id)->user_id ?? null;
                $quarterAssignee->assignment_type = \App\Models\Quarter::AssignmentTypeFortimoEmployees;
                $quarterAssignee->save();
            }
            if ($quarterAssignee->assignee_type == get_morph_type_of(\App\Models\ServiceProvider::class)) {
                $quarterAssignee->user_id = \App\Models\ServiceProvider::find($quarterAssignee->assignee_id)->user_id ?? null;
                $quarterAssignee->assignment_type = \App\Models\Quarter::AssignmentTypeFortimoEmployees;
                $quarterAssignee->save();
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
        Schema::table('quarter_assifnees', function (Blueprint $table) {
            //
        });
    }
}
