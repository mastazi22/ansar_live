<?php
Route::group(['prefix'=>'AVURP','namespace'=>'\App\modules\AVURP\Controllers','middleware'=>['manageDatabase','auth']],function(){
    Route::resource('info','AnsarVDPInfoController');
});