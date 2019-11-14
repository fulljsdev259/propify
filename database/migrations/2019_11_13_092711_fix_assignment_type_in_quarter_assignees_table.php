<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class FixAssignmentTypeInQuarterAssigneesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (now() < \Carbon\Carbon::parse('2019-11-12')) {
            update_db_fields(\App\Models\QuarterAssignee::class, 'assignment_type', 4, 3);
            update_db_fields(\App\Models\QuarterAssignee::class, 'assignment_type', 5, 4);
        }
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
