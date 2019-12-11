<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropConversationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::drop('conversations');
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::create('conversations', function(Blueprint $table)
        {
            $table->increments('id');
            $table->string('conversationable_type', 191);
            $table->bigInteger('conversationable_id')->unsigned();
            $table->timestamps();
            $table->softDeletes();
            $table->index(['conversationable_type','conversationable_id']);
        });
    }
}
