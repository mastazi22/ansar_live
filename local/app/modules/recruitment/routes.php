<?php
Route::group(['prefix'=>'recruitment','middleware'=>['auth','manageDatabase'],'namespace'=>'\App\modules\recruitment\Controllers'],function (){


    Route::get('/',['as'=>'recruitment','uses'=>'RecruitmentController@index']);
    Route::get('/educations',['as'=>'educations','uses'=>'RecruitmentController@educationList']);

    //job category route
    Route::resource('category','JobCategoryController',['except'=>['destroy','show']]);
    Route::resource('circular','JobCircularController',['except'=>['destroy','show']]);
    //applicant management
    Route::any('/applicant',['as'=>'recruitment.applicant.index','uses'=>'ApplicantScreeningController@index']);
    Route::any('/applicant/search',['as'=>'recruitment.applicant.search','uses'=>'ApplicantScreeningController@searchApplicant']);
    //settings
    Route::any('/settings/applicant_quota',['as'=>'recruitment.quota.index','uses'=>'JobApplicantQuotaController@index']);
    Route::any('/settings/applicant_quota/edit',['as'=>'recruitment.quota.edit','uses'=>'JobApplicantQuotaController@edit']);
    Route::post('/settings/applicant_quota/update',['as'=>'recruitment.quota.update','uses'=>'JobApplicantQuotaController@update']);



});