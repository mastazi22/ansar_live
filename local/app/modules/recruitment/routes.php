<?php
Route::group(['prefix'=>'recruitment','middleware'=>['recruitment'],'namespace'=>'\App\modules\recruitment\Controllers'],function (){


   Route::group(['middleware'=>['auth','manageDatabase','checkUserType','permission']],function (){
       Route::get('/',['as'=>'recruitment','uses'=>'RecruitmentController@index']);
       Route::get('/educations',['as'=>'educations','uses'=>'RecruitmentController@educationList']);

       //job category route
       Route::resource('category','JobCategoryController',['except'=>['destroy','show']]);
       Route::resource('circular','JobCircularController',['except'=>['destroy','show']]);
       Route::get('circular/constraint/{id}',['as'=>'recruitment.circular.constraint','uses'=>'JobCircularController@constraint']);
       Route::resource('marks','JobApplicantMarksController',['except'=>['show']]);
       //applicant management
       Route::any('/applicant',['as'=>'recruitment.applicant.index','uses'=>'ApplicantScreeningController@index']);
       Route::any('/applicant/sms',['as'=>'recruitment.applicant.sms','uses'=>'SMSController@index']);
       Route::post('/applicant/sms',['as'=>'recruitment.applicant.sms_send','uses'=>'SMSController@sendSMSToApplicant']);
       Route::get('/applicant/detail/view/{id}',['as'=>'recruitment.applicant.detail_view','uses'=>'ApplicantScreeningController@applicantDetailView']);
       Route::get('/applicant/detail/{id}',['as'=>'recruitment.applicant.detail','uses'=>'ApplicantScreeningController@getApplicantData']);
       Route::post('/applicant/update',['as'=>'recruitment.applicant.update','uses'=>'ApplicantScreeningController@updateApplicantData']);
       Route::post('/applicant/confirm_selection_or_rejection',['as'=>'recruitment.applicant.confirm_selection_or_rejection','uses'=>'ApplicantScreeningController@confirmSelectionOrRejection']);
       Route::post('/applicant/confirm_accepted',['as'=>'recruitment.applicant.confirm_accepted','uses'=>'ApplicantScreeningController@confirmAccepted']);
       Route::post('/applicant/confirm_accepted_bn_candidate',['as'=>'recruitment.applicant.confirm_accepted_if_bn_candidate','uses'=>'ApplicantScreeningController@confirmAcceptedIfBncandidate']);
       Route::post('/applicant/confirm_accepted_special_candidate',['as'=>'recruitment.applicant.confirm_accepted_special_candidate','uses'=>'ApplicantScreeningController@confirmAcceptedIfSpecialCandidate']);
       Route::get('/applicant/search',['as'=>'recruitment.applicant.search','uses'=>'ApplicantScreeningController@searchApplicant']);
       Route::post('/applicant/search',['as'=>'recruitment.applicant.search_result','uses'=>'ApplicantScreeningController@loadApplicants']);
       Route::any('/applicant/info',['as'=>'recruitment.applicant.info','uses'=>'ApplicantScreeningController@loadApplicantsByStatus']);
       Route::any('/applicant/revert',['as'=>'recruitment.applicant.revert','uses'=>'ApplicantScreeningController@loadApplicantsForRevert']);
       Route::post('/applicant/revert_status',['as'=>'recruitment.applicant.revert_status','uses'=>'ApplicantScreeningController@revertApplicantStatus']);
       Route::post('/applicant/detail/selected_applicant',['as'=>'recruitment.applicant.selected_applicant','uses'=>'ApplicantScreeningController@loadSelectedApplicant']);
       Route::get('/applicant/editfield',['as'=>'recruitment.applicant.editfield','uses'=>'ApplicantScreeningController@applicantEditField']);
       Route::post('/applicant/editfield',['as'=>'recruitment.applicant.editfieldstore','uses'=>'ApplicantScreeningController@saveApplicantEditField']);
       Route::get('/applicant/geteditfield',['as'=>'recruitment.applicant.getfieldstore','uses'=>'ApplicantScreeningController@loadApplicantEditField']);
       Route::get('/applicant/final_list',['as'=>'recruitment.applicant.final_list','uses'=>'ApplicantScreeningController@acceptedApplicantView']);
       Route::post('/applicant/final_list/load',['as'=>'recruitment.applicant.final_list_load','uses'=>'ApplicantScreeningController@loadApplicantByQuota']);

       Route::get('/applicant/list/{type?}',['as'=>'recruitment.applicant.list','uses'=>'ApplicantScreeningController@applicantListSupport']);
       Route::get('/applicants/list/{circular_id}/{type?}',['as'=>'recruitment.applicants.list','uses'=>'ApplicantScreeningController@applicantList']);
       Route::get('/applicant/mark_as_paid/{type}/{id}/{circular_id}',['as'=>'recruitment.applicant.mark_as_paid','uses'=>'ApplicantScreeningController@markAsPaid']);
       Route::post('/applicant/mark_as_paid/{id}',['as'=>'recruitment.applicant.update_as_paid','uses'=>'ApplicantScreeningController@updateAsPaid']);
       Route::any('/applicant/update_as_paid_by_file',['as'=>'recruitment.applicant.update_as_paid_by_file','uses'=>'ApplicantScreeningController@updateAsPaidByFile']);
       Route::any('/applicant/move_to_hrm',['as'=>'recruitment.move_to_hrm','uses'=>'ApplicantScreeningController@moveApplicantToHRM']);
       Route::any('/applicant/edit_for_hrm',['as'=>'recruitment.edit_for_hrm','uses'=>'ApplicantScreeningController@editApplicantForHRM']);
       Route::any('/applicant/applicant_edit_for_hrm/{type}/{id}',['as'=>'recruitment.applicant_edit_for_hrm','uses'=>'ApplicantScreeningController@applicantEditForHRM']);
       Route::post('/applicant/store_hrm_detail',['as'=>'recruitment.store_hrm_detail','uses'=>'ApplicantScreeningController@storeApplicantHRmDetail']);


       Route::any('/applicant/hrm',['as'=>'recruitment.hrm.index','uses'=>'ApplicantHRMController@index']);
       Route::get('/applicant/hrm/{type}/{circular_id}/{id}',['as'=>'recruitment.hrm.view_download','uses'=>'ApplicantHRMController@applicantEditForHRM']);
       Route::post('/applicant/hrm/move/{id}',['as'=>'recruitment.hrm.move','uses'=>'ApplicantHRMController@moveApplicantToHRM']);

       Route::any('/applicant/hrm/card_print',['as'=>'recruitment.hrm.card_print','uses'=>'ApplicantHRMController@print_card']);

       //settings
       //quota
       Route::any('/settings/applicant_quota',['as'=>'recruitment.quota.index','uses'=>'JobApplicantQuotaController@index']);
       Route::any('/settings/applicant_quota/edit',['as'=>'recruitment.quota.edit','uses'=>'JobApplicantQuotaController@edit']);
       Route::post('/settings/applicant_quota/update',['as'=>'recruitment.quota.update','uses'=>'JobApplicantQuotaController@update']);
       //point table
       Route::resource('marks_rules','ApplicantMarksRuleController');

       //support
       Route::any('/supports/feedback',['as'=>'supports.feedback','uses'=>'SupportController@problemReport']);
       Route::post('/supports/feedback/{id}',['as'=>'supports.feedback.submit','uses'=>'SupportController@replyProblem']);
       Route::post('/supports/feedback/delete/{id}',['as'=>'supports.feedback.delete','uses'=>'SupportController@replyProblemDelete']);


       Route::any('/reports/applicat_status',['as'=>'report.applicants.status','uses'=>'ApplicantReportsController@applicantStatusReport']);
       Route::any('/reports/applicat_accepted_list',['as'=>'report.applicants.applicat_accepted_list','uses'=>'ApplicantReportsController@applicantAcceptedListReport']);
       Route::any('/reports/applicat_marks_list',['as'=>'report.applicants.applicat_marks_list','uses'=>'ApplicantReportsController@applicantMarksReport']);
       Route::post('/reports/applicat_status/export',['as'=>'report.applicants.status_export','uses'=>'ApplicantReportsController@exportData']);

//
       Route::any('/setting/instruction',['as'=>'recruitment.instruction','uses'=>'RecruitmentController@aplicationInstruction']);

       Route::resource('exam-center','ApplicantExamCenter');
       Route::resource('mark_distribution','JobCircularMarkDistributionController');
       Route::resource('training','JobApplicantTrainingDateController');
       //load image
       Route::get('/profile_image',['as'=>'profile_image','uses'=>'ApplicantScreeningController@loadImage']);
       Route::get('/test',function (){
           $a = [];
           for($i=0;$i<36;$i+=.5){
               $c = ($i*40)/35;
               $a[$i.' '] = $c;
           }
           return $a;
       });
   });


});