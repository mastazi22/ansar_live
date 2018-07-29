<?php

namespace App\modules\AVURP\Controllers;

use App\modules\AVURP\Models\VDPAnsarInfo;
use App\modules\HRM\Models\District;
use Carbon\Carbon;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class OfferInfoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        if($request->ajax()){
//            return var_dump(json_decode($request->age,true));
//            DB::enableQueryLog();
            $vdp_infos = VDPAnsarInfo::whereHas('status',function($q){
                $q->where('offer_sms_status',0);
                $q->where('embodied_status',0);
                $q->where('retire_status',0);
                $q->where('dead_status',0);
            })->searchQueryForOffer($request->all())->paginate(50);
//            return DB::getQueryLog();
            return view("AVURP::offer_info.data",compact('vdp_infos'));
        }
        $user = auth()->user();
        if($user->type==22){
            $units = District::where('division_id',$user->district->division_id)->get();
        } else if($user->type==66){
            $units = District::where('division_id',$user->division_id)->get();
        } else{
            $units = District::where('id','!=',0)->get();
        }
//        return VDPAnsarInfo::paginate(20);
        return view("AVURP::offer_info.index",compact('units'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if($request->ajax()){
            $rules = [
                'ids.*'=>'required|numeric',
                'unit'=>'required'
            ];
            $this->validate($request,$rules);

            DB::connection('avurp')->beginTransaction();
            try{
                $time = Carbon::now();
                $vdps = VDPAnsarInfo::whereIn('id',array_values($request->ids))->get();
                foreach ($vdps as $vdp){
                    $vdp->offer()->create([
                        'unit_id'=>$request->unit,
                        'sms_send_datetime'=>$time,
                        'sms_end_datetime'=>$time->addHours(48),
                        'message'=>$request->message
                    ]);
                    $vdp->status()->update([
                        'offer_sms_status'=>1
                    ]);
                }
                DB::connection('avurp')->commit();
            }catch(\Exception $e){
                DB::connection('avurp')->rollback();
                return response()->json(['status'=>"error",'message'=>$e->getMessage()]);
            }
            return response()->json(['status'=>"success",'message'=>"Offer sent successfully"]);
        }
        return abort(403);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function selectAll(Request $request){
        if($request->ajax()){
            $ids = VDPAnsarInfo::whereHas('status',function($q){
                $q->where('offer_sms_status',0);
                $q->where('embodied_status',0);
                $q->where('retire_status',0);
                $q->where('dead_status',0);
            })->searchQueryForOffer($request->all())->pluck('id');
            return response()->json(compact('ids'));
        }
        return abort(403);
    }
}
