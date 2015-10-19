<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Recording extends Model
{
	public $table = 'recordings';
	
	public $timestamps = false;

        public function belongs(){
                return $this->belongsTo('App\Bbb', 'bbb_server_id', 'id');
        }

}
