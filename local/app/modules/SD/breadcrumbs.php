<?php
Breadcrumbs::register('SD', function($breadcrumbs) {
    $breadcrumbs->parent('home');
    $breadcrumbs->push('SD', URL::route('SD'));
});
Breadcrumbs::register('salary_management', function($breadcrumbs) {
    $breadcrumbs->parent('SD');
    $breadcrumbs->push('Salary Management', '#');
});
Breadcrumbs::register('attendance', function($breadcrumbs) {
    $breadcrumbs->parent('salary_management');
    $breadcrumbs->push('Attendance', "#");
});
Breadcrumbs::register('leave_management', function($breadcrumbs) {
    $breadcrumbs->parent('salary_management');
    $breadcrumbs->push('Leave Management', "#");
});
Breadcrumbs::register('view_attendances', function($breadcrumbs) {
    $breadcrumbs->parent('attendance');
    $breadcrumbs->push('View Attendances', URL::route('SD.attendance.index'));
});
Breadcrumbs::register('attendance.create', function($breadcrumbs) {
    $breadcrumbs->parent('attendance');
    $breadcrumbs->push('Take Attendance', URL::route('SD.attendance.create'));
});
Breadcrumbs::register('view_leaves', function($breadcrumbs) {
    $breadcrumbs->parent('leave_management');
    $breadcrumbs->push('View Leaves', URL::route('SD.leave.index'));
});
Breadcrumbs::register('grant_leave', function($breadcrumbs) {
    $breadcrumbs->parent('leave_management');
    $breadcrumbs->push('Grant Leave', URL::route('SD.leave.index'));
});