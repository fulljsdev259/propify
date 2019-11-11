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
            $table->dropColumn('reminder_user_ids');
        });
        Schema::table('requests', function (Blueprint $table) {
            $table->text('reminder_user_ids', 65535)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

        Schema::table('requests', function (Blueprint $table) {
            $table->dropColumn('reminder_user_ids');
        });
        Schema::table('requests', function (Blueprint $table) {
            $table->integer('reminder_user_ids')->unsigned()->nullable();
        });
    }
}
