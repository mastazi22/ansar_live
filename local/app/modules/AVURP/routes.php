<?php
Route::group(['prefix'=>'AVURP','namespace'=>'\App\modules\AVURP\Controllers','middleware'=>['auth']],function(){
    Route::resource('info','AnsarVDPInfoController');
});