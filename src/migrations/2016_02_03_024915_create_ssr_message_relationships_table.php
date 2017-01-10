<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSsrMessageRelationshipsTable extends Migration
{
   /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ssr_message_relationships', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('message_id')->unsigned();
            $table->foreign('message_id')->references('id')->on('ssr_messages');
            $table->integer('parent_message_id')->unsigned();
            $table->foreign('parent_message_id')->references('id')->on('ssr_messages');
            $table->timestamps();

            $table->unique('message_id', 'parent_message_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('ssr_message_relationships');
    }
}
