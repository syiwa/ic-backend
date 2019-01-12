<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['prefix' => 'v1'], function(){
	Route::group(['prefix' => 'auth'], function(){
		Route::post('login','API\V1\UserController@login');
		Route::post('logout','API\V1\UserController@logout');
	});

	Route::group(['middleware' => ['auth:api']], function(){
		Route::resource('users','API\V1\UserController', ['only'	=> ['index','store','delete']])->middleware(['role:admin']);
		Route::get('users/{id}','API\V1\UserController@show')->middleware('role:admin|user');
		Route::post('users/{id}','API\V1\UserController@update')->middleware('role:admin|user');
	});

});