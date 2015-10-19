<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Bbb extends Model {

	public $table = 'bbb_servers';
	
	public $timestamps = false;
	
	public function meetings(){
		return $this->hasMany('App\Meeting','bbb_server');
	}

	public function recordings(){
                return $this->hasMany('App\Recordings', 'bbb_server_id');
        }

}
