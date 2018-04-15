<?php
Route::group(['prefix'=>'AVURP','namespace'=>'\App\modules\AVURP\Controllers','middleware'=>['auth','permission','manageDatabase']],function(){
    Route::get('/',['as'=>'AVURP','uses'=>'MainController@index']);
    Route::post('info/verify/{id}',['as'=>'AVURP.info.verify','uses'=>'AnsarVDPInfoController@verifyVDP']);
    Route::post('info/approve/{id}',['as'=>'AVURP.info.approve','uses'=>'AnsarVDPInfoController@approveVDP']);
    Route::get('info/image/{id}',['as'=>'AVURP.info.image','uses'=>'AnsarVDPInfoController@loadImage']);
    Route::resource('info','AnsarVDPInfoController');
});