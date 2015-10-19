<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableInvitations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invitations', function (Blueprint $table) {
		$table->increments('id');
		$table->string('token');
		$table->integer('room')->unsigned();
		$table->foreign('room')->references('id')->on('rooms');
		$table->integer('participant')->unsigned();
		$table->foreign('participant')->references('id')->on('participants');
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
        Schema::drop('invitations');
    }
}
