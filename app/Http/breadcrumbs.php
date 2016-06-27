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
//Breadcrumbs::register('kpi_list', function($breadcrumbs) {
//    $breadcrumbs->parent('home');
//    $breadcrumbs->push('KPI Information', URL::route('kpi_view'));
//});
//Breadcrumbs::register('kpi', function($breadcrumbs) {
//    $breadcrumbs->parent('kpi_list');
//    $breadcrumbs->push('Add New KPI', URL::to('kpi'));
//});
//Breadcrumbs::register('kpi_edit', function($breadcrumbs) {
//    $breadcrumbs->parent('kpi_list');
//    $breadcrumbs->push('KPI Update', URL::to('kpi_edit'));
//});
//Breadcrumbs::register('ansar_withdraw_view', function($breadcrumbs) {
//    $breadcrumbs->parent('home');
//    $breadcrumbs->push('Ansar Withdraw', URL::to('ansar-withdraw-view'));
//});
//Breadcrumbs::register('ansar_before_withdraw_list', function($breadcrumbs) {
//    $breadcrumbs->parent('home');
//    $breadcrumbs->push('Ansar Before Withdraw List', URL::to('ansar_before_withdraw_view'));
//});
//Breadcrumbs::register('reduce_guard_strength', function($breadcrumbs) {
//    $breadcrumbs->parent('home');
//    $breadcrumbs->push('Reduce Guard Strength', URL::to('reduce_guard_strength'));
//});
//Breadcrumbs::register('ansar_before_reduce_list', function($breadcrumbs) {
//    $breadcrumbs->parent('home');
//    $breadcrumbs->push('Ansar Before Reduce List', URL::to('ansar_before_reduce_view'));
//});
//Breadcrumbs::register('withdraw_kpi', function($breadcrumbs) {
//    $breadcrumbs->parent('home');
//    $breadcrumbs->push('KPI Withdraw', URL::to('kpi-withdraw-view'));
//});
//Breadcrumbs::register('withdrawn_kpi_list', function($breadcrumbs) {
//    $breadcrumbs->parent('home');
//    $breadcrumbs->push('KPI Withdraw Date Update', URL::to('withdrawn_kpi_view'));
//});
//Breadcrumbs::register('withdraw_date_update', function($breadcrumbs) {
//    $breadcrumbs->parent('withdrawn_kpi_list');
//    $breadcrumbs->push('Update Withdraw Date', URL::to('withdraw-date-edit'));
//});
//Breadcrumbs::register('inactive_kpi_list', function($breadcrumbs) {
//    $breadcrumbs->parent('home');
//    $breadcrumbs->push('Inactive KPI List', URL::to('inactive_kpi_view'));
//});
////Personal Info
//Breadcrumbs::register('entry_list', function($breadcrumbs) {
//    $breadcrumbs->parent('home');
//    $breadcrumbs->push('Entry List', URL::to('entrylist'));
//});
//Breadcrumbs::register('entryform', function($breadcrumbs) {
//    $breadcrumbs->parent('entry_list');
//    $breadcrumbs->push('Add new entry', URL::to('entryform'));
//});
//Breadcrumbs::register('editentryform', function($breadcrumbs) {
//    $breadcrumbs->parent('entry_list');
//    $breadcrumbs->push('Update entry', URL::to('editEntry'));
//});
//Breadcrumbs::register('chunk_verification', function($breadcrumbs) {
//    $breadcrumbs->parent('home');
//    $breadcrumbs->push('Verify Entry(Chunk)', URL::to('chunkverify'));
//});
//Breadcrumbs::register('draft_entry', function($breadcrumbs) {
//    $breadcrumbs->parent('home');
//    $breadcrumbs->push('Draft entry', URL::to('entrydraft'));
//});
//Breadcrumbs::register('entryadvancedsearch', function($breadcrumbs) {
//    $breadcrumbs->parent('home');
//    $breadcrumbs->push('Advanced search', URL::to('entryadvancedsearch'));
//});
//Breadcrumbs::register('print_card_id_view', function($breadcrumbs) {
//    $breadcrumbs->parent('home');
//    $breadcrumbs->push('Print ID card', URL::to('print_card_id_view'));
//});
////Service
////Panel
//Breadcrumbs::register('panel_information', function($breadcrumbs) {
//    $breadcrumbs->parent('home');
//    $breadcrumbs->push('Panel', URL::to('panel_view'));
//});
////Offer
//Breadcrumbs::register('offer_information', function($breadcrumbs) {
//    $breadcrumbs->parent('home');
//    $breadcrumbs->push('Offer', URL::to('make_offer'));
//});
//Breadcrumbs::register('offer_quota', function($breadcrumbs) {
//    $breadcrumbs->parent('home');
//    $breadcrumbs->push('Offer Quota', URL::to('offer_quota'));
//});
////Embodiment
//Breadcrumbs::register('embodiment', function($breadcrumbs) {
//    $breadcrumbs->parent('home');
//    $breadcrumbs->push('Embodiment', URL::to('#'));
//});
//Breadcrumbs::register('embodiment_entry', function($breadcrumbs) {
//    $breadcrumbs->parent('embodiment');
//    $breadcrumbs->push('Embodiment Entry', URL::to('new_embodiment'));
//});
//Breadcrumbs::register('disembodiment_entry', function($breadcrumbs) {
//    $breadcrumbs->parent('embodiment');
//    $breadcrumbs->push('Dis-Embodiment', URL::to('new_disembodiment'));
//});
//Breadcrumbs::register('service_extension', function($breadcrumbs) {
//    $breadcrumbs->parent('embodiment');
//    $breadcrumbs->push('Service Extension', URL::to('service_extension_view'));
//});
//Breadcrumbs::register('disembodiment_date_correction', function($breadcrumbs) {
//    $breadcrumbs->parent('embodiment');
//    $breadcrumbs->push('Disembodiment Date Correction', URL::to('disembodiment_date_correction_view'));
//});
//Breadcrumbs::register('freeze', function($breadcrumbs) {
//    $breadcrumbs->parent('embodiment');
//    $breadcrumbs->push('Freeze', URL::to('#'));
//});
//Breadcrumbs::register('freeze_view', function($breadcrumbs) {
//    $breadcrumbs->parent('freeze');
//    $breadcrumbs->push('Freeze for Disciplinary Action', URL::to('freeze_view'));
//});
//Breadcrumbs::register('freezelist', function($breadcrumbs) {
//    $breadcrumbs->parent('freeze');
//    $breadcrumbs->push('After Result of Freeze', URL::to('freezelist'));
//});
////Blocklist
//Breadcrumbs::register('blocklist', function($breadcrumbs) {
//    $breadcrumbs->parent('home');
//    $breadcrumbs->push('Blocklist', URL::to('#'));
//});
//Breadcrumbs::register('add_to_blocklist', function($breadcrumbs) {
//    $breadcrumbs->parent('blocklist');
//    $breadcrumbs->push('Add Ansar in Blocklist', URL::to('blocklist_entry_view'));
//});
//Breadcrumbs::register('cancel_blocklist', function($breadcrumbs) {
//    $breadcrumbs->parent('blocklist');
//    $breadcrumbs->push('Remove Ansar from Blocklist', URL::to('unblocklist_entry_view'));
//});
////Blacklist
//Breadcrumbs::register('blacklist', function($breadcrumbs) {
//    $breadcrumbs->parent('home');
//    $breadcrumbs->push('Blacklist', URL::to('#'));
//});
//Breadcrumbs::register('add_to_blacklist', function($breadcrumbs) {
//    $breadcrumbs->parent('blacklist');
//    $breadcrumbs->push('Add Ansar in Blacklist', URL::to('blacklist_entry_view'));
//});
//Breadcrumbs::register('cancel_blacklist', function($breadcrumbs) {
//    $breadcrumbs->parent('blacklist');
//    $breadcrumbs->push('Remove Ansar from Blacklist', URL::to('unblacklist_entry_view'));
//});
////Transfer
//Breadcrumbs::register('transfer', function($breadcrumbs) {
//    $breadcrumbs->parent('home');
//    $breadcrumbs->push('Ansar Transfer', URL::to('transfer_process'));
//});
//Report


//DG Forms


//Admin


//General Setting