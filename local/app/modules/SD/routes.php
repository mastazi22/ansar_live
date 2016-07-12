<?php
Route::group(['prefix'=>'SD','middleware'=>['web','auth','permission'] ],function(){
    Route::group(['namespace'=>'\App\modules\SD\Controllers'],function(){
        Route::get('/','SDController@index');
        Route::get('/demandsheet','SDController@demandSheet');
        Route::get('/attendancesheet','SDController@attendanceSheet');
        Route::get('/demandconstant','SDController@demandConstant');
        Route::get('/salarysheet','SDController@salarySheet');
        Route::post('/updateconstant','SDController@updateConstant');
        Route::get('/test','SDController@test');
        Route::get('/download_demand_sheet/{id}','SDController@downloadDemandSheet')->where('id','[0-9]+');
        Route::post('/generatedemandsheet','SDController@generateDemandSheet');
        Route::get('/demandhistory','SDController@demandHistory');
        Route::get('/viewdemandsheet/{id}','SDController@viewDemandSheet')->where('id','[0-9]+');
    });
});