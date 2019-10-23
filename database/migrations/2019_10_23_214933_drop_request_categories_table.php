<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropRequestCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('request_categories');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::create('request_categories', function(Blueprint $table)
        {
            $table->increments('id');
            $table->integer('parent_id')->unsigned()->nullable()->index('service_request_categories_parent_id_foreign');
            $table->string('name', 191);
            $table->string('name_de', 191);
            $table->string('name_fr', 191);
            $table->string('name_it', 191);
            $table->boolean('room')->nullable()->default(0);
            $table->boolean('location')->nullable()->default(0);
            $table->text('description', 65535)->nullable();
            $table->boolean('has_qualifications')->default(0);
            $table->boolean('acquisition')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('parent_id', 'service_request_categories_parent_id_foreign')->references('id')->on('request_categories')->onUpdate('RESTRICT')->onDelete('RESTRICT');
        });
    }
}
