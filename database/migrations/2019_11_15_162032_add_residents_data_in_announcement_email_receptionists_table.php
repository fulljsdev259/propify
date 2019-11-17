<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddResidentsDataInAnnouncementEmailReceptionistsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \App\Models\AnnouncementEmailReceptionist::truncate();
        Schema::table('announcement_email_receptionists', function (Blueprint $table) {
            $table->text('residents_data')->nullable()->after('pinboard_id');
            $table->dropColumn('resident_ids');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('announcement_email_receptionists', function (Blueprint $table) {
            $table->string('resident_ids', 191)->nullable();
            $table->dropColumn('residents_data');
        });
    }
}
