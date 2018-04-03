<?php

Route::group(['prefix'=>'AVURP/api','namespace'=>'\App\modules\AVURP\Controllers','middleware'=>['auth.api']],function(){
   Route::get('info/all',['as'=>'AVURP.api.index','uses'=>'ApiController@index']) ;
   Route::get('info/show/{id}',['as'=>'AVURP.api.show','uses'=>'ApiController@show']) ;
   Route::get('info/edit/{id}',['as'=>'AVURP.api.edit','uses'=>'ApiController@edit']) ;
   Route::post('info/store',['as'=>'AVURP.api.store','uses'=>'ApiController@store']) ;
   Route::post('info/update/{id}',['as'=>'AVURP.api.update','uses'=>'ApiController@update']) ;
});