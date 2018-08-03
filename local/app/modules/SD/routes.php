<?php
use App\modules\HRM\Models\KpiGeneralModel;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

Route::group(['prefix' => 'SD', 'middleware' => ['web', 'auth', 'permission']], function () {
    Route::group(['namespace' => '\App\modules\SD\Controllers'], function () {
        Route::get('/', 'SDController@index')->name('SD');
        Route::get('/demandsheet', 'DemandSheetController@demandSheet')->name('SD.demand_sheet');
        Route::get('/attendancesheet', 'SDController@attendanceSheet');
        Route::get('/demandconstant', 'DemandSheetController@demandConstant')->name('SD.demand_constant');
        Route::get('/salarysheet', 'SDController@salarySheet');
        Route::post('/updateconstant', 'DemandSheetController@updateConstant');
        Route::post('/demandList', ['as'=>'SD.demandList','uses'=>'DemandSheetController@getDemandList']);
        Route::get('/test', 'SDController@test');
        Route::get('/download_demand_sheet/{id}', 'DemandSheetController@downloadDemandSheet')->where('id', '[0-9]+');
        Route::post('/generatedemandsheet', 'DemandSheetController@generateDemandSheet');
        Route::get('/demandhistory', 'DemandSheetController@demandHistory')->name('SD.demand_history');
        Route::get('/viewdemandsheet/{id}', 'DemandSheetController@viewDemandSheet')->where('id', '[0-9]+');
        Route::resource('attendance', 'AttendanceController');
        Route::post('attendance/view_attendance', ['as'=>'SD.attendance.view_attendance','uses'=>'AttendanceController@viewAttendance']);
        Route::resource('leave', 'LeaveManagementController');
        Route::resource('salary_management', 'SalaryManagementController');
        Route::resource('salary_management_short', 'SalaryManagementForShortKPIController');
        Route::get('kpi_payment/document/{id}', ['as'=>'SD.kpi_payment.show_doc','uses'=>'KPIPaymentController@showDoc']);
        Route::resource('kpi_payment', 'KPIPaymentController');
        Route::resource('salary_disburse', 'SalaryDisburseController',["only"=>["index","create","store"]]);
        Route::get('/test', function () {
//
            return strtotime("0000-00-00")==false?"false":"true";
        });
    });
});