<?php

namespace App\Http\Middleware;

use App\modules\HRM\Models\District;
use App\modules\HRM\Models\Division;
use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;

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
        'get_ansar_list'=>['division'=>'range','unit'=>'unit'],
        'get_recent_ansar_list'=>['division'=>'range','unit'=>'unit'],
        'getnotverifiedansar'=>['division'=>'range','unit'=>'unit'],
        'getverifiedansar'=>['division'=>'range','unit'=>'unit'],
        'dashboard_total_ansar'=>['division_id'=>'range','unit_id'=>'unit'],
        'recent_ansar'=>['division_id'=>'range','unit_id'=>'unit'],
    ];
    public function handle($request, Closure $next)
    {
        $user = Auth::user();
        $routeName = $request->route()->getName();
        $input = $request->input();
        foreach($this->urls as $url=>$params){
            if(!strcasecmp($url,$routeName)){
                foreach($params as $key=>$type){
                    if($type=='unit'){
                        if($user->type==22){
                            $input[$key] = $user->district->id;
                        }
                    }
                    else if($type=='range'){
                        if($user->type==22){
                            $input[$key] = $user->district->division_id;
                        }
                        else if($user->type==66){
                            $input[$key] = $user->division_id;
                        }
                    }
                }
            }
        }
        $request->replace($input);
//        return $request->all();
//        if ($request->ajax()) {
//            $name = $request->route()->getName();
//            if (strcasecmp($name, 'district_name') == 0) {
//                switch ($user->type) {
//                    case 22:
//                        return Response::json([District::find($user->district_id)]);
//                    case 66:
//                        $request->replace(['id' => $user->division_id]);
//                        break;
//                }
//            } else if (strcasecmp($name, 'division_name') == 0) {
//                switch ($user->type) {
//                    case 22:
//                        return Response::json([]);
//                    case 66:
//                        return Response::json([Division::find($user->division_id)]);
//                }
//            }
//            else {
//                if ($request->exists('unit')) {
//                    if (strcasecmp($request->input('unit'), 'all') == 0) {
//                        $data = $request->input();
//                        switch ($user->type) {
//                            case 22:
//                                $data['unit'] = $user->district_id;
//                                $request->replace($data);
//                                break;
//                            case 66:
//                                $data['division'] = $user->division_id;
//                                $request->replace($data);
//                                break;
//                        }
//                    }
//                }
//                else{
//                    switch ($user->type) {
//                        case 22:
//                            if ($request->input()) {
//                                $input = $request->input();
//                                array_push($input, ['district_id' => $user->district_id]);
//                                $request->replace($input);
//                            } else {
//                                $request->replace(['district_id' => $user->district_id]);
//                            }
//                            break;
//                        case 66:
//                            if ($request->input()) {
//                                $input = $request->input();
//                                array_push($input, ['division_id' => $user->division_id]);
//                                $request->replace($input);
//                            } else {
//                                $request->replace(['division_id' => $user->division_id]);
//                            }
//                            break;
//                    }
//                }
//            }
//        }
//        else {
//            switch ($user->type) {
//                case 22:
//                    if ($request->input()) {
//                        $input = $request->input();
//                        array_push($input, ['district_id' => $user->district_id]);
//                        $request->replace($input);
//                    } else {
//                        $request->replace(['district_id' => $user->district_id]);
//                    }
//                    break;
//                case 66:
//                    if ($request->input()) {
//                        $input = $request->input();
//                        array_push($input, ['division_id' => $user->division_id]);
//                        $request->replace($input);
//                    } else {
//                        $request->replace(['division_id' => $user->division_id]);
//                    }
//                    break;
//            }
//        }
        return $next($request);
    }
}
