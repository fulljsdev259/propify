<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddUrlColInQuartersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('quarters', function (Blueprint $table) {
            $table->string('url')->nullable()->after('description');
            $table->text('types')->after('type')->nullable();
            $table->dropColumn('assignment_type');
        });

        \App\Models\Quarter::get()->each(function (\App\Models\Quarter $quarter) {
            $quarter->types = [$quarter->type];
            $quarter->save();
        });

        Schema::table('quarters', function (Blueprint $table) {
            $table->dropColumn( 'type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('quarters', function (Blueprint $table) {
            $table->dropColumn('url', 'types');
            $table->smallInteger('type')->after('address_id')->default(1);
            $table->tinyInteger('assignment_type')->after('type')->nullable();
        });
    }
}
