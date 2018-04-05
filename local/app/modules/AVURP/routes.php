<?php
Route::group(['prefix'=>'AVURP','namespace'=>'\App\modules\AVURP\Controllers','middleware'=>['permission','manageDatabase','auth']],function(){
    Route::get('/',['as'=>'AVURP','uses'=>'MainController@index']);
    Route::resource('info','AnsarVDPInfoController');
});