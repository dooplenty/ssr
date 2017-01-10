<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSsrMessageAttributesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ssr_message_attributes', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('message_id')->unsigned();
            $table->foreign('message_id')->references('id')->on('ssr_messages');
            $table->string('to');
            $table->string('from');
            $table->string('subject');
            $table->dateTime('sent_date');
            $table->string('in_reply_to');
            $table->integer('size');
            $table->boolean('has_attachment');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('ssr_message_attributes');
    }
}
