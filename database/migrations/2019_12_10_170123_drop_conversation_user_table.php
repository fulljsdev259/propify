<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropConversationUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::drop('conversation_user');
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::create('conversation_user', function(Blueprint $table)
        {
            $table->integer('conversation_id')->unsigned()->index('conversation_user_conversation_id_foreign');
            $table->integer('user_id')->unsigned()->index('conversation_user_user_id_foreign');
        });
    }

}
