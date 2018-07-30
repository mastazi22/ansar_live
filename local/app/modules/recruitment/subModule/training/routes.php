<?php

    Route::group(["prefix"=>"recruitment.training",'namespace'=>'\App\modules\recruitment\subModule\training\Controllers'],function (){

        Route::get("/",['as'=>'recruitment.training.index','uses'=>'Training@index']);

    });