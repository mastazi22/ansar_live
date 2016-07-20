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
    $breadcrumbs->push('Progress Information', URL::route('service_ended_in_three_years',['count'=>$total]));
});
Breadcrumbs::register('dashboard_menu_50_year', function($breadcrumbs,$total) {
    $breadcrumbs->parent('hrm');
    $breadcrumbs->push('Progress Information', URL::route('ansar_reached_fifty_years',['count'=>$total]));
});
Breadcrumbs::register('dashboard_menu_not_interested', function($breadcrumbs,$total) {
    $breadcrumbs->parent('hrm');
    $breadcrumbs->push('Progress Information', URL::route('ansar_not_interested',['count'=>$total]));
});

//KPI Branch
Breadcrumbs::register('kpi', function($breadcrumbs) {
    $breadcrumbs->parent('hrm');
    $breadcrumbs->push('KPI Branch', '#');
});
Breadcrumbs::register('kpi_view', function($breadcrumbs) {
    $breadcrumbs->parent('kpi');
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
    $breadcrumbs->parent('kpi');
    $breadcrumbs->push('Ansar Withdraw', URL::route('ansar-withdraw-view'));
});
Breadcrumbs::register('ansar_before_withdraw_list', function($breadcrumbs) {
    $breadcrumbs->parent('kpi');
    $breadcrumbs->push('Ansar List Before Withdrawal', URL::route('ansar_before_withdraw_view'));
});
Breadcrumbs::register('reduce_guard_strength', function($breadcrumbs) {
    $breadcrumbs->parent('kpi');
    $breadcrumbs->push('Reduce Guard Strength', URL::route('reduce_guard_strength'));
});
Breadcrumbs::register('ansar_before_reduce_list', function($breadcrumbs) {
    $breadcrumbs->parent('kpi');
    $breadcrumbs->push('Ansar List Before Reduction', URL::route('ansar_before_reduce_view'));
});
Breadcrumbs::register('withdraw_kpi', function($breadcrumbs) {
    $breadcrumbs->parent('kpi');
    $breadcrumbs->push('KPI Withdraw', URL::route('kpi-withdraw-view'));
});
Breadcrumbs::register('withdrawn_kpi_list', function($breadcrumbs) {
    $breadcrumbs->parent('kpi');
    $breadcrumbs->push('KPI Withdraw Date Update', URL::route('withdrawn_kpi_view'));
});
Breadcrumbs::register('kpi_withdraw_cancel', function($breadcrumbs) {
    $breadcrumbs->parent('kpi');
    $breadcrumbs->push('Kpi Withdraw Cancel', URL::to('kpi_withdraw_cancel_view'));
});
Breadcrumbs::register('inactive_kpi_list', function($breadcrumbs) {
    $breadcrumbs->parent('kpi');
    $breadcrumbs->push('Inactive KPI List', URL::route('inactive_kpi_view'));
});
//Personal Info
Breadcrumbs::register('pi', function($breadcrumbs) {
    $breadcrumbs->parent('hrm');
    $breadcrumbs->push('Personal Info', '#');
});
Breadcrumbs::register('entry_list', function($breadcrumbs) {
    $breadcrumbs->parent('pi');
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
    $breadcrumbs->parent('pi');
    $breadcrumbs->push('Verify Entry(Chunk)', URL::route('chunk_verify'));
});
Breadcrumbs::register('draft_list', function($breadcrumbs) {
    $breadcrumbs->parent('pi');
    $breadcrumbs->push('Entry Draft List', URL::route('entry_draft'));
});

Breadcrumbs::register('draft_edit', function($breadcrumbs,$id) {
    $breadcrumbs->parent('draft_list');
    $breadcrumbs->push('Edit Draft', URL::route('draftEdit',['id'=>$id]));
});
Breadcrumbs::register('orginal_info', function($breadcrumbs) {
    $breadcrumbs->parent('pi');
    $breadcrumbs->push('Orginal Info', URL::route('orginal_info'));
});

Breadcrumbs::register('entryadvancedsearch', function($breadcrumbs) {
    $breadcrumbs->parent('pi');
    $breadcrumbs->push('Advanced search', URL::route('entry_advanced_search'));
});
Breadcrumbs::register('print_card_id_view', function($breadcrumbs) {
    $breadcrumbs->parent('pi');
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
Breadcrumbs::register('service', function($breadcrumbs) {
    $breadcrumbs->parent('hrm');
    $breadcrumbs->push('Service', "#");
});
////Panel
Breadcrumbs::register('panel_information', function($breadcrumbs) {
    $breadcrumbs->parent('service');
    $breadcrumbs->push('Panel', URL::route('view_panel_list'));
});
////Offer
Breadcrumbs::register('offer_information', function($breadcrumbs) {
    $breadcrumbs->parent('service');
    $breadcrumbs->push('Offer', URL::route('make_offer'));
});
Breadcrumbs::register('offer_quota', function($breadcrumbs) {
    $breadcrumbs->parent('service');
    $breadcrumbs->push('Offer Quota', URL::route('offer_quota'));
});
////Embodiment
Breadcrumbs::register('embodiment', function($breadcrumbs) {
    $breadcrumbs->parent('service');
    $breadcrumbs->push('Embodiment', URL::to('#'));
});
Breadcrumbs::register('embodiment_entry', function($breadcrumbs) {
    $breadcrumbs->parent('embodiment');
    $breadcrumbs->push('Embodiment Entry', URL::route('go_to_new_embodiment_page'));
});
Breadcrumbs::register('disembodiment_entry', function($breadcrumbs) {
    $breadcrumbs->parent('embodiment');
    $breadcrumbs->push('Dis-Embodiment Entry', URL::route('go_to_new_disembodiment_page'));
});
Breadcrumbs::register('service_extension', function($breadcrumbs) {
    $breadcrumbs->parent('embodiment');
    $breadcrumbs->push('Service Extension', URL::route('service_extension_view'));
});
Breadcrumbs::register('disembodiment_date_correction', function($breadcrumbs) {
    $breadcrumbs->parent('embodiment');
    $breadcrumbs->push('Disembodiment Date Correction', URL::to('disembodiment_date_correction_view'));
});
Breadcrumbs::register('embodiment_memorandum_id_correction_view', function($breadcrumbs) {
    $breadcrumbs->parent('embodiment');
    $breadcrumbs->push('Embodiment Memorandum Id Correction', URL::to('embodiment_memorandum_id_correction_view'));
});
Breadcrumbs::register('fr', function($breadcrumbs) {
    $breadcrumbs->parent('hrm');
    $breadcrumbs->push('Freez', '#');
});
Breadcrumbs::register('freeze', function($breadcrumbs) {
    $breadcrumbs->parent('fr');
    $breadcrumbs->push('Freeze for disciplinary action', URL::route('freeze_view'));
});
//Breadcrumbs::register('freeze_view', function($breadcrumbs) {
//    $breadcrumbs->parent('freeze');
//    $breadcrumbs->push('Freeze for Disciplinary Action', URL::to('freeze_view'));
//});
Breadcrumbs::register('freezelist', function($breadcrumbs) {
    $breadcrumbs->parent('fr');
    $breadcrumbs->push('After Result of Freeze', URL::route('freeze_list'));
});
////Blocklist
Breadcrumbs::register('blocklist', function($breadcrumbs) {
    $breadcrumbs->parent('hrm');
    $breadcrumbs->push('Blocklist', URL::to('#'));
});
Breadcrumbs::register('add_to_blocklist', function($breadcrumbs) {
    $breadcrumbs->parent('blocklist');
    $breadcrumbs->push('Ansar blocklist entry', URL::route('blocklist_entry_view'));
});
Breadcrumbs::register('unblock_ansar', function($breadcrumbs) {
    $breadcrumbs->parent('blocklist');
    $breadcrumbs->push('Remove Ansar from Blocklist', URL::route('unblocklist_entry_view'));
});
////Blacklist
Breadcrumbs::register('blacklist', function($breadcrumbs) {
    $breadcrumbs->parent('hrm');
    $breadcrumbs->push('Blacklist', URL::to('#'));
});
Breadcrumbs::register('add_to_blacklist', function($breadcrumbs) {
    $breadcrumbs->parent('blacklist');
    $breadcrumbs->push('Add Ansar in Blacklist', URL::route('blacklist_entry_view'));
});
Breadcrumbs::register('cancel_blacklist', function($breadcrumbs) {
    $breadcrumbs->parent('blacklist');
    $breadcrumbs->push('Remove Ansar from Blacklist', URL::route('unblacklist_entry_view'));
});
////Transfer
Breadcrumbs::register('transfer', function($breadcrumbs) {
    $breadcrumbs->parent('service');
    $breadcrumbs->push('Ansar Transfer', URL::route('transfer_process'));
});
//Report
Breadcrumbs::register('report', function($breadcrumbs) {
    $breadcrumbs->parent('hrm');
    $breadcrumbs->push('Report', URL::to('#'));
});
Breadcrumbs::register('offer_report', function($breadcrumbs) {
    $breadcrumbs->parent('report');
    $breadcrumbs->push('Offer Report', URL::route('offer_report'));
});
Breadcrumbs::register('disembodiment_report_view', function($breadcrumbs) {
    $breadcrumbs->parent('report');
    $breadcrumbs->push('Dis-embodiment Letter', URL::route('disembodiment_report_view'));
});
Breadcrumbs::register('embodiment_letter_view', function($breadcrumbs) {
    $breadcrumbs->parent('report');
    $breadcrumbs->push('Embodiment Letter', URL::route('embodiment_letter_view'));
});
Breadcrumbs::register('transfer_letter_view', function($breadcrumbs) {
    $breadcrumbs->parent('report');
    $breadcrumbs->push('Transfer Letter', URL::route('transfer_letter_view'));
});
Breadcrumbs::register('service_record_unitwise_view', function($breadcrumbs) {
    $breadcrumbs->parent('report');
    $breadcrumbs->push('Ansar Service Record Unit Wise', URL::route('service_record_unitwise_view'));
});
Breadcrumbs::register('three_year_over_report_view', function($breadcrumbs) {
    $breadcrumbs->parent('report');
    $breadcrumbs->push('Transfer Letter', URL::route('three_year_over_report_view'));
});
Breadcrumbs::register('three_year_over_report_view', function($breadcrumbs) {
    $breadcrumbs->parent('report');
    $breadcrumbs->push('Three Years Over Ansar Report', URL::route('three_year_over_report_view'));
});
Breadcrumbs::register('guard_report', function($breadcrumbs) {
    $breadcrumbs->parent('report');
    $breadcrumbs->push('Ansar in guard Report', URL::route('guard_report'));
});
Breadcrumbs::register('blacklist_view', function($breadcrumbs) {
    $breadcrumbs->parent('report');
    $breadcrumbs->push('Blacklisted Ansar Report', URL::route('blacklist_view'));
});
Breadcrumbs::register('blocklist_view', function($breadcrumbs) {
    $breadcrumbs->parent('report');
    $breadcrumbs->push('Blocklisted Ansar Report', URL::route('blocklist_view'));
});
Breadcrumbs::register('disembodiment_report_view', function($breadcrumbs) {
    $breadcrumbs->parent('report');
    $breadcrumbs->push('Disembodied Ansar Info', URL::route('disembodiment_report_view'));
});
Breadcrumbs::register('embodiment_report_view', function($breadcrumbs) {
    $breadcrumbs->parent('report');
    $breadcrumbs->push('Embodied Ansar Info', URL::route('embodiment_report_view'));
});
Breadcrumbs::register('ansar_service_report_view', function($breadcrumbs) {
    $breadcrumbs->parent('report');
    $breadcrumbs->push('Ansar Service Report', URL::route('ansar_service_report_view'));
});
Breadcrumbs::register('view_ansar_service_record', function($breadcrumbs) {
    $breadcrumbs->parent('report');
    $breadcrumbs->push('Ansar Service Record', URL::route('view_ansar_service_record'));
});
Breadcrumbs::register('transfer_ansar_history', function($breadcrumbs) {
    $breadcrumbs->parent('report');
    $breadcrumbs->push('Ansar Transfer Report', URL::route('transfer_ansar_history'));
});
//DG Forms
Breadcrumbs::register('dg', function($breadcrumbs) {
    $breadcrumbs->parent('hrm');
    $breadcrumbs->push('DG View', URL::to('#'));
});
Breadcrumbs::register('direct_offer', function($breadcrumbs) {
    $breadcrumbs->parent('dg');
    $breadcrumbs->push('Direct Offer', URL::route('direct_offer'));
});
Breadcrumbs::register('direct_panel', function($breadcrumbs) {
    $breadcrumbs->parent('dg');
    $breadcrumbs->push('Direct Panel', URL::route('direct_panel_view'));
});
Breadcrumbs::register('direct_cancel_panel', function($breadcrumbs) {
    $breadcrumbs->parent('dg');
    $breadcrumbs->push('Direct Cancel Panel', URL::route('direct_panel_cancel_view'));
});
Breadcrumbs::register('direct_embodiment', function($breadcrumbs) {
    $breadcrumbs->parent('dg');
    $breadcrumbs->push('Direct Embodiment', URL::route('direct_embodiment'));
});
Breadcrumbs::register('direct_disembodiment', function($breadcrumbs) {
    $breadcrumbs->parent('dg');
    $breadcrumbs->push('Direct Dis-Embodiment', URL::route('direct_disembodiment'));
});
Breadcrumbs::register('direct_transfer', function($breadcrumbs) {
    $breadcrumbs->parent('dg');
    $breadcrumbs->push('Direct Transfer', URL::route('direct_transfer'));
});
Breadcrumbs::register('direct_block', function($breadcrumbs) {
    $breadcrumbs->parent('dg');
    $breadcrumbs->push('Direct Block', URL::route('blocklist_entry_view'));
});
Breadcrumbs::register('direct_black', function($breadcrumbs) {
    $breadcrumbs->parent('dg');
    $breadcrumbs->push('Direct Black', URL::route('dg_blacklist_entry_view'));
});
Breadcrumbs::register('direct_unblock', function($breadcrumbs) {
    $breadcrumbs->parent('dg');
    $breadcrumbs->push('Direct Remove From Block', URL::route('dg_unblocklist_entry_view'));
});
Breadcrumbs::register('direct_unblack', function($breadcrumbs) {
    $breadcrumbs->parent('dg');
    $breadcrumbs->push('Direct Remove From Black', URL::route('dg_unblacklist_entry_view'));
});


//Admin
Breadcrumbs::register('admin', function($breadcrumbs) {
    $breadcrumbs->parent('hrm');
    $breadcrumbs->push('Admin', URL::to('#'));
});
Breadcrumbs::register('global_parameter', function($breadcrumbs) {
    $breadcrumbs->parent('admin');
    $breadcrumbs->push('Global Parameter', URL::route('global_parameter'));
});
Breadcrumbs::register('offer_cancel', function($breadcrumbs) {
    $breadcrumbs->parent('admin');
    $breadcrumbs->push('Offer Cancel', URL::route('cancel_offer'));
});
Breadcrumbs::register('id_card', function($breadcrumbs) {
    $breadcrumbs->parent('admin');
    $breadcrumbs->push('Printed ID Card List', URL::route('print_id_list'));
});
Breadcrumbs::register('rejected_offer_list', function($breadcrumbs) {
    $breadcrumbs->parent('admin');
    $breadcrumbs->push('Rejected Offer List', URL::route('rejected_offer_list'));
});

//General Setting
Breadcrumbs::register('gs', function($breadcrumbs) {
    $breadcrumbs->parent('hrm');
    $breadcrumbs->push('General Setting', URL::to('#'));
});
Breadcrumbs::register('session_information_list', function($breadcrumbs) {
    $breadcrumbs->parent('gs');
    $breadcrumbs->push('Session Information', URL::route('view_session_list'));
});
Breadcrumbs::register('session_information_edit', function($breadcrumbs,$id,$page) {
    $breadcrumbs->parent('session_information_list');
    $breadcrumbs->push('Edit Session Information', URL::route('edit_session',['id'=>$id, 'page'=>$page]));
});
Breadcrumbs::register('session_information_entry', function($breadcrumbs) {
    $breadcrumbs->parent('session_information_list');
    $breadcrumbs->push('Entry of Session Information', URL::route('create_session'));
});

Breadcrumbs::register('unit_information_list', function($breadcrumbs) {
    $breadcrumbs->parent('gs');
    $breadcrumbs->push('Unit Information', URL::route('unit_view'));
});

Breadcrumbs::register('unit_information_edit', function($breadcrumbs,$id) {
    $breadcrumbs->parent('unit_information_list');
    $breadcrumbs->push('Edit Unit Information', URL::route('unit_edit',['id'=>$id]));
});

Breadcrumbs::register('unit_information_entry', function($breadcrumbs) {
    $breadcrumbs->parent('unit_information_list');
    $breadcrumbs->push('Entry of Unit Information', URL::route('unit_form'));
});

Breadcrumbs::register('thana_information_list', function($breadcrumbs) {
    $breadcrumbs->parent('gs');
    $breadcrumbs->push('Thana Information', URL::route('thana_view'));
});

Breadcrumbs::register('thana_information_edit', function($breadcrumbs,$id) {
    $breadcrumbs->parent('thana_information_list');
    $breadcrumbs->push('Edit Thana Information', URL::route('thana_edit',['id'=>$id]));
});

Breadcrumbs::register('thana_information_entry', function($breadcrumbs) {
    $breadcrumbs->parent('thana_information_list');
    $breadcrumbs->push('Entry of Thana Information', URL::route('thana_form'));
});

Breadcrumbs::register('disease_information_list', function($breadcrumbs) {
    $breadcrumbs->parent('gs');
    $breadcrumbs->push('Disease Information', URL::route('disease_view'));
});

Breadcrumbs::register('disease_information_edit', function($breadcrumbs,$id) {
    $breadcrumbs->parent('disease_information_list');
    $breadcrumbs->push('Edit Disease Information', URL::route('disease_edit',['id'=>$id]));
});

Breadcrumbs::register('disease_information_entry', function($breadcrumbs) {
    $breadcrumbs->parent('disease_information_list');
    $breadcrumbs->push('Entry of Disease Information', URL::route('disease_entry'));
});

Breadcrumbs::register('skill_information_list', function($breadcrumbs) {
    $breadcrumbs->parent('gs');
    $breadcrumbs->push('Skill Information', URL::route('skill_view'));
});

Breadcrumbs::register('skill_information_edit', function($breadcrumbs,$id) {
    $breadcrumbs->parent('skill_information_list');
    $breadcrumbs->push('Edit Skill Information', URL::route('skill_edit',['id'=>$id]));
});


Breadcrumbs::register('skill_information_entry', function($breadcrumbs) {
    $breadcrumbs->parent('skill_information_list');
    $breadcrumbs->push('Entry of Skill Information', URL::route('skill_entry'));
});