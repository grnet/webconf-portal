<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
	public function participants(){
		return $this->hasMany('App\Participant');
	}

	public function meetings(){
		return $this->hasMany('App\Meeting','room');
	}

	public function invitations(){
		return $this->hasMany('App\Invitation');
	}

	public function belongs(){
		return $this->belongsTo('App\User', 'owner', 'id');
	}
}
