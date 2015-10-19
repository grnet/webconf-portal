<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRoomsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rooms', function (Blueprint $table){
		$table->increments('id');
		$table->string('name');
		$table->string('bbb_meeting_id')->unique();
		$table->string('mod_pass');
		$table->string('att_pass');
		$table->integer('owner')->unsigned();
		$table->foreign('owner')->references('id')->on('users');
		$table->tinyInteger('public');
		$table->tinyInteger('recording');
		$table->string('access_pin');
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
        Schema::drop('rooms');
    }
}
