<?php
Route::group(['prefix'=>'SD','middleware'=>['web','auth','permission'] ],function(){
    Route::group(['namespace'=>'\App\modules\SD\Controllers'],function(){
        Route::get('/','SDController@index')->name('SD');
        Route::get('/demandsheet','DemandSheetController@demandSheet')->name('SD.demand_sheet');
        Route::get('/attendancesheet','SDController@attendanceSheet');
        Route::get('/demandconstant','DemandSheetController@demandConstant')->name('SD.demand_constant');
        Route::get('/salarysheet','SDController@salarySheet');
        Route::post('/updateconstant','DemandSheetController@updateConstant');
        Route::get('/test','SDController@test');
        Route::get('/download_demand_sheet/{id}','DemandSheetController@downloadDemandSheet')->where('id','[0-9]+');
        Route::post('/generatedemandsheet','DemandSheetController@generateDemandSheet');
        Route::get('/demandhistory','DemandSheetController@demandHistory')->name('SD.demand_history');
        Route::get('/viewdemandsheet/{id}','DemandSheetController@viewDemandSheet')->where('id','[0-9]+');
        Route::resource('attendance','AttendanceController');
    });
});