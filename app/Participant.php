<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Participant extends Model
{

	public $table = 'participants';
	
	public $timestamps = false;
	
	protected $fillable = [
		'email',
		'moderator'
	];

	public function room(){
		return $this->belongsTo('App\Room');
	}

	public function invitations(){
		return $this->hasMany('App\Invitation');
	}

}
