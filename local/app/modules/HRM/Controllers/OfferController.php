<?php

namespace App\modules\HRM\Controllers;

use App\Helper\Helper;
use App\Http\Controllers\Controller;
use App\Jobs\OfferQueue;
use App\Jobs\RearrangePanelPositionGlobal;
use App\Jobs\RearrangePanelPositionLocal;
use App\modules\HRM\Models\ActionUserLog;
use App\modules\HRM\Models\AnsarStatusInfo;
use App\modules\HRM\Models\CustomQuery;
use App\modules\HRM\Models\OfferCancel;
use App\modules\HRM\Models\OfferQuota;
use App\modules\HRM\Models\OfferSmsLog;
use App\modules\HRM\Models\OfferSMSStatus;
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
            $offerZone = OfferZone::where('unit_id', $user->district_id)->pluck('offer_zone_unit_id')->toArray();
            $data = CustomQuery::getAnsarInfo(
                ['male' => $request->get('pc_male'), 'female' => $request->get('pc_female')],
                ['male' => $request->get('apc_male'), 'female' => $request->get('apc_female')],
                ['male' => $request->get('ansar_male'), 'female' => $request->get('ansar_female')],
                $request->get('district'),
                $request->get('exclude_district'), $user, $offerZone);
//            return $data;
            Log::info($request->all());
            $user = Auth::user();
            if ($user->type == 22) {

                if (in_array($user->district_id, Config::get('app.offer'))) {
                    $offer_type = 'GB';
                } else {
                    $offer_type = 'RE';
                }
            } else {
                if (in_array($request->get('district'), Config::get('app.offer'))) {
                    $offer_type = 'GB';
                } else {
                    $offer_type = 'RE';
                }
            }
            RequestDumper::create([
                'user_id' => auth()->user()->id,
                'request_url' => $request->url(),
                'request_data' => serialize($request->all())
            ]);
            $quota = Helper::getOfferQuota(Auth::user());
            if ($quota !== false && $quota < count($data)) throw new \Exception("Your offer quota limit exit");
            PanelModel::whereIn('ansar_id', $data)->update(['locked' => 1]);
            $this->dispatch(new OfferQueue($data, $district_id, Auth::user(), $userOffer, $offer_type));
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
                $panel_date = Carbon::now()->format("Y-m-d H:i:s");
                $offered_ansar = $ansar->offer_sms_info;
                $os = OfferSMSStatus::where('ansar_id', $ansar_ids[$i])->first();
                if (!$offered_ansar) $received_ansar = $ansar->receiveSMS;
                if ($offered_ansar && $offered_ansar->come_from == 'rest') {
                    $ansar->status()->update([
                        'offer_sms_status' => 0,
                        'rest_status' => 1,
                    ]);
                } else {
                    $pa = $ansar->panel;
                    if (!$pa) {
                        $panel_log = $ansar->panelLog()->first();
                        $ansar->panel()->save(new PanelModel([
                            'memorandum_id' => $panel_log->old_memorandum_id,
                            'panel_date' => $os && $os->isGlobalOfferRegion() ? $panel_date : $panel_log->panel_date,
                            're_panel_date' => $os && $os->isRegionalOfferRegion() ? $panel_date : $panel_log->re_panel_date,
                            'come_from' => 'OfferCancel',
                            'ansar_merit_list' => 1,
                            'action_user_id' => auth()->user()->id,
                        ]));

                    } else {
                        $pa->locked = 0;
                        $pa->come_from = 'OfferCancel';
                        if ($os && $os->isGlobalOfferRegion()) {
                            $pa->panel_date = $panel_date;
                        } elseif ($os && $os->isRegionalOfferRegion()) {
                            $pa->re_panel_date = $panel_date;
                        }
                        $pa->save();
                    }
                    $ansar->status()->update([
                        'offer_sms_status' => 0,
                        'pannel_status' => 1,
                    ]);
                }
                $ansar->offerCancel()->save(new OfferCancel([
                    'offer_cancel_date' => Carbon::now()
                ]));

                if ($os) {
                    $ot = explode(",", $os->offer_type);
                    $ou = explode(",", $os->last_offer_units);
                    $ot = array_slice($ot, 0, count($ot) - 1);
                    $ou = array_slice($ou, 0, count($ou) - 1);
                    $os->offer_type = implode(",", $ot);
                    $os->last_offer_units = implode(",", $ou);
                    $os->last_offer_unit = !count($ou) ? "" : $ou[count($ou) - 1];
                    $os->save();
                }
                if ($offered_ansar) {
                    $ansar->offerLog()->save(new OfferSmsLog([
                        'offered_date' => $offered_ansar->sms_send_datetime,
                        'action_date' => Carbon::now(),
                        'offered_district' => $offered_ansar->district_id,
                        'action_user_id' => auth()->user()->id,
                        'reply_type' => 'No Reply',
                        'comment' => 'Offer Cancel'
                    ]));
                    $offered_ansar->delete();
                } else {
                    $ansar->offerLog()->save(new OfferSmsLog([
                        'offered_date' => $received_ansar->sms_send_datetime,
                        'offered_district' => $received_ansar->offered_district,
                        'action_user_id' => auth()->user()->id,
                        'action_date' => Carbon::now(),
                        'reply_type' => 'Yes',
                        'comment' => 'Offer Cancel'
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
        if (count($ansar_ids)) {
            $this->dispatch(new RearrangePanelPositionGlobal());
            $this->dispatch(new RearrangePanelPositionLocal());
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
        $gender = Input::get('gender');
        $rank = Input::get('rank');
        $offer_noreply_ansar = DB::table('tbl_sms_offer_info')
            ->join('tbl_ansar_parsonal_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_sms_offer_info.ansar_id')
            ->join('tbl_designations', 'tbl_ansar_parsonal_info.designation_id', '=', 'tbl_designations.id')
            ->join('tbl_units', 'tbl_ansar_parsonal_info.unit_id', '=', 'tbl_units.id')
            ->where('tbl_sms_offer_info.district_id', '=', $district_id);
        $offer_accepted_ansar = DB::table('tbl_sms_receive_info')
            ->join('tbl_ansar_parsonal_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_sms_receive_info.ansar_id')
            ->join('tbl_designations', 'tbl_ansar_parsonal_info.designation_id', '=', 'tbl_designations.id')
            ->join('tbl_units', 'tbl_ansar_parsonal_info.unit_id', '=', 'tbl_units.id')
            ->where('tbl_sms_receive_info.offered_district', '=', $district_id);

        if (isset($gender) && ($gender == 'Male' || $gender == 'Female' || $gender == 'Other')) {
            $offer_noreply_ansar = $offer_noreply_ansar->where('tbl_ansar_parsonal_info.sex', '=', $gender);
            $offer_accepted_ansar = $offer_accepted_ansar->where('tbl_ansar_parsonal_info.sex', '=', $gender);
        }
        if (isset($rank) && !empty($rank) && is_numeric($rank)) {
            $offer_noreply_ansar = $offer_noreply_ansar->where('tbl_designations.id', '=', $rank);
            $offer_accepted_ansar = $offer_accepted_ansar->where('tbl_designations.id', '=', $rank);
        }

        $clone_offer_noreply_ansar = clone $offer_noreply_ansar;
        $clone_offer_accepted_ansar = clone $offer_accepted_ansar;

        $count_offer_noreply_ansar = $clone_offer_noreply_ansar->groupBy('tbl_designations.id')->select(DB::raw("count('tbl_ansar_parsonal_info.ansar_id') as t"), 'tbl_designations.code');
        $count_offer_accepted_ansar = $clone_offer_accepted_ansar->groupBy('tbl_designations.id')->select(DB::raw("count('tbl_ansar_parsonal_info.ansar_id') as t"), 'tbl_designations.code');
        $a1 = collect($count_offer_noreply_ansar->get())->pluck('t', 'code')->toArray();
        $a2 = collect($count_offer_accepted_ansar->get())->pluck('t', 'code')->toArray();

        $sums = [];
        foreach (array_keys($a1 + $a2) as $key) {
            $sums[$key] = (isset($a1[$key]) ? $a1[$key] : 0) + (isset($a2[$key]) ? $a2[$key] : 0);
        }

        $offer_noreply_ansar = $offer_noreply_ansar->select('tbl_sms_offer_info.ansar_id', 'tbl_sms_offer_info.sms_send_datetime', 'tbl_sms_offer_info.sms_end_datetime', 'tbl_sms_offer_info.district_id', 'tbl_ansar_parsonal_info.ansar_name_bng', 'tbl_ansar_parsonal_info.sex', 'tbl_designations.name_bng', 'tbl_units.unit_name_bng');
        $offer_accepted_ansar = $offer_accepted_ansar->select('tbl_sms_receive_info.ansar_id', 'tbl_sms_receive_info.sms_send_datetime', 'tbl_sms_receive_info.sms_end_datetime', 'tbl_sms_receive_info.offered_district', 'tbl_ansar_parsonal_info.ansar_name_bng', 'tbl_ansar_parsonal_info.sex', 'tbl_designations.name_bng', 'tbl_units.unit_name_bng');
        $list = $offer_noreply_ansar->unionAll($offer_accepted_ansar);

        return Response::json(['list' => $list->get(), 'tCount' => $sums]);
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