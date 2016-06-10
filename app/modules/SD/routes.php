<?php
Route::group(['prefix'=>'SD','middleware'=>['web','auth','permission'] ],function(){
    Route::group(['namespace'=>'\App\modules\SD\Controllers'],function(){
        Route::get('/','SDController@index');
        Route::get('/demandsheet','SDController@demandSheet');
        Route::get('/attendancesheet','SDController@attendanceSheet');
        Route::get('/demandconstant','SDController@demandConstant');
        Route::get('/salarysheet','SDController@salarySheet');
        Route::post('/updateconstant','SDController@updateConstant');
        Route::get('test','SDController@test');
    });
});