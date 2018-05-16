<?php
use App\modules\HRM\Models\KpiGeneralModel;
use App\modules\SD\Models\Attendance;
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
        Route::get('/test', 'SDController@test');
        Route::get('/download_demand_sheet/{id}', 'DemandSheetController@downloadDemandSheet')->where('id', '[0-9]+');
        Route::post('/generatedemandsheet', 'DemandSheetController@generateDemandSheet');
        Route::get('/demandhistory', 'DemandSheetController@demandHistory')->name('SD.demand_history');
        Route::get('/viewdemandsheet/{id}', 'DemandSheetController@viewDemandSheet')->where('id', '[0-9]+');
        Route::resource('attendance', 'AttendanceController');
        Route::get('/test', function () {
            /*$kpis = KpiGeneralModel::with(['embodiment' => function ($q) {
                $q->select('ansar_id', 'kpi_id', 'emboded_status');
            }])
                ->whereHas('embodiment.ansar.status', function ($q) {
                    $q->where('embodied_status', 1);
                    $q->where('freezing_status', 0);
                    $q->where('block_list_status', 0);
                })->where('status_of_kpi', 1)
                ->select('id', 'kpi_name')
                ->get();*/
            $kpis = DB::connection('hrm')->table('tbl_kpi_info')
                ->join('tbl_embodiment','tbl_embodiment.kpi_id','=','tbl_kpi_info.id')
                ->join('tbl_ansar_status_info','tbl_ansar_status_info.ansar_id','=','tbl_embodiment.ansar_id')
//                ->where('status_of_kpi', 1)
                ->where('embodied_status', 1)
                ->where('freezing_status', 0)
                ->where('block_list_status', 0)
                ->select('kpi_name','tbl_embodiment.ansar_id','tbl_kpi_info.id','emboded_status');
//            return $kpis;

            $msg = [];
            $now = Carbon::now();
            $day = $now->format('d');
            $month = $now->format('m');
            $year = $now->format('Y');
            $kpis->chunk(5000,function ($data) use ($day,$month,$year){
                dispatch(new \App\Jobs\GenerateAttendance($data,$day,$month,$year));
            });
            return "success";
        });
    });
});