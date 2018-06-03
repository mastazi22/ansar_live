<?php

Route::group(['prefix'=>'HRM/api','namespace'=>'\App\modules\HRM\Controllers','middleware'=>'cros'],function(){
    Route::get('image/{id}',['as'=>'HRM.api.profile_image','uses'=>'ApiController@loadProfileImage']);
    Route::group(['prefix'=>'HRM/api','middleware'=>['auth.api','permission','checkUserType']],function(){
        Route::get('divisions',['as'=>'HRM.api.division','uses'=>'ApiController@division']);
        Route::get('units',['as'=>'HRM.api.unit','uses'=>'ApiController@unit']);
        Route::get('thana',['as'=>'HRM.api.thana','uses'=>'ApiController@thana']);
        Route::get('union',['as'=>'HRM.api.union','uses'=>'ApiController@union']);
        Route::get('blood_group',['as'=>'HRM.api.blood_group','uses'=>'ApiController@bloodGroup']);
        Route::get('education_list',['as'=>'HRM.api.education_list','uses'=>'ApiController@educationList']);
        Route::get('main_training',['as'=>'HRM.api.main_training','uses'=>'ApiController@main_training']);
        Route::get('sub_training',['as'=>'HRM.api.sub_training','uses'=>'ApiController@sub_training']);
    });
});