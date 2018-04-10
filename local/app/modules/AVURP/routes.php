<?php
Route::group(['prefix'=>'AVURP','namespace'=>'\App\modules\AVURP\Controllers','middleware'=>['auth','permission','manageDatabase']],function(){
    Route::get('/',['as'=>'AVURP','uses'=>'MainController@index']);
    Route::resource('info','AnsarVDPInfoController');
});