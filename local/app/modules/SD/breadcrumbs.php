<?php
Breadcrumbs::register('SD', function($breadcrumbs) {
    $breadcrumbs->parent('home');
    $breadcrumbs->push('SD', URL::route('SD'));
});
Breadcrumbs::register('attendance', function($breadcrumbs) {
    $breadcrumbs->parent('SD');
    $breadcrumbs->push('Attendance', URL::route('SD.attendance.index'));
});