<?php
Route::group(['prefix'=>'SD','middleware'=>['web','auth','permission'] ],function(){
    Route::group(['namespace'=>'\App\modules\SD\Controllers'],function(){
        Route::get('/','SDController@index');
        Route::get('/demandsheet','DemandSheetController@demandSheet');
        Route::get('/attendancesheet','SDController@attendanceSheet');
        Route::get('/demandconstant','DemandSheetController@demandConstant');
        Route::get('/salarysheet','SDController@salarySheet');
        Route::post('/updateconstant','DemandSheetController@updateConstant');
        Route::get('/test','SDController@test');
        Route::get('/download_demand_sheet/{id}','DemandSheetController@downloadDemandSheet')->where('id','[0-9]+');
        Route::post('/generatedemandsheet','DemandSheetController@generateDemandSheet');
        Route::get('/demandhistory','DemandSheetController@demandHistory');
        Route::get('/viewdemandsheet/{id}','DemandSheetController@viewDemandSheet')->where('id','[0-9]+');
    });
});