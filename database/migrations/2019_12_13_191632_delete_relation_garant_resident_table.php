<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DeleteRelationGarantResidentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('relation_garant_resident');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::create('relation_garant_resident', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('relation_id');
            $table->unsignedInteger('resident_id');
        });
    }
}
