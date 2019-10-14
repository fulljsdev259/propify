<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MoveAdminUsersInPm extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \App\Models\User::withRole('administrator')->get()->each(function ($user) {
            $nameParts = explode(' ' ,$user->name);
            $firstName = array_shift($nameParts);
            $lastName = implode(' ',$nameParts);
            $manager = \App\Models\PropertyManager::where('user_id', $user->id)->first();
            $manager = $manager ?? \App\Models\PropertyManager::create([
                'first_name'  => $firstName,
                'lsat_name' => $lastName,
                'title' => $user->title,
                'type' => \App\Models\PropertyManager::TypeAdministrator,
                'user_id' => $user->id,
            ]);
            \App\Models\BuildingAssignee::where('assignee_type', get_morph_type_of(\App\Models\User::class))
                ->where('assignee_id', $user->id)
                ->update([
                    'assignee_type' => get_morph_type_of(\App\Models\PropertyManager::class),
                    'assignee_id' => $manager->id,
                ]);
            \App\Models\QuarterAssignee::where('assignee_type', get_morph_type_of(\App\Models\User::class))
                ->where('assignee_id', $user->id)
                ->update([
                    'assignee_type' => get_morph_type_of(\App\Models\PropertyManager::class),
                    'assignee_id' => $manager->id,
                ]);
            \App\Models\RequestAssignee::where('assignee_type', get_morph_type_of(\App\Models\User::class))
                ->where('assignee_id', $user->id)
                ->update([
                    'assignee_type' => get_morph_type_of(\App\Models\PropertyManager::class),
                    'assignee_id' => $manager->id,
                ]);

        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

    }
}
