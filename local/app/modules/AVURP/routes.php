<?php
Route::group(['prefix'=>'AVURP','namespace'=>'\App\modules\AVURP\Controllers','middleware'=>['manageDatabase','auth']],function(){
    Route::get('/',['as'=>'AVURP','uses'=>'MainController@index']);
    Route::resource('info','AnsarVDPInfoController');
});