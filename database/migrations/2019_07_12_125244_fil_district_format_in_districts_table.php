<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class FilDistrictFormatInDistrictsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        \App\Models\Quarter::get(['id', 'created_at'])->each(function ($district) {
            $district->quarter_format  = $district->getUniqueIDFormat($district->id, $district->created_at);
            $district->save();
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
