<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class FixOldReminderUserIdInReqiuestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('requests', function (Blueprint $table) {
            $table->text('reminder_user_ids')->nullable()->change();
        });
        $requests = DB::table('requests')->whereNotNull('reminder_user_ids')->get();
        foreach ($requests as $request) {
            DB::table('requests')->where('id', $request->id)->update([
                'reminder_user_ids' => json_encode([$request->reminder_user_ids])
            ]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

        Schema::table('requests', function (Blueprint $table) {
            $table->integer('reminder_user_ids')->unsigned()->nullable()->change();
        });
    }
}
