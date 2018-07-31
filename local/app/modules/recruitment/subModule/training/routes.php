<?php

    Route::group(["prefix"=>"recruitment.training",'namespace'=>'\App\modules\recruitment\subModule\training\Controllers'],function (){

        Route::get("/",['as'=>'recruitment.training','uses'=>'Training@index']);
        Route::resource('category','TrainingCategoryController');
        Route::resource('center','TrainingCenterController');
        Route::resource('courses','TrainingCourseController');
        Route::resource('session','TrainingSessionController');

    });