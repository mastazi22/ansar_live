<?php
Breadcrumbs::register('AVURP', function($breadcrumbs) {
    $breadcrumbs->parent('home');
    $breadcrumbs->push('AVURP', URL::route('AVURP'));
});
Breadcrumbs::register('entry.list', function($breadcrumbs) {
    $breadcrumbs->parent('AVURP');
    $breadcrumbs->push('Entry List', URL::route('AVURP.info.index'));
});
Breadcrumbs::register('entry.list.entry', function($breadcrumbs) {
    $breadcrumbs->parent('entry.list');
    $breadcrumbs->push('Add New Entry', URL::route('AVURP.info.create'));
});
Breadcrumbs::register('entry.list.view', function($breadcrumbs) {
    $breadcrumbs->parent('entry.list');
    $breadcrumbs->push('View Entry Detail', '#');
});
Breadcrumbs::register('entry.list.edit', function($breadcrumbs) {
    $breadcrumbs->parent('entry.list');
    $breadcrumbs->push('Edit Entry', '#');
});
