<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddOwnerToRecordings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('recordings', function (Blueprint $table) {
		$table->integer('owner')->unsigned();
		$table->foreign('owner')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('recordings', function (Blueprint $table) {
            	$table->dropColumn('owner');
        });
    }
}
