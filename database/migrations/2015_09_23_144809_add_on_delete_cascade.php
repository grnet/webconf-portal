<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddOnDeleteCascade extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('meetings', function (Blueprint $table) {
		$table->dropForeign('meetings_room_foreign');
		$table->foreign('room')->references('id')->on('rooms')->onDelete('cascade');

            //
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('meetings', function (Blueprint $table) {
		$table->dropForeign('meetings_room_foreign');
		$table->foreign('room')->references('id')->on('rooms');

        });
    }
}
