<?php

namespace App\Providers;

use App\modules\HRM\Models\EmbodimentModel;
use App\modules\HRM\Models\KpiDetailsModel;
use App\modules\HRM\Models\PersonalInfo;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
        Validator::extend('array',function($attribute,$value,$parameter,$validator){

            return is_array($value);

        });
        Validator::extend('array_length_max',function($attribute,$value,$parameter,$validator){

            $length = count($value);
            $max = array_get($validator->getData(),$parameter[0]);
            Log::info($max);
            return $length<=$max;
        });
        Validator::extend('array_length_min',function($attribute,$value,$parameter,$validator){

            $length = count($value);
            $min = intval($parameter[0]);
            Log::info($min);
            return $length>=$min;
        });
        Validator::extend('array_type',function($attribute,$value,$parameter,$validator){
            $type = $parameter[0];
            Log::info($type);
            switch($type){
                case 'int':
                    foreach($value as $v){
                        if(!is_int($v)) return false;
                    }
                break;
            }

            return true;
        });
        Validator::extend('isEligible',function($attribute,$value,$parameter,$validator){
            $ansar_id=array_get($validator->getData(), $parameter[0]);
            $kpi_id=array_get($validator->getData(), $parameter[1]);
            Log::info($ansar_id." ".$kpi_id);
            if(!is_int($ansar_id)||!is_int($kpi_id)){
                return false;
            }
            $ansar_rank=PersonalInfo::where('tbl_ansar_parsonal_info.ansar_id',$ansar_id)->select('designation_id')->first();
            if($ansar_rank->designation_id==1){
                $kpi_ansar_given=KpiDetailsModel::where('kpi_id', $kpi_id)->select('no_of_ansar')->first();
                $kpi_ansar_appointed=EmbodimentModel::join('tbl_ansar_parsonal_info', 'tbl_embodiment.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->where('tbl_embodiment.kpi_id', '=', $kpi_id)->where('tbl_ansar_parsonal_info.designation_id', '=', 1)->count('tbl_ansar_parsonal_info.ansar_id');
                if($kpi_ansar_given->no_of_ansar>$kpi_ansar_appointed){
                    return true;
                }else{
                    return false;
                }
            }
            elseif($ansar_rank->designation_id==2){
                $kpi_ansar_given=KpiDetailsModel::where('kpi_id', $kpi_id)->select('no_of_apc')->first();
                $kpi_ansar_appointed=EmbodimentModel::join('tbl_ansar_parsonal_info', 'tbl_embodiment.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->where('tbl_embodiment.kpi_id', '=', $kpi_id)->where('tbl_ansar_parsonal_info.designation_id', '=', 2)->count('tbl_ansar_parsonal_info.ansar_id');
                if($kpi_ansar_given->no_of_apc>$kpi_ansar_appointed){
                    return true;
                }else{
                    return false;
                }
            }
            elseif($ansar_rank->designation_id== 3){
                $kpi_ansar_given=KpiDetailsModel::where('kpi_id', $kpi_id)->select('no_of_pc')->first();
                $kpi_ansar_appointed=EmbodimentModel::join('tbl_ansar_parsonal_info', 'tbl_embodiment.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->where('tbl_embodiment.kpi_id', '=', $kpi_id)->where('tbl_ansar_parsonal_info.designation_id', '=', 3)->count('tbl_ansar_parsonal_info.ansar_id');
                if($kpi_ansar_given->no_of_pc>$kpi_ansar_appointed){
                    return true;
                }else{
                    return false;
                }
            }
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
