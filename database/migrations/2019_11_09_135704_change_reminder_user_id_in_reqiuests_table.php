<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeReminderUserIdInReqiuestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::table('requests', function (Blueprint $table) {
            $table->dropForeign('service_requests_reminder_user_id_foreign');
            $table->renameColumn('reminder_user_id', 'reminder_user_ids');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::disableForeignKeyConstraints();
        Schema::table('requests', function (Blueprint $table) {
            $table->renameColumn('reminder_user_ids', 'reminder_user_id');
            $table->foreign('reminder_user_id', 'service_requests_reminder_user_id_foreign')->references('id')->on('users')->onUpdate('RESTRICT')->onDelete('RESTRICT');
        });
        Schema::enableForeignKeyConstraints();
    }
}
