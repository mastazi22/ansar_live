<?php

namespace App\modules\HRM\Controllers;

use App\Http\Controllers\Controller;
use App\modules\HRM\Models\AnsarStatusInfo;
use App\modules\HRM\Models\CustomQuery;
use App\modules\HRM\Models\OfferCancel;
use App\modules\HRM\Models\OfferQuota;
use App\modules\HRM\Models\OfferSMS;
use App\modules\HRM\Models\OfferSmsLog;
use App\modules\HRM\Models\PanelInfoLogModel;
use App\modules\HRM\Models\PanelModel;
use App\modules\HRM\Models\ReceiveSMSModel;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;
use Mockery\Exception;
use Monolog\Handler\Curl;

class OfferController extends Controller
{

    //
    function makeOffer()
    {
        $dis = Auth::user()->district_id;
        if ($dis) return View::make('HRM::Offer.offer_view')->with(['isFreeze' => CustomQuery::isAnsarFreezeInDistrict($dis)]);
        else return View::make('HRM::Offer.offer_view')->with(['isFreeze' => false]);
    }

    function getQuotaCount()
    {
        $id = Auth::user()->district_id;

        if ($id) {
            $offered = OfferSMS::where('district_id', $id)->count('ansar_id');
            $totalEmbodiedAnsar = DB::table('tbl_embodiment')->join('tbl_kpi_info', 'tbl_kpi_info.id', '=', 'tbl_embodiment.kpi_id')->where('tbl_kpi_info.unit_id', $id)->count('tbl_embodiment.ansar_id');
            $quota = OfferQuota::where('unit_id', $id)->select('quota')->first();
//            return Response::json(['e'=>$totalEmbodiedAnsar,'q'=>$quota,'o'=>$offered]);
            $total = ceil(($totalEmbodiedAnsar * $quota->quota) / 100) - $offered;
            return Response::json(['total_offer' => $total]);
        } else {
            return Response::json(['total_offer' => 'unlimited']);
        }
    }
    function calculateQuota($id){
        $offered = OfferSMS::where('district_id', $id)->count('ansar_id');
        $totalEmbodiedAnsar = DB::table('tbl_embodiment')->join('tbl_kpi_info', 'tbl_kpi_info.id', '=', 'tbl_embodiment.kpi_id')->where('tbl_kpi_info.unit_id', $id)->count('tbl_embodiment.ansar_id');
        $quota = OfferQuota::where('unit_id', $id)->select('quota')->first();
//            return Response::json(['e'=>$totalEmbodiedAnsar,'q'=>$quota,'o'=>$offered]);
        $total = -1;
        if($quota){
            $total = ceil(($totalEmbodiedAnsar * $quota->quota) / 100) - $offered;
        }
        return $total;
    }
    function getKpi(Request $request)
    {
        $rules = [];
        $rules['pc_male']='required|numeric|regex:/^[0-9]+$/|min:0';
        $rules['pc_female']='required|numeric|regex:/^[0-9]+$/|min:0';
        $rules['apc_male']='required|numeric|regex:/^[0-9]+$/|min:0';
        $rules['apc_female']='required|numeric|regex:/^[0-9]+$/|min:0';
        $rules['ansar_male']='required|numeric|regex:/^[0-9]+$/|min:0';
        $rules['ansar_female']='required|numeric|regex:/^[0-9]+$/|min:0';
        if(Auth::user()->type==11){
            $rules['district']='required';
        }
        else if(Auth::user()->type==22){
            $rules['exclude_district']='required|numeric|regex:/^[0-9]+$/';
        }
        $valid = Validator::make($request->all(),$rules);
        if($valid->fails()){
            return response(collect(['type'=>'error','message'=>'Invalid request'])->toJson(),400,['Content-Type'=>'application/json']);
        }
        return Response::json(CustomQuery::getAnsarInfo(
            ['male' => $request->get('pc_male'), 'female' => $request->get('pc_female')],
            ['male' => $request->get('apc_male'), 'female' => $request->get('apc_female')],
            ['male' => $request->get('ansar_male'), 'female' => $request->get('ansar_female')],
            $request->get('district'),
            $request->get('exclude_district')));
    }

    function sendOfferSMS(Request $request)
    {
        $rules = [];
        if(Auth::user()->type==11){
            $rules['offered_ansar']='required|array|array_type:int';
            $rules['district_id']='required|numeric|regex:/^[0-9]+$/';
        }
        else if(Auth::user()->type==33){
            $rules['offered_ansar']='required|numeric|regex:/^[0-9]+$/';
            $rules['district_id']='required|numeric|regex:/^[0-9]+$/';
            $rules['type']='required|regex:/^[a-z]+$/';
        }
        else if(Auth::user()->type==22){
            $did = Auth::user()->district_id;
            $quota = $this->calculateQuota($did);
            $rules['offer_limit']='required|numeric|regex:/^[0-9]+$/|max:'.$quota;
            $rules['offered_ansar']='required|array|array_length_max:offer_limit|array_type:int';
            $rules['district_id']='required|numeric|regex:/^[0-9]+$/|same:'.$did;
        }
        $valid = Validator::make($request->all(),$rules);
        if($valid->fails()){
            return response(collect(['type'=>'error','message'=>'Invalid request'])->toJson(),400,['Content-Type'=>'application/json']);
        }
        $ansar_ids = Input::get('offered_ansar');
        $district_id = Input::get('district_id');
        $offer_limit = Input::get('offer_limit');
        $type = Input::get('type');
//        if(is_int($offer_limit)&&$offer_limit<count($ansar_ids)){
//            return Response::json(['status'=>false,'message'=>'Your offer limit exceed']);
//        }
        //return $ansar_ids;
        $result = array('success' => 0, 'fail' => 0);
        $user = [];
        if (is_array($ansar_ids)) {
            DB::beginTransaction();
            try {
                for ($i = 0; $i < count($ansar_ids); $i++) {
                    $offer = new OfferSMS;
                    $offer->ansar_id = $ansar_ids[$i];
                    $offer->sms_send_datetime = Carbon::now();
                    $offer->sms_end_datetime = Carbon::now()->addHours(48);
                    $offer->district_id = $district_id;
                    $offer->come_from = $type;
                    $offer->action_user_id = auth()->user()->id;
                    if (!$offer->save()) throw new Exception("An Error Occur While Send Offer. Please Try Again Later");
                    $this->removeFromPanel($ansar_ids[$i]);
                    array_push($user, ['ansar_id' => $ansar_ids[$i], 'action_type' => 'SEND OFFER', 'from_state' => 'PANEL', 'to_state' => 'OFFER', 'action_by' => auth()->user()->id]);
                    $result['success']++;

                }
                DB::commit();
                CustomQuery::addActionlog($user, true);
            } catch (\Exception $e) {
                DB::rollback();
                return response(collect(['status' => 'error', 'message' => $e->getMessage()])->toJson(),400,['Content-Type'=>'application/json']);
            }
            return Response::json(['type' => 'success', 'message' => "Offer Send Successfully"]);
        } else {
            DB::beginTransaction();
            try {
                $offer = new OfferSMS;
                $offer->ansar_id = $ansar_ids;
                $offer->sms_send_datetime = Carbon::now();
                $offer->sms_end_datetime = Carbon::now()->addHours(48);
                $offer->district_id = $district_id;
                $offer->come_from = $type;
                if (!$offer->save()) throw new Exception("An Error Occur While Send Offer. Please Try Again Later");
                switch ($type) {
                    case 'panel':
                        $this->removeFromPanel($ansar_ids);
                        break;
                    case 'rest':
                        $this->removeFromRest($ansar_ids);
                        break;

                }
                DB::commit();
                CustomQuery::addActionlog(['ansar_id' => $ansar_ids, 'action_type' => 'SEND OFFER', 'from_state' => 'PANEL', 'to_state' => 'OFFER', 'action_by' => auth()->user()->id]);
                CustomQuery::addDGlog(['ansar_id' => $ansar_ids, 'action_type' => 'SEND OFFER', 'from_state' => 'PANEL', 'to_state' => 'OFFER']);
            } catch (\Exception $e) {
                DB::rollback();
                return response(collect(['status' => 'error', 'message' => $e->getMessage()])->toJson(),400,['Content-Type'=>'application/json']);
            }
            return Response::json(['status' => 'success', 'data' => "Offer send successfully"]);
        }
    }

    function removeFromPanel($ansar_ids)
    {
        $pa = PanelModel::where('ansar_id', $ansar_ids)->first();
        $as = AnsarStatusInfo::where('ansar_id', $ansar_ids)->first();
        $as->pannel_status = 0;
        $as->offer_sms_status = 1;
        $as->save();
        if ($pa) {
            $pl = new PanelInfoLogModel;
            $pl->panel_id_old = $pa->id;
            $pl->ansar_id = $pa->ansar_id;
            $pl->merit_list = $pa->ansar_merit_list;
            $pl->panel_date = $pa->panel_date;
            $pl->old_memorandum_id = !$pa->memorandum_id ? "N\A" : $pa->memorandum_id;
            $pl->movement_date = Carbon::today()->addHour(6);
            $pl->come_from = $pa->come_from;
            $pl->move_to = 'Offer';
            $pl->save();
            $pa->delete();
        }
    }

    function removeFromRest($ansar_ids)
    {
        $as = AnsarStatusInfo::where('ansar_id', $ansar_ids)->first();
        $as->offer_sms_status = 1;
        return $as->save();
    }

    function offerQuota()
    {
        $quota = CustomQuery::offerQuota();
        return View::make('HRM::Offer.offer_quota',['quota'=>$quota]);
    }

    function getOfferQuota()
    {
        return Response::json(CustomQuery::offerQuota());
    }

    function updateOfferQuota()
    {
        //return 'ggggg';
        $id = Input::get('quota_id');
        $quota = Input::get('quota_value');
        $success = true;
        //return $id;
        for ($i = 0; $i < count($id); $i++) {

            try {
                $offer_quota = OfferQuota::where('unit_id', $id[$i])->firstOrFail();
                $offer_quota->update(['quota' => $quota[$i]]);
            } catch (ModelNotFoundException $e) {
                //return $e->getMessage();
                $offer_quota = new OfferQuota;
                $offer_quota->unit_id = $id[$i];
                $offer_quota->quota = $quota[$i];
                $offer_quota->saveOrFail();
//                $offer_quota->fill(['unit_id'=>$id[$i],'quota' => $quota[$i]]);
            }
            if (!$offer_quota) $success = false;
        }
        return Redirect::back()->with('success','Offer quota updated successfully');
    }

    function handleCancelOffer()
    {
        $rules=[
            'ansar_ids'=>'required|is_array|array_type:int|array_length_min:1'
        ];
        $vaild = Validator::make(Input::all(),$rules);
        if($vaild->fails()){
            return response(collect(['type'=>'error','message'=>"Invalid request(400)"]),400,['Content-Type'=>'application/json']);
        }
        $result = ['success' => 0, 'fail' => 0];
        $ansar_ids = Input::get('ansar_ids');
//        return $ansar_ids;
        for ($i = 0; $i < count($ansar_ids); $i++) {
            //return $ansar_ids[$i];
            DB::beginTransaction();
            try {
                $offered_ansar = OfferSMS::where('ansar_id', $ansar_ids[$i])->first();
                if (!$offered_ansar) $received_ansar = ReceiveSMSModel::where('ansar_id', $ansar_ids[$i])->first();
                $offer_log = new OfferSmsLog;
                $offer_cancel = new OfferCancel;
                $status_info = AnsarStatusInfo::where('ansar_id', $ansar_ids[$i])->first();
                $panel_log = PanelInfoLogModel::where('ansar_id', $ansar_ids[$i])->select('old_memorandum_id')->first();
                $panel_info = new PanelModel;
                $panel_info->ansar_id = $ansar_ids[$i];
                $panel_info->memorandum_id = $panel_log->old_memorandum_id;
                $panel_info->panel_date = Carbon::now()->addHour(6);
                $panel_info->come_from = 'OfferCancel';
                $panel_info->ansar_merit_list = 1;
                $panel_info->action_user_id = auth()->user()->id;
                $panel_info->save();
                $status_info->offer_sms_status = 0;
                $status_info->pannel_status = 1;
                $status_info->save();
                $offer_cancel->ansar_id = $ansar_ids[$i];
                $offer_cancel->offer_cancel_date = Carbon::now();
                $offer_cancel->save();
                if ($offered_ansar) {
                    $offer_log->ansar_id = $ansar_ids[$i];
                    $offer_log->offered_date = $offered_ansar->sms_send_datetime;
                    $offer_log->reply_type = 'No Reply';
                    $offer_log->action_date = Carbon::now();
                    $offer_log->offered_district = $offered_ansar->district_id;
                    $offer_log->action_user_id = auth()->user()->id;
                    $offer_log->save();
                    $offered_ansar->delete();
                } else {
                    $offer_log->ansar_id = $ansar_ids[$i];
                    $offer_log->reply_type = 'Yes';
                    $offer_log->offered_date = $received_ansar->sms_send_datetime;
                    $offer_log->offered_district = $received_ansar->offered_district;
                    $offer_log->action_user_id = auth()->user()->id;
                    $offer_log->save();
                    $received_ansar->delete();
                }
                DB::commit();
                CustomQuery::addActionlog(['ansar_id' => $ansar_ids[$i], 'action_type' => 'CANCEL OFFER', 'from_state' => 'OFFER', 'to_state' => 'PANEL', 'action_by' => auth()->user()->id]);
                $result['success']++;
            } catch (\Exception $e) {
                DB::rollback();
                return response(collect(['type'=>'error','message'=>$e->getMessage()]),400,['Content-Type'=>'application\json']);
            }
        }
        return Response::json(['type'=>'success','message'=>'Offer cancel successfully']);
    }

    function cancelOfferView()
    {
        return View::make('HRM::Offer.offer_cancel_view');
    }

    function getOfferedAnsar()
    {
        $rules=[
          'district_id'=>'required|numeric|regex:/^[0-9]+$/'
        ];
        $valid = Validator::make(Input::all(),$rules);
        if($valid->fails()){
            return response(collect(['type'=>'error','message'=>'Invalid request']),400,['Content-Type'=>'application\json']);
        }
        $district_id = Input::get('district_id');
        return Response::json(CustomQuery::getOfferSMSInfo($district_id));
    }

    function testSmsPurpose()
    {
        $user = "ansarvdp_test";
        $pass = " ssl@123";
        $sid = "ANSARVDPTEST ";
        $url = "http://sms.sslwireless.com/pushapi/dynamic/server.php";
        $param = "user=$user&pass=$pass&sms[0][0]=8801712363785&sms[0][1]=" . urlencode("Test
        SMS 1") . "&sms[0][2]=123456789&sid=$sid";
        $crl = curl_init();
        curl_setopt($crl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($crl, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($crl, CURLOPT_URL, $url);
        curl_setopt($crl, CURLOPT_HEADER, 0);
        curl_setopt($crl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($crl, CURLOPT_POST, 1);
        curl_setopt($crl, CURLOPT_POSTFIELDS, $param);
        $response = curl_exec($crl);
        curl_close($crl);
        $xmlvalue = simplexml_load_string($response);
        return Response::json($xmlvalue);
    }
}