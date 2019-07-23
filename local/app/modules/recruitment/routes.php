<?php
Route::group(['prefix' => 'recruitment', 'middleware' => ['recruitment'], 'namespace' => '\App\modules\recruitment\Controllers'], function () {

    Route::group(['middleware' => ['auth', 'manageDatabase', 'checkUserType', 'permission']], function () {
        Route::get('/', ['as' => 'recruitment', 'uses' => 'RecruitmentController@index']);
        Route::get('/educations', ['as' => 'educations', 'uses' => 'RecruitmentController@educationList']);
        Route::get('/getRecruitmentSummary', ['uses' => 'RecruitmentController@getRecruitmentSummary']);

        //job category route
        Route::resource('category', 'JobCategoryController', ['except' => ['destroy', 'show']]);
        Route::resource('circular', 'JobCircularController', ['except' => ['destroy', 'show']]);
        Route::get('/circular/quota_list/{id}', ['as' => 'recruitment.circular.quota_list', 'uses' => 'JobCircularController@quotaList']);
        Route::get('circular/constraint/{id}', ['as' => 'recruitment.circular.constraint', 'uses' => 'JobCircularController@constraint']);
        Route::resource('marks', 'JobApplicantMarksController', ['except' => ['show']]);
        //applicant management
        Route::any('/applicant', ['as' => 'recruitment.applicant.index', 'uses' => 'ApplicantScreeningController@index']);
        Route::any('/applicant/sms', ['as' => 'recruitment.applicant.sms', 'uses' => 'SMSController@index']);
        Route::post('/applicant/sms', ['as' => 'recruitment.applicant.sms_send', 'uses' => 'SMSController@sendSMSToApplicant']);
        Route::post('/applicant/sms_upload_file', ['as' => 'recruitment.applicant.sms_send_file', 'uses' => 'SMSController@sendSMSToApplicantByUploadFile']);
        Route::get('/applicant/detail/view/{id}', ['as' => 'recruitment.applicant.detail_view', 'uses' => 'ApplicantScreeningController@applicantDetailView']);
        Route::get('/applicant/detail/{id}', ['as' => 'recruitment.applicant.detail', 'uses' => 'ApplicantScreeningController@getApplicantData']);
        Route::post('/applicant/update', ['as' => 'recruitment.applicant.update', 'uses' => 'ApplicantScreeningController@updateApplicantData']);
        Route::post('/applicant/confirm_selection_or_rejection', ['as' => 'recruitment.applicant.confirm_selection_or_rejection', 'uses' => 'ApplicantScreeningController@confirmSelectionOrRejection']);
        Route::post('/applicant/confirm_accepted', ['as' => 'recruitment.applicant.confirm_accepted', 'uses' => 'ApplicantScreeningController@confirmAccepted']);
        Route::post('/applicant/confirm_accepted_by_uploading_file', ['as' => 'recruitment.applicant.confirm_accepted_by_uploading_file', 'uses' => 'ApplicantScreeningController@acceptApplicantByFile']);
        Route::post('/applicant/confirm_accepted_bn_candidate', ['as' => 'recruitment.applicant.confirm_accepted_if_bn_candidate', 'uses' => 'ApplicantScreeningController@confirmAcceptedIfBncandidate']);
        Route::post('/applicant/confirm_accepted_special_candidate', ['as' => 'recruitment.applicant.confirm_accepted_special_candidate', 'uses' => 'ApplicantScreeningController@confirmAcceptedIfSpecialCandidate']);
        Route::get('/applicant/search', ['as' => 'recruitment.applicant.search', 'uses' => 'ApplicantScreeningController@searchApplicant']);
        Route::post('/applicant/search', ['as' => 'recruitment.applicant.search_result', 'uses' => 'ApplicantScreeningController@loadApplicants']);
        Route::any('/applicant/info', ['as' => 'recruitment.applicant.info', 'uses' => 'ApplicantScreeningController@loadApplicantsByStatus']);
        Route::any('/applicant/revert', ['as' => 'recruitment.applicant.revert', 'uses' => 'ApplicantScreeningController@loadApplicantsForRevert']);
        Route::post('/applicant/revert_status', ['as' => 'recruitment.applicant.revert_status', 'uses' => 'ApplicantScreeningController@revertApplicantStatus']);
        Route::post('/applicant/detail/selected_applicant', ['as' => 'recruitment.applicant.selected_applicant', 'uses' => 'ApplicantScreeningController@loadSelectedApplicant']);
        Route::get('/applicant/editfield', ['as' => 'recruitment.applicant.editfield', 'uses' => 'ApplicantScreeningController@applicantEditField']);
        Route::post('/applicant/editfield', ['as' => 'recruitment.applicant.editfieldstore', 'uses' => 'ApplicantScreeningController@saveApplicantEditField']);
        Route::get('/applicant/geteditfield', ['as' => 'recruitment.applicant.getfieldstore', 'uses' => 'ApplicantScreeningController@loadApplicantEditField']);
        Route::get('/applicant/final_list', ['as' => 'recruitment.applicant.final_list', 'uses' => 'ApplicantScreeningController@acceptedApplicantView']);
        Route::post('/applicant/final_list/load', ['as' => 'recruitment.applicant.final_list_load', 'uses' => 'ApplicantScreeningController@loadApplicantByQuota']);

        Route::get('/applicant/list/{type?}', ['as' => 'recruitment.applicant.list', 'uses' => 'ApplicantScreeningController@applicantListSupport']);
        Route::get('/applicants/list/{circular_id}/{type?}', ['as' => 'recruitment.applicants.list', 'uses' => 'ApplicantScreeningController@applicantList']);
        Route::get('/applicant/mark_as_paid/{type}/{id}/{circular_id}', ['as' => 'recruitment.applicant.mark_as_paid', 'uses' => 'ApplicantScreeningController@markAsPaid']);
        Route::post('/applicant/mark_as_paid/{id}', ['as' => 'recruitment.applicant.update_as_paid', 'uses' => 'ApplicantScreeningController@updateAsPaid']);
        Route::any('/applicant/update_as_paid_by_file', ['as' => 'recruitment.applicant.update_as_paid_by_file', 'uses' => 'ApplicantScreeningController@updateAsPaidByFile']);
        Route::any('/applicant/move_to_hrm', ['as' => 'recruitment.move_to_hrm', 'uses' => 'ApplicantScreeningController@moveApplicantToHRM']);
        Route::any('/applicant/edit_for_hrm', ['as' => 'recruitment.edit_for_hrm', 'uses' => 'ApplicantScreeningController@editApplicantForHRM']);
        Route::any('/applicant/applicant_edit_for_hrm/{type}/{id}', ['as' => 'recruitment.applicant_edit_for_hrm', 'uses' => 'ApplicantScreeningController@applicantEditForHRM']);
        Route::post('/applicant/store_hrm_detail', ['as' => 'recruitment.store_hrm_detail', 'uses' => 'ApplicantScreeningController@storeApplicantHRmDetail']);
        Route::any('/applicant/generate_roll_no', ['as' => 'recruitment.applicant.generate_roll_no', 'uses' => 'ApplicantScreeningController@generateApplicantRoll']);


        Route::any('/applicant/hrm', ['as' => 'recruitment.hrm.index', 'uses' => 'ApplicantHRMController@index']);
        Route::get('/applicant/hrm/{type}/{circular_id}/{id}', ['as' => 'recruitment.hrm.view_download', 'uses' => 'ApplicantHRMController@applicantEditForHRM']);
        Route::post('/applicant/hrm/move/{id}', ['as' => 'recruitment.hrm.move', 'uses' => 'ApplicantHRMController@moveApplicantToHRM']);
        Route::post('/applicant/hrm/bulk_move', ['as' => 'recruitment.hrm.bulk_move', 'uses' => 'ApplicantHRMController@moveBulkApplicantToHRM']);

        Route::any('/applicant/hrm/card_print', ['as' => 'recruitment.hrm.card_print', 'uses' => 'ApplicantHRMController@print_card']);

        //settings
        //quota
        Route::any('/settings/applicant_quota', ['as' => 'recruitment.quota.index', 'uses' => 'JobApplicantQuotaController@index']);
        Route::any('/settings/applicant_quota/edit', ['as' => 'recruitment.quota.edit', 'uses' => 'JobApplicantQuotaController@edit']);
        Route::post('/settings/applicant_quota/update', ['as' => 'recruitment.quota.update', 'uses' => 'JobApplicantQuotaController@update']);
        //point table
        Route::resource('marks_rules', 'ApplicantMarksRuleController');

        //support
        Route::any('/supports/feedback', ['as' => 'supports.feedback', 'uses' => 'SupportController@problemReport']);
        Route::post('/supports/feedback/{id}', ['as' => 'supports.feedback.submit', 'uses' => 'SupportController@replyProblem']);
        Route::post('/supports/feedback/delete/{id}', ['as' => 'supports.feedback.delete', 'uses' => 'SupportController@replyProblemDelete']);


        Route::any('/reports/applicat_status', ['as' => 'report.applicants.status', 'uses' => 'ApplicantReportsController@applicantStatusReport']);
        Route::any('/reports/applicat_accepted_list', ['as' => 'report.applicants.applicat_accepted_list', 'uses' => 'ApplicantReportsController@applicantAcceptedListReport']);
        Route::any('/reports/applicat_marks_list', ['as' => 'report.applicants.applicat_marks_list', 'uses' => 'ApplicantReportsController@applicantMarksReport']);
        Route::post('/reports/applicat_status/export', ['as' => 'report.applicants.status_export', 'uses' => 'ApplicantReportsController@exportData']);
        Route::get('/reports/applicant_details/', ['as' => 'report.applicant_details', 'uses' => 'ApplicantReportsController@applicantDetailsReport']);
        Route::post('/reports/applicant_details/export', ['as' => 'report.applicant_details.export', 'uses' => 'ApplicantReportsController@exportApplicantDetailReport']);
        Route::get('/reports/download', ['as' => 'report.download', 'uses' => 'ApplicantReportsController@download']);

//
        Route::get('/setting/instruction', ['as' => 'recruitment.instruction', 'uses' => 'RecruitmentController@aplicationInstruction']);
        Route::any('/setting/instruction/create', ['as' => 'recruitment.instruction.create', 'uses' => 'RecruitmentController@createApplicationInstruction']);
        Route::any('/setting/instruction/edit/{id}', ['as' => 'recruitment.instruction.edit', 'uses' => 'RecruitmentController@editApplicationInstruction']);

        Route::resource('exam-center', 'ApplicantExamCenter');
        Route::resource('mark_distribution', 'JobCircularMarkDistributionController');
        Route::resource('training', 'JobApplicantTrainingDateController');
        //load image
        Route::get('/profile_image', ['as' => 'profile_image', 'uses' => 'ApplicantScreeningController@loadImage']);
        Route::get('/test', function () {
            $data = \Illuminate\Support\Facades\DB::table('job_applicant')
                ->join('job_circular','job_circular.id','=','job_applicant.job_circular_id')
                ->join('job_applicant_exam_center','job_circular.id','=','job_applicant_exam_center.job_circular_id')
                ->where('job_applicant.status','selected')
                ->select('job_applicant.applicant_id','job_applicant.applicant_name_bng','job_applicant.roll_no','job_circular.circular_name','job_applicant.applicant_password',
                    'job_applicant_exam_center.selection_date','job_applicant_exam_center.selection_time','mobile_no_self')
                ->get();
            $datas = [];
//            array_push($datas,['mobile_no_self','sms_body']);
            foreach ($data as $d){
                $bang = ['0'=>'০','1'=>'১','2'=>'২','3'=>'৩','4'=>'৪','5'=>'৫','6'=>'৬','7'=>'৭','8'=>'৮','9'=>'৯'];
                $date_array = str_split(\Carbon\Carbon::parse($d->selection_date)->format('d/m/Y'));
                $time_array = str_split($d->selection_time);
                $roll_array = str_split($d->roll_no);
                $date = "";
                $time = "";
                $time_a = "";
                $roll_no = "";
                foreach ($date_array as $da){
                    if(isset($bang[$da])){
                        $date .= $bang[$da];
                    }else{
                        $date .= $da;
                    }
                }
                foreach ($time_array as $da){
                    if(isset($bang[$da])){
                        $time_a .= $bang[$da];
                    }else{
                        $time_a .= $da;
                    }
                }
                foreach ($roll_array as $da){
                    if(isset($bang[$da])){
                        $roll_no .= $bang[$da];
                    }else{
                        $roll_no .= $da;
                    }
                }
                $rr = explode(" ",$time_a);
                if(!strcasecmp($rr[1],'am')){
                    $time = "সকাল $rr[0]";
                }else{
                    $time = "বিকাল $rr[0]";
                }
                array_push($datas,[$d->mobile_no_self,"নামঃ ".$d->applicant_name_bng.",  আইডিঃ ".$d->applicant_id.", পাসওয়ার্ডঃ ".$d->applicant_password.", রোল নংঃ $roll_no , পদবীঃ ".explode("|",$d->circular_name)[0]." , পরীক্ষার তারিখঃ $date,  সময়ঃ $time । প্রবেশপত্র ও বিস্তারিত  তথ্যের জন্য ভিজিট করুনঃ  www.ansarvdp.gov.bd"]);
            }
            return \Maatwebsite\Excel\Facades\Excel::create('sms_file_download',function($excel) use($datas){
                $excel->sheet('Sheet1', function($sheet) use($datas) {

                    $sheet->fromArray($datas);

                });
            })->export('xls');
        });
        Route::get('/test1', function () {
            $applicants = \App\modules\recruitment\Models\JobAppliciant::whereHas('selectedApplicant',function(){

            })->where('status','paid')->get();
            foreach ($applicants as $applicant){
                $applicant->update('status','selected');
            }

        });
        Route::get('/testt', function () {
            $func = 'leftJoin';
            $q = \Illuminate\Support\Facades\DB::table('job_applicant');
            $q->$func('job_quota','job_applicant_id','=','job_applicant.id');
            return $q->toSql();
        });
        Route::get('/send_sms_paid', ['as' => 'send_sms_paid', 'uses' => 'SupportController@sendUserNamePassword']);

        Route::resource('quota','JobCircularApplicantQuota');
    });


});