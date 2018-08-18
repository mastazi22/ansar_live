<?php
use App\modules\HRM\Models\KpiGeneralModel;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

Route::group(['prefix' => 'SD', 'middleware' => [ 'auth','manageDatabase', 'checkUserType', 'permission']], function () {
    Route::group(['namespace' => '\App\modules\SD\Controllers'], function () {
        Route::get('/', 'SDController@index')->name('SD');
        Route::get('/demandsheet', 'DemandSheetController@demandSheet')->name('SD.demand_sheet');
        Route::get('/attendancesheet', 'SDController@attendanceSheet');
        Route::get('/demandconstant', 'DemandSheetController@demandConstant')->name('SD.demand_constant');
        Route::get('/salarysheet', 'SDController@salarySheet');
        Route::post('/updateconstant', 'DemandSheetController@updateConstant')->name('SD.update_demand_constant');
        Route::post('/demandList', ['as'=>'SD.demandList','uses'=>'DemandSheetController@getDemandList']);
        Route::get('/test', 'SDController@test');
        Route::get('/download_demand_sheet/{id}', 'DemandSheetController@downloadDemandSheet')->where('id', '[0-9]+');
        Route::post('/generatedemandsheet', 'DemandSheetController@generateDemandSheet')->name('SD.generate_demand_sheet');
        Route::get('/demandhistory', 'DemandSheetController@demandHistory')->name('SD.demand_history');
        Route::get('/viewdemandsheet/{id}', 'DemandSheetController@viewDemandSheet')->name("SD.view_demand_sheet")->where('id', '[0-9]+');
        Route::post('attendance/load_datab', ['as'=>"SD.attendance.load_datab",'uses'=>'AttendanceController@loadDataForPlanB']);
        Route::post('attendance/storb', ['as'=>"SD.attendance.storeb",'uses'=>'AttendanceController@storePlanB']);
        Route::resource('attendance', 'AttendanceController');
        Route::post('attendance/view_attendance', ['as'=>'SD.attendance.view_attendance','uses'=>'AttendanceController@viewAttendance']);
        Route::resource('leave', 'LeaveManagementController');
        Route::post('/salarySheetList', ['as'=>'SD.salary_management.salarySheetList','uses'=>'SalaryManagementController@getSalarySheetList']);
        Route::post('/salary_management/view_payroll', ['as'=>'SD.salary_management.view_payroll','uses'=>'SalaryManagementController@generate_payroll']);
        Route::get('/salary_management/view_payroll_by_id/{id}', ['as'=>'SD.salary_management.view_payroll_by_id','uses'=>'SalaryManagementController@generate_payroll_salary_sheet']);
        Route::resource('salary_management', 'SalaryManagementController');
        Route::resource('salary_management_short', 'SalaryManagementForShortKPIController');
        Route::get('kpi_payment/document/{id}', ['as'=>'SD.kpi_payment.show_doc','uses'=>'KPIPaymentController@showDoc']);
        Route::resource('kpi_payment', 'KPIPaymentController');
        Route::get('salary_disburse/download/{file_name}', ['as'=>'SD.salary_disburse.download','uses'=>'SalaryDisburseController@download']);
        Route::resource('salary_disburse', 'SalaryDisburseController',["only"=>["index","create","store","show"]]);
        /*Route::get('/test', function () {
//
            setlocale(LC_TIME,"bn_BD");
            return strftime("%B, %Y",Carbon::now()->timestamp);
//            return
            return \Illuminate\Support\Facades\URL::route('SD.salary_management.show',36);
              return view("SD::salary_sheet.payroll_view");
        });*/
    });
});