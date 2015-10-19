<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMeetingsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('meetings', function (Blueprint $table){
		$table->increments('id');
		$table->integer('room')->unsigned();
		$table->foreign('room')->references('id')->on('rooms');
		$table->integer('bbb_server')->unsigned();
		$table->foreign('bbb_server')->references('id')->on('bbb_servers');
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
		Schema::drop('meetings');
	}

}
