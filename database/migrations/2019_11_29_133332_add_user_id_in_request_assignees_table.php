<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddUserIdInRequestAssigneesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('request_assignees', function (Blueprint $table) {
            $table->unsignedInteger('user_id')->after('request_id')->nullable();
        });

        \App\Models\RequestAssignee::get()->each(function (\App\Models\RequestAssignee $quarterAssignee) {
            if ($quarterAssignee->assignee_type == get_morph_type_of(\App\Models\PropertyManager::class)) {
                $quarterAssignee->user_id = \App\Models\PropertyManager::find($quarterAssignee->assignee_id)->user_id ?? null;
                $quarterAssignee->save();
            }
            if ($quarterAssignee->assignee_type == get_morph_type_of(\App\Models\ServiceProvider::class)) {
                $quarterAssignee->user_id = \App\Models\ServiceProvider::find($quarterAssignee->assignee_id)->user_id ?? null;
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
        Schema::table('request_assignees', function (Blueprint $table) {
            $table->dropColumn('user_id');
        });
    }
}
