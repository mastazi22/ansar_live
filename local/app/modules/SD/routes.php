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
            $kpis = KpiGeneralModel::with(['embodiment' => function ($q) {
                $q->select('ansar_id', 'kpi_id', 'emboded_status');
            }])
                ->whereHas('embodiment.ansar.status', function ($q) {
                    $q->where('embodied_status', 1);
                    $q->where('freezing_status', 0);
                    $q->where('block_list_status', 0);
                })->where('status_of_kpi', 1)
                ->select('id', 'kpi_name')
                ->get();
            $msg = [];
            $now = Carbon::now();
            foreach ($kpis as $kpi){
                DB::connection('sd')->beginTransaction();
                try{

                    $day = $now->format('d');
                    $month = $now->format('m');
                    $year = $now->format('Y');
                    $kpi_id = $kpi->id;
                    foreach ($kpi->embodiment as $e){
                        $ansar_id = $e->ansar_id;
                        if(Attendance::where(compact('day','month','year','kpi_id','ansar_id'))->exists()) {
                            continue;
                        }
                        Attendance::create(compact('day','month','year','kpi_id','ansar_id'));
                        DB::connection('sd')->commit();

                    }
                    array_push($msg,"success");

                }catch(\Exception $e){
                    DB::connection('sd')->rollBack();
                    array_push($msg,$e->getMessage());
                }

            }
            return $msg;
        });
    });
});