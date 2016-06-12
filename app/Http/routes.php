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
Route::get('/log_in', 'UserController@login');
Route::post('/check_login', 'UserController@handleLogin');
Route::get('/', function () {
    return view('template.index');
});
Route::get('image', 'UserController@getImage');
Route::get('/logout', 'UserController@logout');
Route::get('/view_profile/{id}', 'UserController@viewProfile');
