<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Invitation extends Model
{
	public $table = "invitations";


	public function room(){
		return $this->belongsTo('App\Room','room');
	}
	public function participant(){
		return $this->belongsTo('App\Participant', 'participant');
	}

}
