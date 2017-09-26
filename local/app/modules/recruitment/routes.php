<?php
Route::group(['prefix'=>'recruitment','middleware'=>'manageDatabase','namespace'=>'\App\modules\recruitment\Controllers'],function (){


    Route::get('/',function (){
        return "ANSAR recruitment";
    });


});