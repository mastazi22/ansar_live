<?php

Route::group(['prefix'=>'HRM','middleware'=>['auth','manageDatabase','checkUserType'] ],function(){
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

        //ADMIN ROUTE
        Route::get('/print_id_list', ['as' => 'print_id_list', 'uses' => 'ReportController@printIdList']);
        Route::get('/get_print_id_list', ['as' => 'get_print_id_list', 'uses' => 'ReportController@getPrintIdList']);
        Route::post('/change_ansar_card_status', ['as' => 'change_ansar_card_status', 'uses' => 'ReportController@ansarCardStatusChange']);
        Route::post('/global_parameter_update', ['as' => 'global_parameter_update', 'uses' => 'HrmController@updateGlobalParameter']);
        Route::get('/global_parameter', ['as' => 'global_parameter', 'uses' => 'HrmController@globalParameterView']);
        Route::get('/cancel_offer', ['as' => 'cancel_offer', 'uses' => 'OfferController@cancelOfferView']);
        //END ADMIN ROUTE
        //OFFER ROUTE
        Route::get('/make_offer', ['as' => 'make_offer', 'uses' => 'OfferController@makeOffer']);
        Route::get('/kpi_list', 'OfferController@getKpi');
        Route::get('/get_offered_ansar_info', 'OfferController@getOfferedAnsar');
        Route::post('/cancel_offer_handle', ['as' => 'cancel_offer_handle', 'uses' => 'OfferController@handleCancelOffer']);
        Route::post('/send_offer', 'OfferController@sendOfferSMS');
        Route::get('/get_offer_count', 'OfferController@getQuotaCount');
        Route::get('/offer_quota', ['as' => 'offer_quota', 'uses' => 'OfferController@offerQuota']);
        Route::get('/get_offer_quota', 'OfferController@getOfferQuota');
        Route::get('/cancel_offer', ['as' => 'cancel_offer', 'uses' => 'OfferController@cancelOfferView']);
        Route::post('/update_offer_quota', 'OfferController@updateOfferQuota');
        Route::get('rejected_offer_list',['as'=>'rejected_offer_list','uses'=>'ReportController@rejectedOfferListView']);
        Route::get('get_rejected_ansar_list','ReportController@getRejectedAnsarList');
        //END OFFER ROUTE
        //SESSION

        Route::get('/session', ['as' => 'create_session', 'uses' => 'SessionController@index']);
        Route::post('/save-session-entry', 'SessionController@saveSessionEntry');
        Route::get('/session_view', ['as' => 'view_session_list', 'uses' => 'SessionController@sessionView']);
        Route::get('/session-delete/{id}', ['as' => 'delete_session', 'uses' => 'SessionController@sessionDelete']);
        Route::get('/session-edit/{id}/{page}', ['as' => 'edit_session', 'uses' => 'SessionController@sessionEdit']);
        Route::post('/session-update', 'SessionController@sessionUpdate');
        route::get('/session_name', 'SessionController@SessionName');

        //END SESSION
        //GENERAL SETTING

        Route::get('/unit_form', ['as' => 'unit_form', 'uses' => 'GeneralSettingsController@unitIndex']);
        Route::get('/thana_form', ['as' => 'thana_form', 'uses' => 'GeneralSettingsController@thanaIndex']);
        Route::get('/unit_view', ['as' => 'unit_view', 'uses' => 'GeneralSettingsController@unitView']);
        Route::get('/unit_view_details', 'GeneralSettingsController@unitViewDetails');
        Route::get('/thana_view', ['as' => 'thana_view', 'uses' => 'GeneralSettingsController@thanaView']);
        Route::get('/thana_view_details', 'GeneralSettingsController@thanaViewDetails');
        Route::post('/unit_entry', ['as' => 'unit_entry', 'uses' => 'GeneralSettingsController@unitEntry']);
        Route::post('/thana_entry', 'GeneralSettingsController@thanaEntry');
        Route::get('/unit_edit/{id}', ['as' => 'unit_edit', 'uses' => 'GeneralSettingsController@unitEdit']);
        Route::get('/unit_delete/{id}', ['as' => 'unit_delete', 'uses' => 'GeneralSettingsController@unitDelete']);
        Route::get('/thana_edit/{id}', ['as' => 'thana_edit', 'uses' => 'GeneralSettingsController@thanaEdit']);
        Route::get('/thana_delete/{id}', ['as' => 'thana_delete', 'uses' => 'GeneralSettingsController@thanaDelete']);
        Route::post('/unit_update', 'GeneralSettingsController@updateUnit');
        Route::post('/thana_update', 'GeneralSettingsController@updateThana');

        Route::get('/disease_view', ['as' => 'disease_view', 'uses' => 'GeneralSettingsController@diseaseView']);
        Route::get('/add_disease', ['as' => 'disease_view', 'uses' => 'GeneralSettingsController@addDiseaseName']);
        Route::post('disease_entry', ['as' => 'disease_entry', 'uses' => 'GeneralSettingsController@diseaseEntry']);
        Route::get('/disease_edit/{id}', ['as' => 'disease_edit', 'uses' => 'GeneralSettingsController@diseaseEdit']);
        Route::post('/disease_update', ['as' => 'disease_update', 'uses' => 'GeneralSettingsController@updateDisease']);


        Route::get('/skill_view', ['as' => 'skill_view', 'uses' => 'GeneralSettingsController@skillView']);
        Route::get('/add_skill', ['as' => 'skill_view', 'uses' => 'GeneralSettingsController@addSkillName']);
        Route::post('skill_entry', ['as' => 'skill_entry', 'uses' => 'GeneralSettingsController@skillEntry']);
        Route::get('/skill_edit/{id}', ['as' => 'skill_edit', 'uses' => 'GeneralSettingsController@skillEdit']);
        Route::post('/skill_update', ['as' => 'skill_update', 'uses' => 'GeneralSettingsController@updateSkill']);

        //END GENERAL SETTING
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
        //DG ROUTE

        Route::get('/direct_offer', ['as' => 'direct_offer', 'uses' => 'DGController@directOfferView']);
        Route::get('/direct_transfer', ['as' => 'direct_transfer', 'uses' => 'DGController@directTransferView']);
        Route::get('/direct_embodiment', ['as' => 'direct_embodiment', 'uses' => 'DGController@directEmbodimentView']);
        Route::get('/direct_offer_ansar_detail', 'DGController@loadAnsarDetail');
        Route::post('/direct_embodiment_submit', ['as' => 'direct_embodiment_submit', 'uses' => 'DGController@directEmbodimentSubmit']);
        Route::post('/direct_disembodiment_submit', ['as' => 'direct_disembodiment_submit', 'uses' => 'DGController@directDisEmbodimentSubmit']);
        Route::post('/direct_transfer_submit', ['as' => 'direct_transfer_submit', 'uses' => 'DGController@directTransferSubmit']);
        Route::get('/direct_disembodiment', ['as' => 'direct_disembodiment', 'uses' => 'DGController@directDisEmbodimentView']);
        Route::get('/load_disembodiment_reason', ['as' => 'load_disembodiment_reason', 'uses' => 'DGController@loadDisembodimentReson']);
        Route::get('/direct_panel_view', ['as' => 'direct_panel_view', 'uses' => 'DGController@directPanelView']);
        Route::get('/direct_panel_ansar_details','DGController@loadAnsarDetailforDirectPanel');
        Route::post('/direct_panel_entry', 'DGController@directPanelEntry');
        Route::get('/direct_panel_cancel_view', ['as' => 'direct_panel_cancel_view', 'uses' => 'DGController@directCancelPanelView']);
        Route::get('/cancel_panel_ansar_details', 'DGController@loadAnsarDetailforCancelPanel');
        Route::post('/cancel_panel_entry_for_dg', 'DGController@cancelPanelEntry');
        Route::get('/dg_blocklist_entry_view', ['as' => 'dg_blocklist_entry_view', 'uses' => 'DGController@blockListEntryView']);
//Letter route by Arafat
        Route::get('/transfer_letter_view', ['as' => 'transfer_letter_view', 'uses' => 'LetterController@transferLetterView']);
        Route::get('/embodiment_letter_view', ['as' => 'embodiment_letter_view', 'uses' => 'LetterController@embodimentLetterView']);
        Route::get('/disembodiment_letter_view', ['as' => 'disembodiment_letter_view', 'uses' => 'LetterController@disembodimentLetterView']);
        Route::get('/print_letter', 'LetterController@printLetter');
        Route::get('KPIName', ['as' => 'kpi_name', 'uses' => 'EmbodimentController@kpiName']);

    });
    Route::get('/view_profile/{id}', '\App\Http\Controllers\UserController@viewProfile');
});