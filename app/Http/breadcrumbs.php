<?php

//Home
Breadcrumbs::register('home', function($breadcrumbs) {
    $breadcrumbs->push('Home', URL::to('/'));
});
Breadcrumbs::register('hrm', function($breadcrumbs) {
    $breadcrumbs->parent('home');
    $breadcrumbs->push('HRM', URL::to('HRM'));
});
Breadcrumbs::register('dashboard_menu', function($breadcrumbs,$title,$type) {
    $breadcrumbs->parent('hrm');
    $breadcrumbs->push($title, URL::route('show_ansar_list',['type'=>$type]));
});
Breadcrumbs::register('dashboard_menu_recent', function($breadcrumbs,$title,$type) {
    $breadcrumbs->parent('hrm');
    $breadcrumbs->push($title, URL::route('show_recent_ansar_list',['type'=>$type]));
});
Breadcrumbs::register('dashboard_menu_service_ended_2_month', function($breadcrumbs,$total) {
    $breadcrumbs->parent('hrm');
    $breadcrumbs->push('Ansars whom service ended 2 month', URL::route('service_ended_in_three_years',['count'=>$total]));
});
Breadcrumbs::register('dashboard_menu_50_year', function($breadcrumbs,$total) {
    $breadcrumbs->parent('hrm');
    $breadcrumbs->push('Ansars will reached 50 yeaars within 3 months', URL::route('ansar_reached_fifty_years',['count'=>$total]));
});
Breadcrumbs::register('dashboard_menu_not_interested', function($breadcrumbs,$total) {
    $breadcrumbs->parent('hrm');
    $breadcrumbs->push('Ansars not interested more then 10 times', URL::route('ansar_not_interested',['count'=>$total]));
});

//KPI Branch
Breadcrumbs::register('kpi_view', function($breadcrumbs) {
    $breadcrumbs->parent('hrm');
    $breadcrumbs->push('KPI Information', URL::route('kpi_view'));
});
Breadcrumbs::register('new_kpi', function($breadcrumbs) {
    $breadcrumbs->parent('kpi_view');
    $breadcrumbs->push('Add New KPI', URL::route('go_to_kpi_page'));
});
Breadcrumbs::register('kpi_edit', function($breadcrumbs,$id) {
    $breadcrumbs->parent('kpi_view');
    $breadcrumbs->push('KPI Update', URL::route('Kpi_edit',['id'=>$id]));
});
Breadcrumbs::register('ansar_withdraw_view', function($breadcrumbs) {
    $breadcrumbs->parent('hrm');
    $breadcrumbs->push('Ansar Withdraw', URL::route('ansar-withdraw-view'));
});
Breadcrumbs::register('ansar_before_withdraw_list', function($breadcrumbs) {
    $breadcrumbs->parent('hrm');
    $breadcrumbs->push('Ansar Before Withdraw List', URL::route('ansar_before_withdraw_view'));
});
Breadcrumbs::register('reduce_guard_strength', function($breadcrumbs) {
    $breadcrumbs->parent('hrm');
    $breadcrumbs->push('Reduce Guard Strength', URL::route('reduce_guard_strength'));
});
Breadcrumbs::register('ansar_before_reduce_list', function($breadcrumbs) {
    $breadcrumbs->parent('hrm');
    $breadcrumbs->push('Ansar Before Reduce List', URL::route('ansar_before_reduce_view'));
});
Breadcrumbs::register('withdraw_kpi', function($breadcrumbs) {
    $breadcrumbs->parent('hrm');
    $breadcrumbs->push('KPI Withdraw', URL::route('kpi-withdraw-view'));
});
Breadcrumbs::register('withdrawn_kpi_list', function($breadcrumbs) {
    $breadcrumbs->parent('hrm');
    $breadcrumbs->push('KPI Withdraw Date Update', URL::route('withdrawn_kpi_view'));
});
Breadcrumbs::register('kpi_withdraw_cancel', function($breadcrumbs) {
    $breadcrumbs->parent('hrm');
    $breadcrumbs->push('Kpi Withdraw Cancel', URL::to('kpi_withdraw_cancel_view'));
});
Breadcrumbs::register('inactive_kpi_list', function($breadcrumbs) {
    $breadcrumbs->parent('hrm');
    $breadcrumbs->push('Inactive KPI List', URL::route('inactive_kpi_view'));
});
//Personal Info
Breadcrumbs::register('entry_list', function($breadcrumbs) {
    $breadcrumbs->parent('hrm');
    $breadcrumbs->push('Entry List', URL::route('anser_list'));
});
Breadcrumbs::register('entry_report', function($breadcrumbs,$id) {
    $breadcrumbs->parent('entry_list');
    $breadcrumbs->push('Entry Report', URL::route('entry_report',['id'=>$id]));
});

Breadcrumbs::register('entryform', function($breadcrumbs) {
    $breadcrumbs->parent('entry_list');
    $breadcrumbs->push('Add new entry', URL::route('ansar_registration'));
});
Breadcrumbs::register('entry_edit', function($breadcrumbs,$id) {
    $breadcrumbs->parent('entry_list');
    $breadcrumbs->push('Edit entry', URL::route('editentry',['ansarid'=>$id]));
});
Breadcrumbs::register('chunk_verification', function($breadcrumbs) {
    $breadcrumbs->parent('hrm');
    $breadcrumbs->push('Verify Entry(Chunk)', URL::route('chunk_verify'));
});
Breadcrumbs::register('draft_list', function($breadcrumbs) {
    $breadcrumbs->parent('hrm');
    $breadcrumbs->push('Entry Draft List', URL::route('entry_draft'));
});

Breadcrumbs::register('draft_edit', function($breadcrumbs,$id) {
    $breadcrumbs->parent('draft_list');
    $breadcrumbs->push('Edit Draft', URL::route('draftEdit',['id'=>$id]));
});
Breadcrumbs::register('orginal_info', function($breadcrumbs) {
    $breadcrumbs->parent('hrm');
    $breadcrumbs->push('Orginal Info', URL::route('orginal_info'));
});

Breadcrumbs::register('entryadvancedsearch', function($breadcrumbs) {
    $breadcrumbs->parent('hrm');
    $breadcrumbs->push('Advanced search', URL::route('entry_advanced_search'));
});
Breadcrumbs::register('print_card_id_view', function($breadcrumbs) {
    $breadcrumbs->parent('hrm');
    $breadcrumbs->push('Print ID card', URL::route('print_card_id_view'));
});
Breadcrumbs::register('all_user', function($breadcrumbs) {
    $breadcrumbs->parent('home');
    $breadcrumbs->push('User Management', URL::route('view_user_list'));
});
Breadcrumbs::register('edit_user', function($breadcrumbs,$id) {
    $breadcrumbs->parent('all_user');
    $breadcrumbs->push('Edit User', URL::route('edit_user',['id'=>$id]));
});
Breadcrumbs::register('user_permission', function($breadcrumbs,$id) {
    $breadcrumbs->parent('all_user');
    $breadcrumbs->push('User Permission', URL::route('edit_user_permission',['id'=>$id]));
});

////Service
////Panel
Breadcrumbs::register('panel_information', function($breadcrumbs) {
    $breadcrumbs->parent('hrm');
    $breadcrumbs->push('Panel', URL::route('view_panel_list'));
});
////Offer
Breadcrumbs::register('offer_information', function($breadcrumbs) {
    $breadcrumbs->parent('hrm');
    $breadcrumbs->push('Offer', URL::route('make_offer'));
});
Breadcrumbs::register('offer_quota', function($breadcrumbs) {
    $breadcrumbs->parent('hrm');
    $breadcrumbs->push('Offer Quota', URL::route('offer_quota'));
});
////Embodiment
//Breadcrumbs::register('embodiment', function($breadcrumbs) {
//    $breadcrumbs->parent('home');
//    $breadcrumbs->push('Embodiment', URL::to('#'));
//});
Breadcrumbs::register('embodiment_entry', function($breadcrumbs) {
    $breadcrumbs->parent('hrm');
    $breadcrumbs->push('Embodiment Entry', URL::route('go_to_new_embodiment_page'));
});
Breadcrumbs::register('disembodiment_entry', function($breadcrumbs) {
    $breadcrumbs->parent('hrm');
    $breadcrumbs->push('Dis-Embodiment Entry', URL::route('go_to_new_disembodiment_page'));
});
Breadcrumbs::register('service_extension', function($breadcrumbs) {
    $breadcrumbs->parent('hrm');
    $breadcrumbs->push('Service Extension', URL::route('service_extension_view'));
});
Breadcrumbs::register('disembodiment_date_correction', function($breadcrumbs) {
    $breadcrumbs->parent('hrm');
    $breadcrumbs->push('Disembodiment Date Correction', URL::to('disembodiment_date_correction_view'));
});
Breadcrumbs::register('embodiment_memorandum_id_correction_view', function($breadcrumbs) {
    $breadcrumbs->parent('hrm');
    $breadcrumbs->push('Embodiment Memorandum Id Correction', URL::to('embodiment_memorandum_id_correction_view'));
});

Breadcrumbs::register('freeze', function($breadcrumbs) {
    $breadcrumbs->parent('hrm');
    $breadcrumbs->push('Freeze for disciplinary action', URL::route('freeze_view'));
});
//Breadcrumbs::register('freeze_view', function($breadcrumbs) {
//    $breadcrumbs->parent('freeze');
//    $breadcrumbs->push('Freeze for Disciplinary Action', URL::to('freeze_view'));
//});
Breadcrumbs::register('freezelist', function($breadcrumbs) {
    $breadcrumbs->parent('hrm');
    $breadcrumbs->push('After Result of Freeze', URL::route('freeze_list'));
});
////Blocklist
//Breadcrumbs::register('blocklist', function($breadcrumbs) {
//    $breadcrumbs->parent('home');
//    $breadcrumbs->push('Blocklist', URL::to('#'));
//});
Breadcrumbs::register('add_to_blocklist', function($breadcrumbs) {
    $breadcrumbs->parent('hrm');
    $breadcrumbs->push('Ansar blocklist entry', URL::route('blocklist_entry_view'));
});
Breadcrumbs::register('unblock_ansar', function($breadcrumbs) {
    $breadcrumbs->parent('hrm');
    $breadcrumbs->push('Remove Ansar from Blocklist', URL::route('unblocklist_entry_view'));
});
////Blacklist
//Breadcrumbs::register('blacklist', function($breadcrumbs) {
//    $breadcrumbs->parent('home');
//    $breadcrumbs->push('Blacklist', URL::to('#'));
//});
Breadcrumbs::register('add_to_blacklist', function($breadcrumbs) {
    $breadcrumbs->parent('hrm');
    $breadcrumbs->push('Add Ansar in Blacklist', URL::route('blacklist_entry_view'));
});
Breadcrumbs::register('cancel_blacklist', function($breadcrumbs) {
    $breadcrumbs->parent('hrm');
    $breadcrumbs->push('Remove Ansar from Blacklist', URL::route('unblacklist_entry_view'));
});
////Transfer
Breadcrumbs::register('transfer', function($breadcrumbs) {
    $breadcrumbs->parent('hrm');
    $breadcrumbs->push('Ansar Transfer', URL::route('transfer_process'));
});
//Report


//DG Forms


//Admin


//General Setting
Breadcrumbs::register('session_information_list', function($breadcrumbs) {
    $breadcrumbs->parent('hrm');
    $breadcrumbs->push('Session Information List', URL::route('view_session_list'));
});
Breadcrumbs::register('session_information_edit', function($breadcrumbs,$id,$page) {
    $breadcrumbs->parent('hrm');
    $breadcrumbs->push('Session Information Edit', URL::route('edit_session',['id'=>$id, 'page'=>$page]));
});
Breadcrumbs::register('session_information_entry', function($breadcrumbs) {
    $breadcrumbs->parent('hrm');
    $breadcrumbs->push('Session Information Entry', URL::route('create_session'));
});

Breadcrumbs::register('unit_information_list', function($breadcrumbs) {
    $breadcrumbs->parent('hrm');
    $breadcrumbs->push('Unit Information Entry', URL::route('unit_view'));
});