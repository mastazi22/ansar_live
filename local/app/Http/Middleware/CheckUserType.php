<?php

namespace App\Http\Middleware;

use App\modules\HRM\Models\District;
use App\modules\HRM\Models\Division;
use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;

class CheckUserType
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    private $urls =[
        'district_name'=>['id'=>'range'],
        'division_name'=>['id'=>'range'],
        'thana_name'=>['id'=>'unit'],
        'get_ansar_list'=>['division'=>'range','unit'=>'unit'],
        'get_recent_ansar_list'=>['division'=>'range','unit'=>'unit'],
        'getnotverifiedansar'=>['division'=>'range','unit'=>'unit'],
        'getverifiedansar'=>['division'=>'range','unit'=>'unit'],
        'dashboard_total_ansar'=>['division_id'=>'range','unit_id'=>'unit'],
        'progress_info'=>['division_id'=>'range','district_id'=>'unit'],
        'graph_embodiment'=>['division_id'=>'range','district_id'=>'unit'],
        'recent_ansar'=>['division_id'=>'range','unit_id'=>'unit'],
        'service_ended_info_details'=>['division'=>'range','unit'=>'unit'],
        'ansar_reached_fifty_details'=>['division'=>'range','unit'=>'unit'],
        'offer_accept_last_5_day_data'=>['division'=>'range','unit'=>'unit'],
        'kpi_view_details'=>['division'=>'range','unit'=>'unit'],
        'load_ansar_before_withdraw'=>['division_id'=>'range','unit_id'=>'unit'],
        'load_ansar_before_reduce'=>['division_id'=>'range','unit_id'=>'unit'],
        'ansar_list_for_reduce'=>['range'=>'range','unit'=>'unit'],
        'load_ansar'=>['range'=>'range','unit'=>'unit'],
        'load_ansar_for_embodiment_date_correction'=>['range'=>'range','unit'=>'unit'],
//        'get_transfer_ansar_history'=>['range'=>'range','unit'=>'unit'],
        'inactive_kpi_list'=>['division'=>'range','unit'=>'unit'],
        'three_years_over_ansar_info'=>['division'=>'range','unit'=>'unit'],
        'disemboded_ansar_info'=>['division_id'=>'range','unit_id'=>'unit'],
        'get_offered_ansar'=>['division'=>'range','unit'=>'unit'],
        'print_letter'=>['unit'=>'unit'],
        'check-ansar'=>['unit'=>'unit'],
        'letter_data'=>['unit'=>'unit'],
        'send_offer'=>['exclude_district'=>'unit','district_id'=>'unit'],
        'entry_info'=>['unit'=>'unit','range'=>'range'],
        'freeze_entry'=>['unit'=>'unit','range'=>'range'],
        'load_ansar_for_freeze'=>['unit'=>'unit','range'=>'range'],
        'entry_report'=>['unit'=>'unit','range'=>'range'],
        'new-embodiment-entry'=>['division_name_eng'=>'unit'],
        'report.applicants.status'=>['range'=>'range','unit'=>'unit'],
        'recruitment.marks.index'=>['range'=>'range','unit'=>'unit'],
        'recruitment.move_to_hrm'=>['range'=>'range','unit'=>'unit'],
        'recruitment.edit_for_hrm'=>['range'=>'range','unit'=>'unit'],
        'recruitment.hrm.index'=>['range'=>'range','unit'=>'unit'],
        'recruitment.hrm.card_print'=>['range'=>'range','unit'=>'unit'],
        'recruitment.applicant.search_result'=>['range'=>'range','unit'=>'unit'],
    ];
    public function handle($request, Closure $next)
    {
        $user = Auth::user();
        $route = $request->route();
        if(is_null($route)) return $next($request);
        $routeName = $route->getName();
        $routePrefix = $route->getPrefix();
//        return Session::get('module');
        $input = $request->input();
        foreach($this->urls as $url=>$params){
            if(!strcasecmp($url,$routeName)){
                foreach($params as $key=>$type){
                    if($type=='unit'){
                        if($user->type==22){
                            if(Session::has('module')&&Session::get('module')==='recruitment'&&$user->recDistrict) {
                                $input[$key] = $user->recDistrict->id;
//                                return $input;
                            }
                            else $input[$key] = $user->district->id;
                        }
                        else if($user->type==66){
                            $units = District::where('division_id',$user->division_id)->pluck('id');
                            if(isset($input[$key])&&$input[$key]!='all'&&$input[$key]&&!in_array($input[$key],$units->toArray())){
                                if($request->ajax()){
                                    return response("Unauthorized",401);
                                }
                                else abort(401);
                            }
                        }
                    }
                    else if($type=='range'){
                        if($user->type==22){
                            if(Session::has('module')&&Session::get('module')==='recruitment'&&$user->recDistrict) $input[$key] = $user->recDistrict->division_id;
                            else $input[$key] = $user->district->division_id;
                        }
                        else if($user->type==66){
                            $input[$key] = $user->division_id;
                        }
                    }
                }
            }
        }
        $request->replace($input);
        return $next($request);
    }
}
