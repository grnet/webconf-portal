<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Meeting extends Model {

	public $table = 'meetings';
	
	public function room(){
		return $this->belongsTo('App\Room');
	}

	public function bbb(){
		return $this->belongsTo('App\Bbb','bbb_server','id');
	}

}
