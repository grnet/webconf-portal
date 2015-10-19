<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRecordingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
	Schema::drop('rec_keep');
        Schema::create('recordings', function (Blueprint $table){
		$table->increments('id');
		$table->string('rid');
		$table->index('rid');
		$table->integer('bbb_server_id')->unsigned();
		$table->foreign('bbb_server_id')->references('id')->on('bbb_servers')->onDelete('cascade');;
		$table->tinyInteger('published');
	});
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::create('rec_keep', function (Blueprint $table){
                        $table->increments('id');
                        $table->string('rid');
        });
        Schema::drop('recordings');
        
    }
}
