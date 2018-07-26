<?php
Route::group(['prefix'=>'AVURP','namespace'=>'\App\modules\AVURP\Controllers','middleware'=>['auth','permission','manageDatabase','checkUserType']],function(){
    Route::get('/',['as'=>'AVURP','uses'=>'MainController@index']);
    Route::post('info/verify/{id}',['as'=>'AVURP.info.verify','uses'=>'AnsarVDPInfoController@verifyVDP']);
    Route::post('info/approve/{id}',['as'=>'AVURP.info.approve','uses'=>'AnsarVDPInfoController@approveVDP']);
    Route::post('info/verify_approve/{id}',['as'=>'AVURP.info.verify_approve','uses'=>'AnsarVDPInfoController@verifyAndApproveVDP']);
    Route::get('info/image/{id}',['as'=>'AVURP.info.image','uses'=>'AnsarVDPInfoController@loadImage']);
    Route::get('info/import',['as'=>'AVURP.info.import','uses'=>'AnsarVDPInfoController@import']);
    Route::get('info/import/download/{file_name}',['as'=>'AVURP.info.import.download','uses'=>'AnsarVDPInfoController@downloadFile']);
    Route::post('info/import',['as'=>'AVURP.info.import_upload','uses'=>'AnsarVDPInfoController@processImportedFile']);
    Route::resource('info','AnsarVDPInfoController');
    Route::resource('kpi','KpiInfoController');
    Route::resource('offer_info','OfferInfoController');
    Route::get("test", function () {
        return "বাঘবেড়"  == "বাঘবেড়"?"true":"false";
    });
});