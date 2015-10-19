<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateParticipantsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('participants', function (Blueprint $table){
		$table->increments('id');
		$table->string('mail');
		$table->integer('room_id')->unsigned();
		$table->foreign('room_id')->references('id')->on('rooms')->onDelete('cascade');
		$table->tinyInteger('moderator');
	});
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('participants');
    }
}
