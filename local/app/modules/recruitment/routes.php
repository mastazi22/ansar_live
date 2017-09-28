<?php
Route::group(['prefix'=>'recruitment','middleware'=>'manageDatabase','namespace'=>'\App\modules\recruitment\Controllers'],function (){


    Route::get('/',['as'=>'recruitment','uses'=>'RecruitmentController@index']);

    //job category route
    Route::resource('category','JobCategoryController',['except'=>['destroy','show']]);
    Route::resource('circular','JobCircularController',['except'=>['destroy','show']]);


});