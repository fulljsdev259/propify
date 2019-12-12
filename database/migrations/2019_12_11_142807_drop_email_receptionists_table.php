<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropEmailReceptionistsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('email_receptionists');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::create('email_receptionists', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('property_manager_id');
            $table->smallInteger('category');
            $table->unsignedInteger('model_id');
            $table->string('model_type');
            $table->timestamps();
        });
    }
}
