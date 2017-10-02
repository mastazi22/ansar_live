<?php
Route::group(['prefix'=>'recruitment','middleware'=>['auth','manageDatabase'],'namespace'=>'\App\modules\recruitment\Controllers'],function (){


    Route::get('/',['as'=>'recruitment','uses'=>'RecruitmentController@index']);
    Route::get('/educations',['as'=>'educations','uses'=>'RecruitmentController@educationList']);

    //job category route
    Route::resource('category','JobCategoryController',['except'=>['destroy','show']]);
    Route::resource('circular','JobCircularController',['except'=>['destroy','show']]);


});