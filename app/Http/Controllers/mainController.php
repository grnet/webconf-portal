<?php

namespace App\Http\Controllers;

//tsipizic for logout
require_once('/usr/share/simplesamlphp/lib/_autoload.php');


use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use DB;
use Auth;
use Redirect;

use App\Bbb;

class mainController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        //
	return view('main.index');
    }

    public function login(){
	//auth middleware takes care of login we just redirect here
	return Redirect::Action('roomController@own');
    }


    public function help(){
	    $bbb = Bbb::orderByRaw("RAND()")->first();
	    $bbb_check_url = parse_url($bbb->url, PHP_URL_SCHEME) . '://' . parse_url($bbb->url, PHP_URL_HOST) . '/check';
	    return view('main.help', ['bbb_check_url' => $bbb_check_url]);
    }

    public function logout(){
	//check for application session and invalidate
	if(Auth::check()){
		Auth::logout();
	}
	//check for sso session and invalidate
	$as = new \SimpleSAML_Auth_Simple('default-sp');
	if($as->isAuthenticated()){
		$as->logout();
	}
	//redirect to home
	return Redirect::Action('mainController@index');
    }

}
