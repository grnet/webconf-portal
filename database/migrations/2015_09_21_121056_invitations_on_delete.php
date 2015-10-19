<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class InvitationsOnDelete extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('invitations', function (Blueprint $table) {
		$table->dropForeign('invitations_room_foreign');
		$table->dropForeign('invitations_participant_foreign');
		$table->foreign('room')->references('id')->on('rooms')->onDelete('cascade');
		$table->foreign('participant')->references('id')->on('participants')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
	Schema::table('invitations', function (Blueprint $table) {
       	        $table->dropForeign('invitations_room_foreign');
		$table->dropForeign('invitations_participant_foreign');

		$table->foreign('room')->references('id')->on('rooms');
		$table->foreign('participant')->references('id')->on('participants');

        });
    }
}
