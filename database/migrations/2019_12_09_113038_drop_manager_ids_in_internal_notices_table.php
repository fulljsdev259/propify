<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropManagerIdsInInternalNoticesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('internal_notices', function (Blueprint $table) {
            $table->dropColumn('manager_ids');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('internal_notices', function (Blueprint $table) {
            $table->text('manager_ids')->after('user_id')->nullable();
        });
    }
}
