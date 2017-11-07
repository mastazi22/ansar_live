<?php
Route::group(['prefix'=>'recruitment','middleware'=>['auth','manageDatabase'],'namespace'=>'\App\modules\recruitment\Controllers'],function (){


    Route::get('/',['as'=>'recruitment','uses'=>'RecruitmentController@index']);
    Route::get('/educations',['as'=>'educations','uses'=>'RecruitmentController@educationList']);

    //job category route
    Route::resource('category','JobCategoryController',['except'=>['destroy','show']]);
    Route::resource('circular','JobCircularController',['except'=>['destroy','show']]);
    //applicant management
    Route::any('/applicant',['as'=>'recruitment.applicant.index','uses'=>'ApplicantScreeningController@index']);
    Route::get('/applicant/search',['as'=>'recruitment.applicant.search','uses'=>'ApplicantScreeningController@searchApplicant']);
    Route::post('/applicant/search',['as'=>'recruitment.applicant.search_result','uses'=>'ApplicantScreeningController@loadApplicants']);

    Route::get('/applicant/list/{type?}',['as'=>'recruitment.applicant.list','uses'=>'ApplicantScreeningController@applicantListSupport']);
    Route::get('/applicants/list/{circular_id}/{type?}',['as'=>'recruitment.applicants.list','uses'=>'ApplicantScreeningController@applicantList']);
    Route::get('/applicant/mark_as_paid/{type}/{id}',['as'=>'recruitment.applicant.mark_as_paid','uses'=>'ApplicantScreeningController@markAsPaid']);
    Route::post('/applicant/mark_as_paid/{id}',['as'=>'recruitment.applicant.update_as_paid','uses'=>'ApplicantScreeningController@updateAsPaid']);
    Route::any('/applicant/update_as_paid_by_file',['as'=>'recruitment.applicant.update_as_paid_by_file','uses'=>'ApplicantScreeningController@updateAsPaidByFile']);

    //settings
    Route::any('/settings/applicant_quota',['as'=>'recruitment.quota.index','uses'=>'JobApplicantQuotaController@index']);
    Route::any('/settings/applicant_quota/edit',['as'=>'recruitment.quota.edit','uses'=>'JobApplicantQuotaController@edit']);
    Route::post('/settings/applicant_quota/update',['as'=>'recruitment.quota.update','uses'=>'JobApplicantQuotaController@update']);

    //support
    Route::any('/supports/feedback',['as'=>'supports.feedback','uses'=>'SupportController@problemReport']);
    Route::post('/supports/feedback/{id}',['as'=>'supports.feedback.submit','uses'=>'SupportController@replyProblem']);
    Route::post('/supports/feedback/delete/{id}',['as'=>'supports.feedback.delete','uses'=>'SupportController@replyProblemDelete']);

    Route::get('/test',function (){
       $data = \Maatwebsite\Excel\Facades\Excel::load(storage_path('bt.xls'),function (){

       })->get() ;
       $a = collect($data)->pluck('txid');
       return implode('\',\'',$a->toArray());
    });

});