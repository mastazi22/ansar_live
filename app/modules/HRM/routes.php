<?php

Route::group(['prefix'=>'HRM','middleware'=>['manageDatabase','checkUserType'] ],function(){
    Route::group(['namespace'=>'\App\modules\HRM\Controllers'],function(){

        //DASHBOARD

        Route::get('/', 'HrmController@hrmDashboard');
        Route::get('/getTotalAnsar', ['as' => 'dashboard_total_ansar', 'uses' => 'HrmController@getTotalAnsar']);
        Route::get('/getrecentansar', ['as' => 'recent_ansar', 'uses' => 'HrmController@getRecentAnsar']);
        Route::get('/progress_info', ['as' => 'progress_info', 'uses' => 'HrmController@progressInfo']);
        Route::get('/graph_embodiment', ['as' => 'graph_embodiment', 'uses' => 'HrmController@graphEmbodiment']);
        Route::get('/graph_disembodiment', ['as' => 'graph_disembodiment', 'uses' => 'HrmController@graphDisembodiment']);
        Route::get('getrecentansar', ['as' => 'recent_ansar', 'uses' => 'HrmController@getRecentAnsar']);
        Route::get('/show_ansar_list/{type}', ['as' => 'show_ansar_list', 'uses' => 'HrmController@showAnsarList']);
        Route::get('/get_ansar_list', ['as' => 'get_ansar_list', 'uses' => 'HrmController@getAnsarList']);
        Route::get('/service_ended_in_three_years/{count}', ['as' => 'service_ended_in_three_years', 'uses' => 'HrmController@showAnsarForServiceEnded']);
        Route::get('/service_ended_info_details', ['as' => 'service_ended_info_details', 'uses' => 'HrmController@serviceEndedInfoDetails']);

        Route::get('/ansar_not_interested/{count}', ['as' => 'ansar_not_interested', 'uses' => 'HrmController@showAnsarForNotInterested']);
        Route::get('/not_interested_info_details', 'HrmController@notInterestedInfoDetails');

        Route::get('/ansar_reached_fifty_years/{count}', ['as' => 'ansar_reached_fifty_years', 'uses' => 'HrmController@showAnsarForReachedFifty']);
        Route::get('/ansar_reached_fifty_details', ['as' => 'ansar_reached_fifty_details', 'uses' => 'HrmController@ansarReachedFiftyDetails']);
        Route::get('/show_recent_ansar_list/{type}', ['as' => 'show_recent_ansar_list', 'uses' => 'HrmController@showRecentAnsarList']);
        Route::get('/get_recent_ansar_list', 'HrmController@getRecentAnsarList');

        //END DASHBOARD

        //ANSAR ENTRY

        Route::get('entrylist', ['as' => 'anser_list', 'uses' => 'EntryFormController@entrylist']);
        Route::get('/entryreport/{ansarid}', ['as' => 'entry_report', 'uses' => 'EntryFormController@entryReport']);
        Route::get('entryform', ['as' => 'ansar_registration', 'uses' => 'EntryFormController@entryform']);
        Route::get('ansar_rank', ['as' => 'ansar_rank', 'uses' => 'FormSubmitHandler@getAnsarRank']);
        Route::post('handleregistration', 'FormSubmitHandler@handleregistration');
        Route::post('submiteditentry', 'FormSubmitHandler@submitEditEntry');
        Route::get('editEntry/{ansarid}', ['as' => 'editentry', 'uses' => 'FormSubmitHandler@editEntry']);
        Route::post('entrysearch', 'FormSubmitHandler@EntrySearch');
        Route::get('chunkverify', ['as' => 'chunk_verify', 'uses' => 'FormSubmitHandler@chunkVerify']);
        Route::post('reject', 'EntryFormController@Reject');
        route::get('getBloodName', ['as' => 'blood_name', 'uses' => 'FormSubmitHandler@getBloodName']);
        Route::post('entryVerify', ['as' => 'entryverify', 'uses' => 'EntryFormController@entryVerify']);
        Route::get('getnotverifiedansar', 'FormSubmitHandler@getNotVerifiedAnsar');
        route::post('entryVerify', ['as' => 'entryverify', 'uses' => 'EntryFormController@entryVerify']);
        Route::get('getverifiedansar', 'FormSubmitHandler@getVerifiedAnsar');
        Route::get('getDiseaseName', ['as' => 'get_disease_list', 'uses' => 'EntryFormController@getAllDisease']);
        Route::get('getallskill', ['as' => 'get_skill_list', 'uses' => 'EntryFormController@getAllSkill']);
        Route::get('/getalleducation', 'EntryFormController@getAllEducation');

        //Draft entry
        Route::get('entrydraft', ['as' => 'entry_draft', 'uses' => 'DraftController@draftList']);
        Route::get('entrysingledraft', 'DraftController@entrySingleDraft');
        Route::get('draftdelete/{draftid}', ['as' => 'draftDelete', 'uses' => 'DraftController@draftDelete']);
        Route::get('getdraftlist', 'DraftController@getDraftList');
        Route::get('singledraftedit/{id}', ['as' => 'draftEdit', 'uses' => 'DraftController@singleDraftEdit']);
        Route::get('entrysingledraft/{id}', 'DraftController@entrySingleDraft');
        Route::post('editdraft/{id}', 'DraftController@editDraft');

        //END Draft entry

        //ENTRY SEARCH
        Route::get('entryadvancedsearch', ['as' => 'entry_advanced_search', 'uses' => 'EntryFormController@entryAdvancedSearch']);
        Route::get('advancedentrysearchsubmit', 'FormSubmitHandler@advancedEntrySearchSubmit');
        //END ENTRY SEARCH

        //ORGINAL INFO

        route::get('originalinfo', ['as' => 'orginal_info', 'uses' => 'EntryFormController@ansarOriginalInfo']);
        route::post('idsearch', 'FormSubmitHandler@idSearch');

        //END ORGINAL INFO

        //ANSAR ID CARD

        Route::get('/print_card_id_view', ['as' => 'print_card_id_view', 'uses' => 'ReportController@ansarPrintIdCardView']);
        Route::get('/print_card_id', ['as' => 'print_card_id', 'uses' => 'ReportController@printIdCard']);
        //END ANSAR ID CARD
        //END ANSAR ENTRY

        //SESSION

        Route::get('/session', ['as' => 'create_session', 'uses' => 'SessionController@index']);
        Route::post('/save-session-entry', 'SessionController@saveSessionEntry');
        Route::get('/session_view', ['as' => 'view_session_list', 'uses' => 'SessionController@sessionView']);
        Route::get('/session-delete/{id}', ['as' => 'delete_session', 'uses' => 'SessionController@sessionDelete']);
        Route::get('/session-edit/{id}/{page}', ['as' => 'edit_session', 'uses' => 'SessionController@sessionEdit']);
        Route::post('/session-update', 'SessionController@sessionUpdate');
        route::get('/session_name', 'SessionController@SessionName');

        //END SESSION
        //REPORT

        Route::get('/guard_report', ['as' => 'guard_report', 'uses' => 'ReportController@reportGuardSearchView']);
        Route::get('/guard_list', 'ReportController@reportAllGuard');
        Route::get('/localize_report', 'ReportController@localizeReport');
        Route::get('/ansar_service_report_view', ['as' => 'ansar_service_report_view', 'uses' => 'ReportController@ansarServiceReportView']);
        Route::get('/ansar_service_report', 'ReportController@ansarServiceReport');
        Route::get('DistrictName', ['as' => 'district_name', 'uses' => 'FormSubmitHandler@DistrictName']);
        Route::get('DivisionName', ['as' => 'division_name', 'uses' => 'FormSubmitHandler@DivisionName']);
        Route::get('ThanaName', ['as' => 'thana_name', 'uses' => 'FormSubmitHandler@ThanaName']);
        Route::get('/get_transfer_ansar_history', ['as' => 'get_transfer_ansar_history', 'uses' => 'ReportController@getAnserTransferHistory']);
        Route::get('/transfer_ansar_history', ['as' => 'transfer_ansar_history', 'uses' => 'ReportController@anserTransferHistory']);
        Route::get('/view_ansar_service_record', ['as' => 'view_ansar_service_record', 'uses' => 'ReportController@viewAnsarServiceRecord']);
        Route::get('/get_print_id_list', ['as' => 'get_print_id_list', 'uses' => 'ReportController@getPrintIdList']);
        Route::post('/change_ansar_card_status', ['as' => 'change_ansar_card_status', 'uses' => 'ReportController@ansarCardStatusChange']);
        Route::get('/print_id_list', ['as' => 'print_id_list', 'uses' => 'ReportController@printIdList']);
        Route::get('/check_file', 'ReportController@checkFile');
        Route::get('/blocklist_view', ['as' => 'blocklist_view', 'uses' => 'ReportController@blockListView']);
        Route::get('/blocklisted_ansar_info', 'ReportController@blockListedAnsarInfoDetails');

        Route::get('/blacklist_view', ['as' => 'blacklist_view', 'uses' => 'ReportController@blackListView']);
        Route::get('/blacklisted_ansar_info', 'ReportController@blackListedAnsarInfoDetails');
//End Block and BlackList Report

////Start Disembodiment Report
        Route::get('/disembodiment_report_view', ['as' => 'disembodiment_report_view', 'uses' => 'ReportController@ansarDisembodimentReportView']);
        Route::get('/disemboded_ansar_info', 'ReportController@disembodedAnsarInfo');
//End Disembodiment Report

///Start Embodiment Report
        Route::get('/embodiment_report_view', ['as' => 'embodiment_report_view', 'uses' => 'ReportController@ansarEmbodimentReportView']);
        Route::get('/emboded_ansar_info', 'ReportController@embodedAnsarInfo');
//End Embodiment Report

///Start Service Record Report
        Route::get('/service_record_unitwise_view', ['as' => 'service_record_unitwise_view', 'uses' => 'ReportController@serviceRecordUnitWise']);
        Route::get('/service_record_unitwise_info', 'ReportController@ansarInfoForServiceRecordUnitWise');
//End Service Record Report

///Start Three Years Over Report
        Route::get('/three_year_over_report_view', ['as' => 'three_year_over_report_view', 'uses' => 'ReportController@threeYearsOverListView']);
        Route::get('/three_years_over_ansar_info', 'ReportController@threeYearsOverAnsarInfo');
    });
});