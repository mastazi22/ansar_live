<?php

Route::group(['prefix'=>'HRM/api','namespace'=>'\App\modules\HRM\Controllers','middleware'=>['permission','checkUserType','auth.api']],function(){
    Route::get('divisions',['as'=>'HRM.api.division','uses'=>'ApiController@division']);
    Route::get('units',['as'=>'HRM.api.unit','uses'=>'ApiController@division']);
    Route::get('thana',['as'=>'HRM.api.thana','uses'=>'ApiController@division']);
    Route::get('union',['as'=>'HRM.api.union','uses'=>'ApiController@division']);
    Route::get('main_training',['as'=>'HRM.api.main_training','uses'=>'ApiController@main_training']);
    Route::get('sub_training',['as'=>'HRM.api.sub_training','uses'=>'ApiController@sub_training']);
});