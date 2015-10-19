<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
 */

Route::get('/', 'mainController@index');
Route::get('/help', 'mainController@help');

//login and logout support routes
Route::get('saml2/login', ['middleware' => 'auth', 'uses' => 'mainController@login']);
Route::get('saml2/logout','mainController@logout');

Route::post('/room/invite/{id}',['middleware' => 'auth', 'uses' => 'roomController@invite']);


Route::get('/join/{id}', ['middleware' => 'auth', 'uses' => 'bbbController@join']);

Route::get('/room/own', ['middleware' => 'auth' , 'uses' =>'roomController@own']);
Route::get('/room/invited', ['middleware' => 'auth' , 'uses' =>'roomController@invited']);
Route::get('/room/public', ['middleware' => 'auth' , 'uses' =>'roomController@publicr']);
Route::get('/room/create', ['middleware' => 'auth' , 'uses' =>'roomController@create']);
Route::get('/room/show/{id}', ['middleware' => 'auth' , 'uses' =>'roomController@show']);
Route::post('/room/store', ['middleware' => 'auth' , 'uses' =>'roomController@store']);
Route::get('/room/edit/{id}', ['middleware' => 'auth' , 'uses' =>'roomController@edit']);
Route::put('/room/update/{id}', ['middleware' => 'auth' , 'uses' =>'roomController@update']);
Route::delete('/room/destroy/{id}', ['middleware' => 'auth' , 'uses' =>'roomController@destroy']);
Route::get('/room/running/{id}', ['middleware' => 'auth' , 'uses' =>'bbbController@runningAjax']);

Route::put('/recording/keep/{id}', ['middleware' => 'auth' , 'uses' =>'recordingsController@keep']);
Route::put('/recording/publish/{id}', ['middleware' => 'auth' , 'uses' =>'recordingsController@publish']);
Route::delete('/recording/delete/{id}', ['middleware' => 'auth' , 'uses' =>'recordingsController@delete']);
Route::post('/recording/share', ['middleware' => 'auth' , 'uses' =>'recordingsController@share']);

//room access pin, this one is without authentication
Route::get('/room/withPin', ['uses' =>'roomController@withPin']);
Route::post('/join_pin', ['uses' => 'bbbController@join_pin']);

//join external without authentication
Route::get('/join_external/{token}', ['uses' => 'bbbController@join_external']);
