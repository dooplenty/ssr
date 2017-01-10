<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSsrMessageContactsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ssr_message_contacts', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('message_id')->unsigned()->unique();
            $table->foreign('message_id')->references('id')->on('ssr_messages');
            $table->integer('contact_id')->unsigned()->unique();
            $table->foreign('contact_id')->references('id')->on('ssr_contacts');
            $table->boolean('is_owner');
            $table->boolean('is_recipient');
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
        Schema::drop('ssr_message_contacts');
    }
}
