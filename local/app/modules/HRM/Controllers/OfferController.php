<?php

namespace App\modules\HRM\Controllers;

use App\Helper\Helper;
use App\Http\Controllers\Controller;
use App\Jobs\OfferQueue;
use App\modules\HRM\Models\ActionUserLog;
use App\modules\HRM\Models\AnsarStatusInfo;
use App\modules\HRM\Models\CustomQuery;
use App\modules\HRM\Models\OfferCancel;
use App\modules\HRM\Models\OfferQuota;
use App\modules\HRM\Models\OfferSmsLog;
use App\modules\HRM\Models\OfferZone;
use App\modules\HRM\Models\PanelInfoLogModel;
use App\modules\HRM\Models\PanelModel;
use App\modules\HRM\Models\PersonalInfo;
use App\modules\HRM\Models\RequestDumper;
use App\modules\HRM\Models\UserOfferQueue;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;

class OfferController extends Controller
{

    //
    function makeOffer()
    {
        $dis = Auth::user()->district_id;
        //if ($dis) return View::make('HRM::Offer.offer_view')->with(['isFreeze' => CustomQuery::isAnsarFreezeInDistrict($dis)]);
//        else return View::make('HRM::Offer.offer_view')->with(['isFreeze' => false]);
        return View::make('HRM::Offer.offer_view')->with(['isFreeze' => false]);
    }

    function getQuotaCount()
    {
        return Response::json(['total_offer' => Helper::getOfferQuota(Auth::user())]);
    }

    function calculateQuota($id)
    {

        return Helper::getOfferQuota(Auth::user());
    }

    function getKpi(Request $request)
    {
        $rules = [];
        $rules['pc_male'] = 'required|numeric|regex:/^[0-9]+$/|min:0';
        $rules['pc_female'] = 'required|numeric|regex:/^[0-9]+$/|min:0';
        $rules['apc_male'] = 'required|numeric|regex:/^[0-9]+$/|min:0';
        $rules['apc_female'] = 'required|numeric|regex:/^[0-9]+$/|min:0';
        $rules['ansar_male'] = 'required|numeric|regex:/^[0-9]+$/|min:0';
        $rules['ansar_female'] = 'required|numeric|regex:/^[0-9]+$/|min:0';
        if (Auth::user()->type == 11) {
            $rules['district'] = 'required';
        } else if (Auth::user()->type == 22) {
            $rules['exclude_district'] = 'required|numeric|regex:/^[0-9]+$/';
        }
        $valid = Validator::make($request->all(), $rules);
        if ($valid->fails()) {
            return response(collect(['type' => 'error', 'message' => 'Invalid request'])->toJson(), 400, ['Content-Type' => 'application/json']);
        }
        DB::beginTransaction();
        try {
            $data = CustomQuery::getAnsarInfo(
                ['male' => $request->get('pc_male'), 'female' => $request->get('pc_female')],
                ['male' => $request->get('apc_male'), 'female' => $request->get('apc_female')],
                ['male' => $request->get('ansar_male'), 'female' => $request->get('ansar_female')],
                $request->get('district'),
                $request->get('exclude_district'), Auth::user());
            PanelModel::whereIn('ansar_id', $data)->update(['locked' => 1]);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return response(collect(['type' => 'error', 'message' => $e->getMessage()])->toJson(), 400, ['Content-Type' => 'application/json']);
        }
        return Response::json($data);
    }

    function sendOfferSMS(Request $request)
    {
        $rules = [];
        $rules['pc_male'] = 'required|numeric|regex:/^[0-9]+$/|min:0';
        $rules['pc_female'] = 'required|numeric|regex:/^[0-9]+$/|min:0';
        $rules['apc_male'] = 'required|numeric|regex:/^[0-9]+$/|min:0';
        $rules['apc_female'] = 'required|numeric|regex:/^[0-9]+$/|min:0';
        $rules['ansar_male'] = 'required|numeric|regex:/^[0-9]+$/|min:0';
        $rules['ansar_female'] = 'required|numeric|regex:/^[0-9]+$/|min:0';
        $rules['district_id'] = 'required|numeric|regex:/^[0-9]+$/';
        if (Auth::user()->type == 11) {
            $rules['district'] = 'required';
        } else if (Auth::user()->type == 22) {
            $rules['exclude_district'] = 'required|numeric|regex:/^[0-9]+$/';
        }
        $valid = Validator::make($request->all(), $rules);
        if ($valid->fails()) {
            Log::info($valid->messages()->toArray());
            return response(collect(['type' => 'error', 'message' => 'Invalid request'])->toJson(), 400, ['Content-Type' => 'application/json']);
        }
        $district_id = $request->get('district_id');
        DB::beginTransaction();
        try {
            if (UserOfferQueue::where('user_id', Auth::user()->id)->exists()) {
                throw new \Exception("Your have one pending offer.Please wait until your offer is complete");
            }
            $userOffer = UserOfferQueue::create([
                'user_id' => Auth::user()->id
            ]);
            $user = Auth::user();
            $offerZone = OfferZone::where('unit_id',$user->district_id)->pluck('offer_zone_unit_id')->toArray();
            $data = CustomQuery::getAnsarInfo(
                ['male' => $request->get('pc_male'), 'female' => $request->get('pc_female')],
                ['male' => $request->get('apc_male'), 'female' => $request->get('apc_female')],
                ['male' => $request->get('ansar_male'), 'female' => $request->get('ansar_female')],
                $request->get('district'),
                $request->get('exclude_district'), $user,$offerZone);
            return $data;
            Log::info($request->all());

            if($user->type==22){

                if(in_array($user->district_id,Config::get('app.DG'))){
                   $offer_type = 'DG';
                } else if(in_array($user->district_id,Config::get('app.CG'))){
                    $offer_type = 'CG';
                } else{
                    $offer_type = 'RE';
                }
            } else{
                $offer_type = '';
            }
            RequestDumper::create([
                'user_id' => auth()->user()->id,
                'request_url' => $request->url(),
                'request_data' => serialize($request->all())
            ]);
            $quota = Helper::getOfferQuota(Auth::user());
            if ($quota !== false && $quota < count($data)) throw new \Exception("Your offer quota limit exit");
            PanelModel::whereIn('ansar_id', $data)->update(['locked' => 1]);
            $this->dispatch(new OfferQueue($data, $district_id, Auth::user(), $userOffer,$offer_type));
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return response(collect(['status' => 'error', 'message' => $e->getMessage()])->toJson(), 400, ['Content-Type' => 'application/json']);
        }
        return Response::json(['type' => 'success', 'message' => "Offer Send Successfully"]);
    }

    function removeFromPanel($ansar)
    {
        $pa = $ansar->panel;
        $ansar->status()->update([
            'pannel_status' => 0,
            'offer_sms_status' => 1,
        ]);
        $ansar->panel->panelLog()->save(new PanelInfoLogModel([
            'ansar_id' => $pa->ansar_id,
            'merit_list' => $pa->ansar_merit_list,
            'panel_date' => $pa->panel_date,
            'old_memorandum_id' => !$pa->memorandum_id ? "N\A" : $pa->memorandum_id,
            'movement_date' => Carbon::today(),
            'come_from' => $pa->come_from,
            'move_to' => 'Offer',
        ]));
        $ansar->panel()->delete();
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
        return View::make('HRM::Offer.offer_quota', ['quota' => $quota]);
    }

    function getOfferQuota(Request $request)
    {
        return Response::json(CustomQuery::offerQuota($request->range ? $request->range : 'all'));
    }

    function updateOfferQuota(Request $request)
    {
        //return $request->get('quota_id');
        $rules = [
            'quota_id' => 'required|is_array|array_type:int',
            'quota_value' => 'required|is_array|array_length_same:quota_id'
        ];
        $valid = Validator::make($request->all(), $rules);
        if ($valid->fails()) {
            return Redirect::back()->with('error', "Invalid request");
        }
        $id = $request->get('quota_id');
        $quota = $request->get('quota_value');
        DB::beginTransaction();
        try {
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
                }
                DB::commit();
            }
        } catch (\Exception $e) {
            DB::rollback();
            return Redirect::back()->with('error', $e->getMessage());
        }
        return Redirect::back()->with('success', 'Offer quota updated successfully');
    }

    function handleCancelOffer()
    {
        $rules = [
            'ansar_ids' => 'required|is_array|array_type:int|array_length_min:1'
        ];
        $vaild = Validator::make(Input::all(), $rules);
        if ($vaild->fails()) {
            return response(collect(['type' => 'error', 'message' => "Invalid request(400)"]), 400, ['Content-Type' => 'application/json']);
        }
        $result = ['success' => 0, 'fail' => 0];
        $ansar_ids = Input::get('ansar_ids');
        for ($i = 0; $i < count($ansar_ids); $i++) {
            DB::beginTransaction();
            try {
                $ansar = PersonalInfo::where('ansar_id', $ansar_ids[$i])->first();
                $offered_ansar = $ansar->offer_sms_info;
                if (!$offered_ansar) $received_ansar = $ansar->receiveSMS;
                if ($offered_ansar && $offered_ansar->come_from == 'rest') {
                    $ansar->status()->update([
                        'offer_sms_status' => 0,
                        'rest_status' => 1,
                    ]);
                } else {
                    $panel_log = $ansar->panelLog()->orderBy('panel_date','desc')->first();
                    $ansar->panel()->save(new PanelModel([
                        'memorandum_id' => $panel_log->old_memorandum_id,
                        'panel_date' => $panel_log->panel_date,
                        'come_from' => 'OfferCancel',
                        'ansar_merit_list' => 1,
                        'action_user_id' => auth()->user()->id,
                    ]));
                    $ansar->status()->update([
                        'offer_sms_status' => 0,
                        'pannel_status' => 1,
                    ]);
                }
                $ansar->offerCancel()->save(new OfferCancel([
                    'offer_cancel_date' => Carbon::now()
                ]));
                if ($offered_ansar) {
                    $ansar->offerLog()->save(new OfferSmsLog([
                        'offered_date' => $offered_ansar->sms_send_datetime,
                        'action_date' => Carbon::now(),
                        'offered_district' => $offered_ansar->district_id,
                        'action_user_id' => auth()->user()->id,
                        'reply_type' => 'No Reply',
                    ]));
                    $offered_ansar->delete();
                } else {
                    $ansar->offerLog()->save(new OfferSmsLog([
                        'offered_date' => $received_ansar->sms_send_datetime,
                        'offered_district' => $received_ansar->offered_district,
                        'action_user_id' => auth()->user()->id,
                        'action_date' => Carbon::now(),
                        'reply_type' => 'Yes',
                    ]));
                    $received_ansar->delete();
                }
                DB::commit();
                auth()->user()->actionLog()->save(new ActionUserLog([
                    'ansar_id' => $ansar_ids[$i],
                    'action_type' => 'CANCEL OFFER',
                    'from_state' => 'OFFER',
                    'to_state' => 'PANEL'
                ]));
                $result['success']++;
            } catch (\Exception $e) {
                DB::rollback();
                return response(collect(['type' => 'error', 'message' => $e->getMessage()]), 400, ['Content-Type' => 'application\json']);
            }
        }
        return Response::json(['type' => 'success', 'message' => 'Offer cancel successfully']);
    }

    function cancelOfferView()
    {
        return View::make('HRM::Offer.offer_cancel_view');
    }

    function getOfferedAnsar()
    {
        $rules = [
            'district_id' => 'required|numeric|regex:/^[0-9]+$/'
        ];
        $valid = Validator::make(Input::all(), $rules);
        if ($valid->fails()) {
            return response(collect(['type' => 'error', 'message' => 'Invalid request']), 400, ['Content-Type' => 'application\json']);
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