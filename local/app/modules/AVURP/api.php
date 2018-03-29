<?php

Route::group(['prefix'=>'AVURP/api','namespace'=>'\App\modules\AVURP\Controllers','middleware'=>['auth.api']],function(){
   Route::post('test',function (){
       return ['test'];
   }) ;
});