<?php
use App\Helper\Facades\GlobalParameterFacades;
use App\modules\HRM\Models\PanelModel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Log;

Route::group(['prefix' => 'HRM', 'middleware' => 'manageDatabase', 'namespace' => '\App\modules\HRM\Controllers'], function () {
    Route::any('/send_sms', 'SMSController@sendSMS');
    Route::post('/receive_sms', ['as' => 'receive_sms', 'uses' => 'SMSController@receiveSMS']);
    Route::post('/get_sms_status', 'SMSController@getSMSStatus');
//    Route::get('/panel_list/{sex}/{designation}',['as'=>'panel_list','uses'=>'PanelController@getPanelListBySexAndDesignation']);
    Route::get('/central_panel_list', ['as' => 'central_panel_list', 'uses' => 'PanelController@getCentralPanelList']);
});
Route::group(['prefix' => 'HRM', 'middleware' => ['hrm']], function () {
    Route::group(['namespace' => '\App\modules\HRM\Controllers', 'middleware' => ['auth', 'manageDatabase', 'checkUserType', 'permission']], function () {


        Route::get('download/file/{dataJob}', ['as' => 'download_file', 'uses' => 'DownloadController@downloadFile']);
        Route::get('download/file/name/{file}', ['as' => 'download_file_by_name', 'uses' => 'DownloadController@downloadFileByName']);
        Route::get('delete/file/{dataJob}', ['as' => 'delete_file', 'uses' => 'DownloadController@deleteFiles']);
        Route::post('generate/file/{dataJob}', ['as' => 'generate_file', 'uses' => 'DownloadController@generatingFile']);


        Route::get('view_image/{type}/{file}', ['as' => 'view_image', 'uses' => 'FormSubmitHandler@getImage']);
        //DASHBOARD
        Route::get('/tesst', function () {
            return (UserPermission::isPermissionExists(Input::get('q')) ? "found" : "not found");
        });
        Route::get('/', ['as' => '', 'uses' => 'HrmController@hrmDashboard']);
        Route::get('/getTotalAnsar', ['as' => 'dashboard_total_ansar', 'uses' => 'HrmController@getTotalAnsar']);
        Route::get('/getrecentansar', ['as' => 'recent_ansar', 'uses' => 'HrmController@getRecentAnsar']);
        Route::get('/progress_info', ['as' => 'progress_info', 'uses' => 'HrmController@progressInfo']);
        Route::get('/graph_embodiment', ['as' => 'graph_embodiment', 'uses' => 'HrmController@graphEmbodiment']);
        Route::get('/graph_disembodiment', ['as' => 'graph_disembodiment', 'uses' => 'HrmController@graphDisembodiment']);
        Route::get('/template_list/{key}', ['as' => 'template_list', 'uses' => 'HrmController@getTemplate']);
//        Route::get('getrecentansar', ['as' => 'recent_ansar', 'uses' => 'HrmController@getRecentAnsar']);
        Route::get('/show_ansar_list/{type}', ['as' => 'show_ansar_list', 'uses' => 'HrmController@showAnsarList'])->where('type', '^[a-z]+(_[a-z]+)+$');
        Route::get('/get_ansar_list', ['as' => 'get_ansar_list', 'uses' => 'HrmController@getAnsarList']);
        Route::get('/service_ended_in_three_years/{count}', ['as' => 'service_ended_in_three_years', 'uses' => 'HrmController@showAnsarForServiceEnded'])->where('count', '^[0-9,]+$');
        Route::get('/offer_accept_last_5_day', ['as' => 'offer_accept_last_5_day', 'uses' => 'HrmController@offerAcceptLastFiveDays']);
        Route::get('/offer_accept_last_5_day_data', ['as' => 'offer_accept_last_5_day_data', 'uses' => 'HrmController@ansarAcceptOfferLastFiveDays']);
        Route::get('/service_ended_info_details', ['as' => 'service_ended_info_details', 'uses' => 'HrmController@serviceEndedInfoDetails']);

        Route::get('/ansar_not_interested/{count}', ['as' => 'ansar_not_interested', 'uses' => 'HrmController@showAnsarForNotInterested'])->where('count', '^[0-9,]+$');
        Route::get('/not_interested_info_details', ['as' => 'not_interested_info_details', 'uses' => 'HrmController@notInterestedInfoDetails']);

        Route::get('/ansar_reached_fifty_years/{count}', ['as' => 'ansar_reached_fifty_years', 'uses' => 'HrmController@showAnsarForReachedFifty'])->where('count', '^[0-9,]+$');
        Route::get('/ansar_reached_fifty_details', ['as' => 'ansar_reached_fifty_details', 'uses' => 'HrmController@ansarReachedFiftyDetails']);
        Route::get('/show_recent_ansar_list/{type}', ['as' => 'show_recent_ansar_list', 'uses' => 'HrmController@showRecentAnsarList']);
        Route::get('/get_recent_ansar_list', ['as' => 'get_recent_ansar_list', 'uses' => 'HrmController@getRecentAnsarList']);


        Route::get('/ansar_detail_info', ['as' => 'ansar_detail_info', 'uses' => 'HrmController@getAnsarInfoinExcel']);
        Route::post('/ansar_detail_info', ['as' => 'generate_ansar_detail_info', 'uses' => 'HrmController@generateAnsarInfoExcel']);

        //END DASHBOARD
//Start Panel
        Route::get('/panel_view', ['as' => 'view_panel_list', 'uses' => 'PanelController@panelView']);
        Route::post('/save-panel-entry', ['as' => 'save-panel-entry', 'uses' => 'PanelController@savePanelEntry']);
        Route::get('/search_panel_by_id', ['as' => 'search_panel', 'uses' => 'PanelController@searchPanelByID']);
        Route::get('/select_status', ['as' => 'select_status', 'uses' => 'PanelController@statusSelection']);
        //end Panel
        //ANSAR ENTRY

        Route::get('entrylist', ['as' => 'anser_list', 'uses' => 'EntryFormController@entrylist']);
        Route::get('/entryreport/{ansarid}/{type?}', ['as' => 'entry_report', 'uses' => 'EntryFormController@entryReport'])->where('ansarid', '[0-9]+');
        Route::get('entryform', ['as' => 'ansar_registration', 'uses' => 'EntryFormController@entryform']);
        Route::get('ansar_rank', ['as' => 'ansar_rank', 'uses' => 'FormSubmitHandler@getAnsarRank']);
        Route::post('handleregistration', ['as' => 'handleregistration', 'uses' => 'FormSubmitHandler@handleregistration']);
        Route::post('submiteditentry', ['as' => 'submiteditentry', 'uses' => 'FormSubmitHandler@submitEditEntry']);
        Route::get('editEntry/{ansarid}', ['as' => 'editentry', 'uses' => 'FormSubmitHandler@editEntry'])->where('ansarid', '^[0-9]+$');
        Route::get('editVerifiedEntry/{ansarid}', ['as' => 'editVerifiedEntry', 'uses' => 'FormSubmitHandler@editEntry'])->where('ansarid', '^[0-9]+$');
        Route::post('entrysearch', 'FormSubmitHandler@EntrySearch');
        Route::get('chunkverify', ['as' => 'chunk_verify', 'uses' => 'FormSubmitHandler@chunkVerify']);
        Route::post('reject', ['as' => 'reject', 'uses' => 'EntryFormController@Reject']);
        route::get('getBloodName', ['as' => 'blood_name', 'uses' => 'FormSubmitHandler@getBloodName']);
        Route::post('entryVerify', ['as' => 'entryverify', 'uses' => 'EntryFormController@entryVerify']);
        Route::post('entryChunkVerify', ['as' => 'entryChunkVerify', 'uses' => 'EntryFormController@entryChunkVerify']);
        Route::get('getnotverifiedansar', ['as' => 'getnotverifiedansar', 'uses' => 'FormSubmitHandler@getNotVerifiedAnsar']);
        Route::get('getverifiedansar', ['as' => 'getverifiedansar', 'uses' => 'FormSubmitHandler@getVerifiedAnsar']);
        Route::get('getDiseaseName', ['as' => 'get_disease_list', 'uses' => 'EntryFormController@getAllDisease']);
        Route::get('getallskill', ['as' => 'get_skill_list', 'uses' => 'EntryFormController@getAllSkill']);
        Route::get('/getalleducation', ['as' => 'getalleducation', 'uses' => 'EntryFormController@getAllEducation']);

        //Draft entry
        Route::get('entrydraft', ['as' => 'entry_draft', 'uses' => 'DraftController@draftList']);
//        Route::get('entrysingledraft', ['as'=>'entrysingledraft','uses'=>'DraftController@entrySingleDraft']);
        Route::get('draftdelete/{draftid}', ['as' => 'draftDelete', 'uses' => 'DraftController@draftDelete'])->where('draftid', '[0-9]+\.txt');
        Route::get('getdraftlist', ['as' => 'getdraftlist', 'uses' => 'DraftController@getDraftList']);
        Route::get('singledraftedit/{id}', ['as' => 'draftEdit', 'uses' => 'DraftController@singleDraftEdit']);
        Route::get('entrysingledraft/{id}', ['as' => 'entrysingledraft', 'uses' => 'DraftController@entrySingleDraft']);
        Route::post('editdraft/{id}', ['as' => 'editdraft', 'uses' => 'DraftController@editDraft']);

        //END Draft entry

        //ENTRY SEARCH
        Route::get('entryadvancedsearch', ['as' => 'entry_advanced_search', 'uses' => 'EntryFormController@entryAdvancedSearch']);
        Route::post('advancedentrysearchsubmit', ['as' => 'search_result', 'uses' => 'FormSubmitHandler@advancedEntrySearchSubmit']);
        //END ENTRY SEARCH


        //UPLOAD IMAGES
        Route::get('upload/photo_signature', ['as' => 'photo_signature', 'uses' => 'PhotoUploadController@uploadPhotoSignature']);
        Route::get('upload/photo_original', ['as' => 'photo_original', 'uses' => 'PhotoUploadController@uploadOriginalInfo']);
        Route::post('upload/photo/store', ['as' => 'photo_store', 'uses' => 'PhotoUploadController@storePhoto']);
        Route::post('upload/signature/store', ['as' => 'signature_store', 'uses' => 'PhotoUploadController@storeSignature']);
        Route::post('upload/original_front/store', ['as' => 'original_front', 'uses' => 'PhotoUploadController@storeOriginalFrontInfo']);
        Route::post('upload/original_back/store', ['as' => 'original_back', 'uses' => 'PhotoUploadController@storeOriginalBackInfo']);

        //END UPLOAD IMAGES


        //ORGINAL INFO

        route::get('originalinfo', ['as' => 'orginal_info', 'uses' => 'EntryFormController@ansarOriginalInfo']);
        route::any('entryInfo', ['as' => 'entry_info', 'uses' => 'EntryFormController@entryInfo']);
        route::post('idsearch', ['as' => 'idsearch', 'uses' => 'FormSubmitHandler@idSearch']);

        //END ORGINAL INFO

        //ANSAR ID CARD

        Route::get('/print_card_id_view', ['as' => 'print_card_id_view', 'uses' => 'ReportController@ansarPrintIdCardView']);
        Route::post('/print_card_id', ['as' => 'print_card_id', 'uses' => 'ReportController@printIdCard']);
        Route::get('/id_card_history', ['as' => 'id_card_history', 'uses' => 'ReportController@getAnsarIDHistory']);

        //END ANSAR ID CARD
        //END ANSAR ENTRY

        //ADMIN ROUTE
        Route::get('/print_id_list', ['as' => 'print_id_list', 'uses' => 'ReportController@printIdList']);
        Route::get('/get_print_id_list', ['as' => 'get_print_id_list', 'uses' => 'ReportController@getPrintIdList']);
        Route::post('/change_ansar_card_status', ['as' => 'change_ansar_card_status', 'uses' => 'ReportController@ansarCardStatusChange']);
        Route::post('/global_parameter_update', ['as' => 'global_parameter_update', 'uses' => 'HrmController@updateGlobalParameter']);
        Route::get('/system_setting', ['as' => 'system_setting', 'uses' => 'HrmController@systemSettingIndex']);
        Route::get('/system_setting_edit/{id}', ['as' => 'system_setting_edit', 'uses' => 'HrmController@systemSettingEdit']);
        Route::post('/system_setting_update/{id}', ['as' => 'system_setting_update', 'uses' => 'HrmController@systemSettingUpdate']);
        Route::get('/global_parameter', ['as' => 'global_parameter', 'uses' => 'HrmController@globalParameterView']);
        Route::get('/cancel_offer', ['as' => 'cancel_offer', 'uses' => 'OfferController@cancelOfferView']);
        //END ADMIN ROUTE
        //OFFER ROUTE
        Route::get('/make_offer', ['as' => 'make_offer', 'uses' => 'OfferController@makeOffer']);
        Route::post('/kpi_list', ['as' => 'kpi_list', 'uses' => 'OfferController@getKpi']);
        Route::get('/get_offered_ansar_info', ['as' => 'get_offered_ansar_info', 'uses' => 'OfferController@getOfferedAnsar']);
        Route::post('/cancel_offer_handle', ['as' => 'cancel_offer_handle', 'uses' => 'OfferController@handleCancelOffer']);
        Route::post('/send_offer', ['as' => 'send_offer', 'uses' => 'OfferController@sendOfferSMS']);
        Route::get('/get_offer_count', ['as' => 'get_offer_count', 'uses' => 'OfferController@getQuotaCount']);
        Route::get('/offer_quota', ['as' => 'offer_quota', 'uses' => 'OfferController@offerQuota']);
        Route::get('/get_offer_quota', ['as' => 'get_offer_quota', 'uses' => 'OfferController@getOfferQuota']);
        Route::get('/cancel_offer', ['as' => 'cancel_offer', 'uses' => 'OfferController@cancelOfferView']);
        Route::any('/update_offer_quota', ['as' => 'update_offer_quota', 'uses' => 'OfferController@updateOfferQuota']);
        Route::get('rejected_offer_list', ['as' => 'rejected_offer_list', 'uses' => 'ReportController@rejectedOfferListView']);
        Route::get('get_rejected_ansar_list', ['as' => 'get_rejected_ansar_list', 'uses' => 'ReportController@getRejectedAnsarList']);
        Route::resource('offer_zone','OfferZoneController');
        //END OFFER ROUTE

        Route::resource('offer_rollback', 'OfferBlockController');
        //SESSION

        Route::get('/session', ['as' => 'create_session', 'uses' => 'SessionController@index']);
        Route::post('/save-session-entry', ['as' => 'save-session-entry', 'uses' => 'SessionController@saveSessionEntry']);
        Route::get('/session_view', ['as' => 'session_view', 'uses' => 'SessionController@sessionView']);
        Route::get('/session-delete/{id}', ['as' => 'delete_session', 'uses' => 'SessionController@sessionDelete']);
        Route::get('/session-edit/{id}/{page}', ['as' => 'edit_session', 'uses' => 'SessionController@sessionEdit'])->where('id', '[0-9]+')->where('page', '[0-9]+');
        Route::post('/session-update', ['as' => 'session-update', 'uses' => 'SessionController@sessionUpdate']);
        route::get('/session_name', 'SessionController@SessionName');

        //END SESSION
        //GENERAL SETTING

        Route::get('/thana_form', ['as' => 'thana_form', 'uses' => 'GeneralSettingsController@thanaIndex']);
        Route::get('/thana_view', ['as' => 'thana_view', 'uses' => 'GeneralSettingsController@thanaView']);
        Route::get('/thana_view_details', ['as' => 'thana_details', 'uses' => 'GeneralSettingsController@thanaViewDetails']);
        Route::post('/thana_entry', ['as' => 'thana_entry', 'uses' => 'GeneralSettingsController@thanaEntry']);
        Route::get('/thana_edit/{id}', ['as' => 'thana_edit', 'uses' => 'GeneralSettingsController@thanaEdit'])->where('id', '[0-9]+');
        Route::get('/thana_delete/{id}', ['as' => 'thana_delete', 'uses' => 'GeneralSettingsController@thanaDelete']);
        Route::post('/thana_update', ['as' => 'thana_update', 'uses' => 'GeneralSettingsController@updateThana']);

        Route::get('/disease_view', ['as' => 'disease_view', 'uses' => 'GeneralSettingsController@diseaseView']);
        Route::any('/add_disease', ['as' => 'add_disease_view', 'uses' => 'GeneralSettingsController@addDiseaseName']);
        Route::post('disease_entry', ['as' => 'disease_entry', 'uses' => 'GeneralSettingsController@diseaseEntry']);
        Route::get('/disease_edit/{id}', ['as' => 'disease_edit', 'uses' => 'GeneralSettingsController@diseaseEdit']);
        Route::post('/disease_update', ['as' => 'disease_update', 'uses' => 'GeneralSettingsController@updateDisease']);


        Route::get('/skill_view', ['as' => 'skill_view', 'uses' => 'GeneralSettingsController@skillView']);
        Route::get('/add_skill', ['as' => 'add_skill_view', 'uses' => 'GeneralSettingsController@addSkillName']);
        Route::post('skill_entry', ['as' => 'skill_entry', 'uses' => 'GeneralSettingsController@skillEntry']);
        Route::get('/skill_edit/{id}', ['as' => 'skill_edit', 'uses' => 'GeneralSettingsController@skillEdit']);
        Route::post('/skill_update', ['as' => 'skill_update', 'uses' => 'GeneralSettingsController@updateSkill']);
        Route::get('unit/all-units', ['as' => 'all-units', 'uses' => 'UnitController@allUnit']);
        Route::resource('unit', 'UnitController');
        Route::resource('range', 'DivisionController');
        Route::get('union/showall', ['as' => 'HRM.union.showall', 'uses' => 'UnionController@showAll']);
        Route::resource('union', 'UnionController');
        Route::get('main_training/all', ['as' => 'HRM.main_training.all', 'uses' => 'MainTrainingInfoController@getAllTraining']);
        Route::resource('main_training', 'MainTrainingInfoController');
        Route::get('sub_training/all/{id}', ['as' => 'HRM.main_training.all', 'uses' => 'SubTrainingInfoController@getAllTraining']);
        Route::resource('sub_training', 'SubTrainingInfoController');

        //END GENERAL SETTING
        //REPORT

        Route::get('/guard_report', ['as' => 'guard_report', 'uses' => 'ReportController@reportGuardSearchView']);
        Route::get('/guard_list', ['as' => 'guard_list', 'uses' => 'ReportController@reportAllGuard']);
        Route::get('/localize_report', ['as' => 'localize_report', 'uses' => 'ReportController@localizeReport']);
        Route::get('/ansar_service_report_view', ['as' => 'ansar_service_report_view', 'uses' => 'ReportController@ansarServiceReportView']);
        Route::get('/ansar_service_report', ['as' => 'ansar_service_report', 'uses' => 'ReportController@ansarServiceReport']);
        Route::get('DistrictName', ['as' => 'district_name', 'uses' => 'FormSubmitHandler@DistrictName']);
        Route::get('DivisionName', ['as' => 'division_name', 'uses' => 'FormSubmitHandler@DivisionName']);
        Route::get('ThanaName', ['as' => 'thana_name', 'uses' => 'FormSubmitHandler@ThanaName']);
        Route::get('/get_transfer_ansar_history', ['as' => 'get_transfer_ansar_history', 'uses' => 'ReportController@getAnserTransferHistory']);
        Route::get('/transfer_ansar_history', ['as' => 'transfer_ansar_history', 'uses' => 'ReportController@anserTransferHistory']);
        Route::get('/view_ansar_service_record', ['as' => 'view_ansar_service_record', 'uses' => 'ReportController@viewAnsarServiceRecord']);
        Route::get('/get_print_id_list', ['as' => 'get_print_id_list', 'uses' => 'ReportController@getPrintIdList']);
        Route::post('/change_ansar_card_status', ['as' => 'change_ansar_card_status', 'uses' => 'ReportController@ansarCardStatusChange']);
        Route::get('/print_id_list', ['as' => 'print_id_list', 'uses' => 'ReportController@printIdList']);
        Route::get('/check_file', ['as' => 'check_file', 'uses' => 'ReportController@checkFile']);
        Route::get('/blocklist_view', ['as' => 'blocklist_view', 'uses' => 'ReportController@blockListView']);
        Route::get('/blocklisted_ansar_info', ['as' => 'blocklisted_ansar_info', 'uses' => 'ReportController@blockListedAnsarInfoDetails']);

        Route::get('/blacklist_view', ['as' => 'blacklist_view', 'uses' => 'ReportController@blackListView']);
        Route::get('/blacklisted_ansar_info', ['as' => 'blacklisted_ansar_info', 'uses' => 'ReportController@blackListedAnsarInfoDetails']);
//End Block and BlackList Report

////Start Disembodiment Report
        Route::get('/disembodiment_report_view', ['as' => 'disembodiment_report_view', 'uses' => 'ReportController@ansarDisembodimentReportView']);
        Route::get('/disemboded_ansar_info', ['as' => 'disemboded_ansar_info', 'uses' => 'ReportController@disembodedAnsarInfo']);
//End Disembodiment Report

///Start Embodiment Report
        Route::get('/embodiment_report_view', ['as' => 'embodiment_report_view', 'uses' => 'ReportController@ansarEmbodimentReportView']);
        Route::get('/emboded_ansar_info', ['as' => 'emboded_ansar_info', 'uses' => 'ReportController@embodedAnsarInfo']);
//End Embodiment Report

///Start Service Record Report
//        Route::get('/service_record_unitwise_view', ['as' => 'service_record_unitwise_view', 'uses' => 'ReportController@serviceRecordUnitWise']);
//        Route::get('/service_record_unitwise_info', ['as'=>'service_record_unitwise_info','uses'=>'ReportController@ansarInfoForServiceRecordUnitWise']);
//End Service Record Reportembodiment_report_view

///Start Three Years Over Report
        Route::get('/three_year_over_report_view', ['as' => 'three_year_over_report_view', 'uses' => 'ReportController@threeYearsOverListView']);
        Route::get('/three_years_over_ansar_info', ['as' => 'three_years_over_ansar_info', 'uses' => 'ReportController@threeYearsOverAnsarInfo']);

        Route::get('/ansar_history', ['as' => 'ansar_history', 'uses' => 'ReportController@ansarHistoryView']);
        Route::post('/get_ansar_history', ['as' => 'get_ansar_history', 'uses' => 'ReportController@getAnsarHistory']);

        //DG ROUTE

        Route::get('/direct_offer', ['as' => 'direct_offer', 'uses' => 'DGController@directOfferView']);
        Route::get('/direct_transfer', ['as' => 'direct_transfer', 'uses' => 'DGController@directTransferView']);
        Route::get('/direct_embodiment_ansar_details', ['as' => 'direct_embodiment_ansar_details', 'uses' => 'DGController@loadAnsarForDirectEmbodiment']);
        Route::get('/direct_disembodiment_ansar_details', ['as' => 'direct_disembodiment_ansar_details', 'uses' => 'DGController@loadAnsarForDirectDisEmbodiment']);
        Route::get('/direct_embodiment', ['as' => 'direct_embodiment', 'uses' => 'DGController@directEmbodimentView']);
        Route::get('/direct_offer_ansar_detail', ['as' => 'ansar_detail_info', 'uses' => 'DGController@loadAnsarDetail']);
        Route::post('/direct_embodiment_submit', ['as' => 'direct_embodiment_submit', 'uses' => 'DGController@directEmbodimentSubmit']);
        Route::post('/direct_disembodiment_submit', ['as' => 'direct_disembodiment_submit', 'uses' => 'DGController@directDisEmbodimentSubmit']);
        Route::post('/direct_transfer_submit', ['as' => 'direct_transfer_submit', 'uses' => 'DGController@directTransferSubmit']);
        Route::get('/direct_disembodiment', ['as' => 'direct_disembodiment', 'uses' => 'DGController@directDisEmbodimentView']);
        Route::get('/load_disembodiment_reason', ['as' => 'load_disembodiment_reason', 'uses' => 'DGController@loadDisembodimentReson']);
        Route::get('/direct_panel_view', ['as' => 'direct_panel_view', 'uses' => 'DGController@directPanelView']);
        Route::get('/direct_panel_ansar_details', 'DGController@loadAnsarDetailforDirectPanel');
        Route::get('/direct_offer_ansar_details', 'DGController@loadAnsarDetailforDirectOffer');
        Route::post('/direct_panel_entry', ['as' => 'direct_panel_entry', 'uses' => 'DGController@directPanelEntry']);
        Route::get('/direct_panel_cancel_view', ['as' => 'direct_panel_cancel_view', 'uses' => 'DGController@directCancelPanelView']);
        Route::get('/cancel_panel_ansar_details', 'DGController@loadAnsarDetailforCancelPanel');
        Route::post('/cancel_panel_entry_for_dg', ['as' => 'cancel_panel_entry_for_dg', 'uses' => 'DGController@cancelPanelEntry']);
        Route::post('/direct_offer', ['as' => 'send_direct_offer', 'uses' => 'DGController@directOfferSend']);
        Route::post('/direct_offer_cancel', 'DGController@directOfferCancel');
        Route::get('/dg_blocklist_entry_view', ['as' => 'dg_blocklist_entry_view', 'uses' => 'DGController@blockListEntryView']);
        Route::get('/blocklist_entry_view', ['as' => 'blocklist_entry_view', 'uses' => 'BlockBlackController@blockListEntryView']);
        Route::get('/blocklist_ansar_details', ['as' => 'blocklist_ansar_details', 'uses' => 'BlockBlackController@loadAnsarDetailforBlock']);
        Route::post('/blocklist_entry', ['as' => 'blocklist_entry', 'uses' => 'BlockBlackController@blockListEntry']);
        Route::any('/user_action_log/{id?}', ['as' => 'user_action_log', 'uses' => 'DGController@viewUserActionLog']);
        Route::post('/multi_blocklist_entry', ['as' => 'multi_blocklist_entry', 'uses' => 'BlockBlackController@arrayBlockListEntry']);


        Route::get('/unblocklist_entry_view', ['as' => 'unblocklist_entry_view', 'uses' => 'BlockBlackController@unblockListEntryView']);
        Route::get('/unblocklist_ansar_details', ['as' => 'unblocklist_ansar_details', 'uses' => 'BlockBlackController@loadAnsarDetailforUnblock']);
        Route::post('/unblocklist_entry', ['as' => 'unblocklist_entry', 'uses' => 'BlockBlackController@unblockListEntry']);
        //TRANSFER
        Route::get('/transfer_process', ['as' => 'transfer_process', 'uses' => 'EmbodimentController@transferProcessView']);
        Route::any('/disembodiment_ansar_correction', ['as' => 'transfer_process', 'uses' => 'EmbodimentController@transferProcessView']);
        Route::get('/single_embodied_ansar_detail/{id}', ['as' => 'single_embodied_ansar_detail', 'uses' => 'EmbodimentController@getSingleEmbodiedAnsarInfo']);
        Route::get('/multiple_kpi_transfer_process', ['as' => 'multiple_kpi_transfer_process', 'uses' => 'EmbodimentController@multipleKpiTransferView']);
        Route::post('/search_kpi_by_ansar', ['as' => 'search_kpi_by_ansar', 'uses' => 'EmbodimentController@getEmbodiedAnsarInfo']);
        Route::post('/confirm_transfer', ['as' => 'confirm_transfer', 'uses' => 'EmbodimentController@confirmTransfer']);
        Route::get('/get_embodied_ansar', ['as' => 'get_embodied_ansar', 'uses' => 'EmbodimentController@getEmbodiedAnsarOfKpi']);
        Route::post('/complete_transfer_process', ['as' => 'complete_transfer_process', 'uses' => 'EmbodimentController@completeTransferProcess']);
        //Start Block and Black list for DG
        Route::get('/dg_blocklist_entry_view', ['as' => 'dg_blocklist_entry_view', 'uses' => 'DGController@blockListEntryView']);
        Route::get('/dg_blocklist_ansar_details', ['as' => 'dg_blocklist_ansar_details', 'uses' => 'DGController@loadAnsarDetailforBlock']);
        Route::post('/dg_blocklist_entry', ['as' => 'dg_blocklist_entry', 'uses' => 'DGController@blockListEntry']);

        Route::get('/dg_unblocklist_entry_view', ['as' => 'dg_unblocklist_entry_view', 'uses' => 'DGController@unblockListEntryView']);
        Route::get('/dg_unblocklist_ansar_details', ['as' => 'dg_unblocklist_ansar_details', 'uses' => 'DGController@loadAnsarDetailforUnblock']);
        Route::post('/dg_unblocklist_entry', ['as' => 'dg_unblocklist_entry', 'uses' => 'DGController@unblockListEntry']);

        Route::get('/dg_blacklist_entry_view', ['as' => 'dg_blacklist_entry_view', 'uses' => 'DGController@blackListEntryView']);
        Route::get('/dg_blacklist_ansar_details', ['as' => 'dg_blacklist_ansar_details', 'uses' => 'DGController@loadAnsarDetailforBlack']);
        Route::post('/dg_blacklist_entry', ['as' => 'dg_blacklist_entry', 'uses' => 'DGController@blackListEntry']);

        Route::get('/dg_unblacklist_entry_view', ['as' => 'dg_unblacklist_entry_view', 'uses' => 'DGController@unblackListEntryView']);
        Route::get('/dg_unblacklist_ansar_details', ['as' => 'dg_unblacklist_ansar_details', 'uses' => 'DGController@loadAnsarDetailforUnblack']);
        Route::post('/dg_unblacklist_entry', ['as' => 'dg_unblacklist_entry', 'uses' => 'DGController@unblackListEntry']);
//End Block and Black list for DG

//Letter route by Arafat
        Route::get('/transfer_letter_view', ['as' => 'transfer_letter_view', 'uses' => 'LetterController@transferLetterView']);
        Route::get('/embodiment_letter_view', ['as' => 'embodiment_letter_view', 'uses' => 'LetterController@embodimentLetterView']);
        Route::get('/disembodiment_letter_view', ['as' => 'disembodiment_letter_view', 'uses' => 'LetterController@disembodimentLetterView']);
        Route::post('/print_letter', ['as' => 'print_letter', 'uses' => 'LetterController@printLetter']);
        Route::get('/letter_data', ['as' => 'letter_data', 'uses' => 'LetterController@getMemorandumIds']);

        //REPORT ROUTE
        Route::get('/guard_report', ['as' => 'guard_report', 'uses' => 'ReportController@reportGuardSearchView']);
        Route::get('offer_report', ['as' => 'offer_report', 'uses' => 'ReportController@offerReportView']);
        Route::get('get_offered_ansar', ['as' => 'get_offered_ansar', 'uses' => 'ReportController@getOfferedAnsar']);
        Route::get('over_aged_ansar', ['as' => 'over_aged_ansar', 'uses' => 'ReportController@ansarOverAgedInfo']);
//        Route::any('unfrozen_report',['as'=>'unfrozen_report','uses'=>'ReportController@unfrozenAnsarReport']);
        //END REPORT ROUTE
//Start EmbodimentnewEmbodimentView
        Route::post('/save-bank-info', ['as' => 'save-bank-info', 'uses' => 'EmbodimentController@addNewBankAccount']);
        Route::get('/new_embodiment', ['as' => 'go_to_new_embodiment_page', 'uses' => 'EmbodimentController@newEmbodimentView']);
        Route::get('KPIName', ['as' => 'kpi_name', 'uses' => 'EmbodimentController@kpiName']);
        Route::get('/embodiment_view', ['as' => 'go_to_embodiment_view_page', 'uses' => 'EmbodimentController@embodimentListView']);
        Route::get('/disembodiment_view', ['as' => 'go_to_disembodiment_view_page', 'uses' => 'EmbodimentController@disembodimentListView']);
        Route::get('/check-ansar', ['as' => 'check-ansar', 'uses' => 'EmbodimentController@loadAnsarForEmbodiment']);
        Route::post('/new-embodiment-entry', ['as' => 'new-embodiment-entry', 'uses' => 'EmbodimentController@newEmbodimentEntry']);
        Route::post('/new-embodiment-entry-multiple', ['as' => 'new-embodiment-entry-multiple', 'uses' => 'EmbodimentController@newMultipleEmbodimentEntry']);
        Route::get('/new_disembodiment', ['as' => 'go_to_new_disembodiment_page', 'uses' => 'EmbodimentController@newDisembodimentView']);
        Route::get('/load_ansar', ['as' => 'load_ansar', 'uses' => 'EmbodimentController@loadAnsarForDisembodiment']);
        Route::get('/confirm_disembodiment', 'EmbodimentController@confirmDisembodiment');
        Route::post('/disembodiment-entry', ['as' => 'disembodiment-entry', 'uses' => 'EmbodimentController@disembodimentEntry']);
        Route::get('/service_extension_view', ['as' => 'service_extension_view', 'uses' => 'EmbodimentController@serviceExtensionView']);
        Route::get('/load_ansar_for_service_extension', ['as' => 'load_ansar_for_service_extension', 'uses' => 'EmbodimentController@loadAnsarDetail']);
        Route::post('/service_extension_entry', ['as' => 'service_extension_entry', 'uses' => 'EmbodimentController@serviceExtensionEntry']);
        Route::get('/get_ansar', 'EmbodimentController@getEmbodiedAnsarOfKpiV');
        Route::get('/download_bank_form/{id}', 'EmbodimentController@downloadBankForm');
        Route::get('/generate_bank_form', 'EmbodimentController@generateBankForm');
        Route::get('/make_zip_all_bank_form', 'EmbodimentController@makingZipAllBankForm');
        Route::get('/download_all_bank_form', 'EmbodimentController@downloadAllBankForm');
        Route::get('/bank_recipt', ['as' => 'bank_recipt', 'uses' => 'EmbodimentController@bankRecipt']);

        Route::any('/disembodied_period_correction', ['as' => 'disembodied_period_correction', 'uses' => 'EmbodimentController@loadDisembodiedAnsar']);

        Route::get('/disembodiment_date_correction_view', ['as' => 'disembodiment_date_correction_view', 'uses' => 'EmbodimentController@disembodimentDateCorrectionView']);
        Route::get('/embodiment_date_correction_view', ['as' => 'embodiment_date_correction_view', 'uses' => 'EmbodimentController@embodimentDateCorrectionView']);
        Route::get('/load_ansar_for_disembodiment_date_correction', ['as' => 'load_ansar_for_disembodiment_date_correction', 'uses' => 'EmbodimentController@loadAnsarForDisembodimentDateCorrection']);
        Route::get('/load_ansar_for_embodiment_date_correction', ['as' => 'load_ansar_for_embodiment_date_correction', 'uses' => 'EmbodimentController@loadAnsarEmbodimentDateCorrection']);
        Route::post('/new-disembodiment-date-entry', ['as' => 'new-disembodiment-date-entry', 'uses' => 'EmbodimentController@newDisembodimentDateEntry']);
        Route::post('/new-embodiment-date-entry', ['as' => 'new-embodiment-date-entry', 'uses' => 'EmbodimentController@newEmbodimentDateEntry']);
        Route::get('/kpi_detail', ['as' => 'kpi_detail', 'uses' => 'EmbodimentController@getKpiDetail']);
        Route::get('/embodiment_memorandum_id_correction_view', ['as' => 'embodiment_memorandum_id_correction_view', 'uses' => 'EmbodimentController@embodimentMemorandumIdCorrectionView']);
        Route::get('/load_ansar_for_embodiment_memorandum_id_correction', ['as' => 'load_ansar_for_embodiment_memorandum_id_correction', 'uses' => 'EmbodimentController@loadAnsarForEmbodimentMemorandumIdCorrection']);
        Route::post('/new_embodiment_memorandum_id_update', ['as' => 'new_embodiment_memorandum_id_update', 'uses' => 'EmbodimentController@newMemorandumIdCorrectionEntry']);
//End Embodiment
        Route::get('freeze_view', ['as' => 'freeze_view', 'uses' => 'FreezeController@freezeView']);
        Route::get('load_ansar_for_freeze', ['as' => 'load_ansar_for_freeze', 'uses' => 'FreezeController@loadAnsarDetailforFreeze']);
        Route::post('freeze_entry', ['as' => 'freeze_entry', 'uses' => 'FreezeController@freezeEntry']);
        //freeze list
        Route::get('freezelist', ['as' => 'freeze_list', 'uses' => 'FreezeController@freezeList']);
        Route::get('getfreezelist', ['as' => 'getfreezelist', 'uses' => 'FreezeController@getfreezelist']);
        Route::post('transfer_freezed_ansar', ['as' => 'transfer_freezed_ansar', 'uses' => 'FreezeController@transferFreezedAnsar']);
        //  reembodied
        Route::post('freezeRembodied/', ['as' => 'freezeRembodied', 'uses' => 'FreezeController@freezeRembodied']);
        //  disembodied
        Route::post('freezeDisEmbodied', ['as' => 'freezeDisEmbodied', 'uses' => 'FreezeController@freezeDisEmbodied']);
        //  Black from freeze
        Route::post('freezeblack', ['as' => 'freezeblack', 'uses' => 'FreezeController@freezeBlack']);
        //Start KPI
        Route::get('/blacklist_entry_view', ['as' => 'blacklist_entry_view', 'uses' => 'BlockBlackController@blackListEntryView']);
        Route::get('/blacklist_ansar_details', ['as' => 'blacklist_ansar_details', 'uses' => 'BlockBlackController@loadAnsarDetailforBlack']);
        Route::post('/blacklist_entry', ['as' => 'blacklist_entry', 'uses' => 'BlockBlackController@blackListEntry']);

        Route::get('/unblacklist_entry_view', ['as' => 'unblacklist_entry_view', 'uses' => 'BlockBlackController@unblackListEntryView']);
        Route::get('/unblacklist_ansar_details', ['as' => 'unblacklist_ansar_details', 'uses' => 'BlockBlackController@loadAnsarDetailforUnblack']);
        Route::post('/unblacklist_entry', ['as' => 'unblacklist_entry', 'uses' => 'BlockBlackController@unblackListEntry']);

        Route::get('/kpi', ['as' => 'go_to_kpi_page', 'uses' => 'KpiController@kpiIndex']);
        Route::get('/kpi_view', ['as' => 'kpi_view', 'uses' => 'KpiController@kpiView']);
        Route::get('/kpi_view_details', ['as' => 'kpi_view_details', 'uses' => 'KpiController@kpiViewDetails']);
        Route::post('/save-kpi', ['as' => 'save-kpi', 'uses' => 'KpiController@saveKpiInfo']);
        Route::get('/kpi-delete/{id}', ['as' => 'kpi_delete', 'uses' => 'KpiController@delete'])->where('id', '[0-9]+');
        Route::get('/kpi-edit/{id}', ['as' => 'Kpi_edit', 'uses' => 'KpiController@edit'])->where('id', '[0-9]+');
        Route::get('/kpi_verify/{id}', ['as' => 'kpi_verify', 'uses' => 'KpiController@kpiVerify'])->where('id', '[0-9]+');

        Route::get('/ansar-withdraw-view', ['as' => 'ansar-withdraw-view', 'uses' => 'KpiController@ansarWithdrawView']);
        Route::get('/ansar_list_for_withdraw', ['as' => 'ansar_list_for_withdraw', 'uses' => 'KpiController@ansarListForWithdraw']);

        Route::get('/ansar_before_withdraw_view', ['as' => 'ansar_before_withdraw_view', 'uses' => 'KpiController@guardBeforeWithdrawView']);
        Route::get('/load_ansar_before_withdraw', ['as' => 'load_ansar_before_withdraw', 'uses' => 'KpiController@loadAnsarsForBeforeWithdraw']);

        Route::get('/reduce_guard_strength', ['as' => 'reduce_guard_strength', 'uses' => 'KpiController@reduceGuardStrength']);
        Route::get('/ansar_list_for_reduce', ['as' => 'ansar_list_for_reduce', 'uses' => 'KpiController@ansarListForReduce']);

        Route::get('/ansar_before_reduce_view', ['as' => 'ansar_before_reduce_view', 'uses' => 'KpiController@guardBeforeReduceView']);
        Route::get('/load_ansar_before_reduce', ['as' => 'load_ansar_before_reduce', 'uses' => 'KpiController@loadAnsarsForBeforeReduce']);

        Route::post('/ansar-withdraw-update', ['as' => 'ansar-withdraw-update', 'uses' => 'KpiController@ansarWithdrawUpdate']);
        Route::post('/kpi-update', ['as' => 'kpi-update', 'uses' => 'KpiController@updateKpi']);
        Route::post('/ansar-reduce-update', ['as' => 'ansar-reduce-update', 'uses' => 'KpiController@ansarReduceUpdate']);

        Route::get('/kpi-withdraw-view', ['as' => 'kpi-withdraw-view', 'uses' => 'KpiController@kpiWithdrawView']);
        Route::get('/kpi-withdraw-action-view', ['as' => 'kpi-withdraw-action-view', 'uses' => 'KpiController@kpiWithdrawActionView']);
        Route::get('/kpiinfo/{id}', ['as' => 'single-kpi-info', 'uses' => 'KpiController@singleKpiInfo']);
        Route::get('/kpi_list_for_withdraw', ['as' => 'kpi_list_for_withdraw', 'uses' => 'KpiController@loadKpiForWithdraw']);
        Route::post('/kpi-withdraw-update/{id}', ['as' => 'kpi_withdraw_update', 'uses' => 'KpiController@kpiWithdrawUpdate'])->where('id', '^[0-9]+$');

        Route::get('/withdrawn_kpi_view', ['as' => 'withdrawn_kpi_view', 'uses' => 'KpiController@withdrawnKpiView']);
        Route::get('/withdrawn_kpi_list', ['as' => 'withdrawn_kpi_list', 'uses' => 'KpiController@withdrawnKpiList']);
        Route::get('/withdraw-date-edit/{id}', ['as' => 'withdraw-date-edit', 'uses' => 'KpiController@kpiWithdrawDateEdit'])->where('id', '^[0-9]+$');
        Route::post('/withdraw-date-update/{id}', ['as' => 'withdraw-date-update', 'uses' => 'KpiController@kpiWithdrawDateUpdate'])->where('id', '^[0-9]+$');
        Route::get('/inactive_kpi_view', ['as' => 'inactive_kpi_view', 'uses' => 'KpiController@inactiveKpiView']);
        Route::get('/inactive_kpi_list', ['as' => 'inactive_kpi_list', 'uses' => 'KpiController@inactiveKpiList']);
        Route::post('/active_kpi/{id}', ['as' => 'active_kpi', 'uses' => 'KpiController@activeKpi'])->where('id', '[0-9]+');

        Route::get('/withdrawn_kpi_name', ['as' => 'withdrawn_kpi_name', 'uses' => 'KpiController@withdrawnKpiName']);
        Route::get('/kpi_withdraw_cancel_view', ['as' => 'kpi_withdraw_cancel_view', 'uses' => 'KpiController@kpiWithdrawCancelView']);
        Route::get('/kpi_list_for_withdraw_cancel', ['as' => 'kpi_list_for_withdraw_cancel', 'uses' => 'KpiController@kpiListForWithdrawCancel']);
        Route::post('/kpi-withdraw-cancel-update/{id}', ['as' => 'kpi-withdraw-cancel-update', 'uses' => 'KpiController@kpiWithdrawCancelUpdate'])->where('id', '^[0-9]+$');
//End KPI
        Route::post('upload_original_info', ['as' => 'upload_original_info', 'uses' => 'GeneralSettingsController@uploadOriginalInfo']);
        Route::get('upload_original_info', ['as' => 'upload_original_info_view', 'uses' => 'GeneralSettingsController@uploadOriginalInfoView']);
        Route::any('test', function (\Illuminate\Http\Request $request) {

                if(!strcasecmp($request->method(),"get")){
                    return view("HRM::bank.upload_ansar_avub_share_id");
                }else if(!strcasecmp($request->method(),"post")){
                    $file = $request->file("shareIdFile");
                    $result = [];
                    \Maatwebsite\Excel\Facades\Excel::load($file,function($reader) use(&$result){
                       $result = collect($reader->limitColumns(2)->first())->toArray();
                    });
                    ob_implicit_flush(true);
                    ob_end_flush();
                    echo "Start Processing....</br>";
                    $i=1;
                    foreach($result as $r){
                        echo "processed {$i}:{$r[1]}:{$r[0]}</br>";
                        $ansar = \App\modules\HRM\Models\PersonalInfo::where('ansar_id',$r[1])->first();
                        if($ansar){
                            $ansar->avub_share_id = $r[0];
                            $ansar->save();
                        }
                        $i++;
                    }
                    return $result;
                }
        });
        Route::get('manual_offer_to_panel', function () {
            Log::info("called : Ansar Block For Age");

            DB::connection('hrm')->beginTransaction();
            try {
                $ansars = \App\modules\HRM\Models\SmsReceiveInfoModel::whereIn('ansar_id',[4354,20172,24046,26377,35415]);
                $s_ansar = \App\modules\HRM\Models\AnsarStatusInfo::whereIn('ansar_id',[4354,20172,24046,26377,35415]);
                $p_ansars = \App\modules\HRM\Models\PanelInfoLogModel::whereIn('ansar_id',[4354,20172,24046,26377,35415])->groupBy('ansar_id')->orderBy('panel_date','desc')->get();
                foreach ($p_ansars as $id){

                    $panel_entry = new PanelModel;
                    $panel_entry->ansar_id = $id->ansar_id;
                    $panel_entry->come_from = "Offer";
                    $panel_entry->panel_date = $id->panel_date;
                    $panel_entry->memorandum_id = $id->old_memorandum_id;
                    $panel_entry->ansar_merit_list = $id->merit_list;
                    $panel_entry->action_user_id = Auth::user()->id;
                    $panel_entry->save();
                }
                foreach ($s_ansar as $id){

                    $id->offer_sms_status = 0;
                    $id->pannel_status = 1;
                    $id->save();
                }
                foreach ($ansars as $id){

                    $id->saveLog();
                }
                echo "done!!!";
                DB::connection('hrm')->commit();
            }catch(\Exception $e){
                Log::info("ansar_block_for_age:".$e->getMessage());
                DB::connection('hrm')->rollback();
            }
        });
        Route::resource('retire_ansar_management','RetireAnsarManagementController',['only'=>['index','update']]);

        Route::any('/bulk-upload-bank-info', ['as' => "bulk_upload_bank_file", 'uses' => "EntryFormController@bulkUploadBankInfo"]);

        Route::get('/export_data_for_bank', ['as' => 'export_data_for_bank', 'uses' => 'EntryFormController@exportDataForBank']);
    });
    Route::get('/view_profile/{id}', '\App\Http\Controllers\UserController@viewProfile');
    Route::get('/all_notification', function () {
        return view('all_notification');
    });
    Route::get('/change_password/{user}', '\App\Http\Controllers\UserController@changeForgetPassword');
    Route::get('/remove_request/{user}', '\App\Http\Controllers\UserController@removePasswordRequest');
});
