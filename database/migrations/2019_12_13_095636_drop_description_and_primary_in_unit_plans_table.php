<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropDescriptionAndPrimaryInUnitPlansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('unit_plans', function (Blueprint $table) {
            $table->dropColumn('description');
            $table->dropColumn('primary');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('unit_plans', function (Blueprint $table) {
            $table->string('description')->after('name');
            $table->boolean('primary')->after('description')->default(false);
        });
    }
}
