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
use PhpParser\Parser;

Route::get('/log_in', ['as'=>'login','uses'=>'UserController@login']);
Route::get('/forget_password_request', ['as'=>'forget_password_request','uses'=>'UserController@forgetPasswordRequest']);
Route::post('/forget_password_request_handle', ['as'=>'forget_password_request_handle','uses'=>'UserController@handleForgetRequest']);
Route::post('/check_login', 'UserController@handleLogin');
Route::group(['middleware' => 'auth'], function () {
    Route::get('/', ['as'=>'home','uses'=>function () {
        return view('template.index');
    }]);
    Route::get('/all_notification', function () {
        return view('all_notification');
    });
 Route::get('/change_password/{user}', ['as'=>'change_password','uses'=>'UserController@changeForgetPassword']);
    Route::get('/remove_request/{user}', 'UserController@removePasswordRequest');
 Route::post('/handle_change_password', ['as'=>'handle_change_password','uses'=>'UserController@handleChangeForgetPassword']);

    Route::get('image', ['as'=>'profile_image','uses'=>'UserController@getImage']);
    Route::get('sign_image/{id}', ['as'=>'sign_image','uses'=>'UserController@getSingImage']);
    Route::get('thumb_image/{id}', ['as'=>'thumb_image','uses'=>'UserController@getThumbImage']);
    Route::get('/logout', 'UserController@logout');

    //user route

    Route::get('/view_profile/{id}', 'UserController@viewProfile');
    Route::post('/update_profile', 'UserController@updateProfile');
    Route::get('/action_log/{id?}', 'UserController@viewActionLog');
    Route::post('/change_user_name', ['as' => 'edit_user_name', 'uses' => 'UserController@changeUserName']);
    Route::post('/change_user_password', ['as' => 'edit_user_password', 'uses' => 'UserController@changeUserPassword']);
    Route::post('change_user_image', 'UserController@changeUserImage');
    Route::post('/verify_memorandum_id', 'UserController@verifyMemorandumId');
    Route::get('user_data','UserController@getUserData');


   Route::group(['middleware'=>'admin'],function(){
       Route::get('/user_search', ['as' => 'user_search', 'uses' => 'UserController@userSearch']);
       Route::get('/all_user', ['as' => 'all_user', 'uses' => 'UserController@getAllUser']);
       Route::post('update_permission/{id}', 'UserController@updatePermission');
       Route::post('handle_registration', 'UserController@handleRegister');
       Route::get('/user_management', ['as' => 'view_user_list', 'uses' => 'UserController@userManagement']);
       Route::get('/edit_user/{id}', ['as' => 'edit_user', 'uses' => 'UserController@editUser']);
       Route::post('/block_user', ['as' => 'block_user', 'uses' => 'UserController@blockUser']);
       Route::post('/unblock_user', ['as' => 'unblock_user', 'uses' => 'UserController@unBlockUser']);
       Route::get('/user_registration', ['as' => 'create_user', 'uses' => 'UserController@userRegistration']);
       Route::get('/edit_user_permission/{id}', ['as' => 'edit_user_permission', 'uses' => 'UserController@editUserPermission']);
   });
    Route::get('test',function (){
       $pdf = \Barryvdh\Snappy\Facades\SnappyPdf::loadView('welcome');
       return $pdf->download();
    });
    //end user route
});
