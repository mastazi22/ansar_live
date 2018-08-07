<?php

namespace App\modules\HRM\Controllers;

use App\Events\ActionUserEvent;
use App\Http\Controllers\Controller;
use App\modules\HRM\Models\AllDisease;
use App\modules\HRM\Models\AllEducationName;
use App\modules\HRM\Models\AllSkill;
use App\modules\HRM\Models\AnsarBankAccountInfoDetails;
use App\modules\HRM\Models\AnsarStatusInfo;
use App\modules\HRM\Models\CustomQuery;
use App\modules\HRM\Models\PersonalInfo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;
use Maatwebsite\Excel\Facades\Excel;

class EntryFormController extends Controller
{

    public function entrylist()
    {
        $user = Auth::user();
        $userType = Auth::user()->type;
        if ($userType == 11 || $userType == 22 || $userType == 33 || $userType == 66) {
            $notVerified = PersonalInfo::where('verified', '1')->orwhere('verified', '0')->count('ansar_id');
            $Verified = Personalinfo::where('verified', '2')->count('ansar_id');

        } elseif ($userType == 55) {
            $notVerified = PersonalInfo::where('verified', '0')->where('user_id', Auth::user()->id)->count('ansar_id');
            $Verified = Personalinfo::where('verified', '1')->where('user_id', Auth::user()->id)->count('ansar_id');
        } else {
            $notVerified = PersonalInfo::where('verified', '1')->count('ansar_id');
            $Verified = Personalinfo::where('verified', '2')->where('user_id', Auth::user()->id)->count('ansar_id');
        }
        return View::make('HRM::Entryform.entrylist')->with(['notVerified' => $notVerified, 'Verified' => $Verified]);
    }

    public function entryform()
    {

        return View::make('HRM::Entryform.entryform');
    }

    public function entryInfo(Request $request)
    {
//        return $request->ansar_id;
        if ($request->ansar_id) {
            $ansar = PersonalInfo::where('ansar_id', $request->ansar_id);
            $e_ansar = DB::table('tbl_embodiment')->join('tbl_kpi_info', 'tbl_embodiment.kpi_id', '=', 'tbl_kpi_info.id')->where('ansar_id', $request->ansar_id);
            if ($request->unit) {
                $ansar->where('unit_id', $request->unit);
                $e_ansar->where('tbl_kpi_info.unit_id', $request->unit);
            }
            if ($request->range) {
                $ansar->where('division_id', $request->range);
                $e_ansar->where('tbl_kpi_info.division_id', $request->range);
            }
            if (Auth::user()->type == 55) {
                $ansar->where('user_id', Auth::user()->id);
            }
            if (!$ansar->exists()) {
                if (Auth::user()->type == 55) {
                    return Redirect::back()->with('entryInfo', '<p class="text text-danger text-center">No Ansar found with this id</p>');
                }
                if (!$e_ansar->exists()) return Redirect::back()->with('entryInfo', '<p class="text text-danger text-center">No Ansar found with this id</p>');
                $ansar = PersonalInfo::where('ansar_id', $request->ansar_id);
            }

            $data = View::make('HRM::Entryform.entry_info', ['ansarAllDetails' => $ansar->first(), 'label' => (object)Config::get('report.label'), 'type' => 'eng', 'title' => (object)Config::get('report.title')]);
            return Redirect::back()->with('entryInfo', $data->render());

        }
        return View::make('HRM::Entryform.entryinfo');
    }

    public function ansarDetails($ansarid)
    {
        $alldetails = PersonalInfo::where('ansar_id', $ansarid)->first();
        return View::make('entryform/viewform')->with('ansarAllDetails', $alldetails);
    }

    public function entryVerify(Request $request)
    {

        $usertype = Auth::user()->type;
        $rules = ['verified_id' => 'required|numeric|regex:/^[0-9]+$/'];
        $valid = Validator::make($request->all(), $rules);
        if ($valid->fails()) {
            return response("Invalid request(400)", 400);
        }
        $verifyid = $request->input('verified_id');
        $ansar = PersonalInfo::where('ansar_id', $verifyid)->first();
        if (empty($ansar->mobile_no_self) || !preg_match('/^[0-9]{11}$/', $ansar->mobile_no_self))
            return Response::json(['status' => false, 'message' => 'This ansar can`t be verified. Because this ansar`s mobile no is empty or invalid']);
        if ($usertype == 55) {
            $success = PersonalInfo::where('ansar_id', $verifyid)->update(['verified' => 1]);
            if ($success) {
                CustomQuery::addActionlog(['ansar_id' => $verifyid, 'action_type' => 'VERIFIED', 'from_state' => 'ENTRY', 'to_state' => 'VERIFIED', 'action_by' => auth()->user()->id]);
                return ['status' => true, 'message' => 'Ansar Verification Complete'];

            } else
                return ['status' => true, 'message' => 'Can`t verify Ansar. Please try again later'];
        }
        if ($usertype == 44 || $usertype == 11 || $usertype == 77 || $usertype == 22 || $usertype == 33 || $usertype == 66) {
            $success = PersonalInfo::where('ansar_id', $verifyid)->update(['verified' => 2]);
            $statusSuccess = AnsarStatusInfo::where('ansar_id', $verifyid)->update(['free_status' => 1]);

            if ($success && $statusSuccess) {
                CustomQuery::addActionlog(['ansar_id' => $verifyid, 'action_type' => 'VERIFIED', 'from_state' => 'VERIFIED', 'to_state' => 'FREE', 'action_by' => auth()->user()->id]);
                return ['status' => true, 'message' => 'Ansar Verification Complete'];
            } else
                return ['status' => true, 'message' => 'Can`t verify Ansar. Please try again later'];
        }
    }

    public function entryChunkVerify(Request $request)
    {
        $user = Auth::user();
        $ids = $request->input('not_verified');
        $rules = [
            'not_verified' => 'required|is_array|array_type:int'
        ];
        $valid = Validator::make($request->all(), $rules);
        if ($valid->fails()) {
            return Response::json(['status' => false, 'message' => 'Invalid Request']);
        }
        $messages = [];
        DB::beginTransaction();
        try {
            for ($i = 0; $i < count($ids); $i++) {
                $ansar = PersonalInfo::where('ansar_id', $ids[$i])->first();
                if (!preg_match('/^(\+88)?[0-9]{11}$/', $ansar->mobile_no_self)) {
                    //return 'error';
                    array_push($messages, ['status' => false, 'message' => 'Ansar id ' . $ansar->ansar_id . ' does not contain valid mobile no']);
                } else {
                    if ($user->type == 55) {
                        $ansar->verified = 1;
                        $ansar->save();
                    } else {
                        $ansar->verified = 2;
                        $ansar->status->free_status = 1;
                        $ansar->save();
                        $ansar->status->save();
                    }
                    CustomQuery::addActionlog(['ansar_id' => $ids[$i], 'action_type' => 'VERIFIED', 'from_state' => 'ENTRY', 'to_state' => 'VERIFIED', 'action_by' => auth()->user()->id]);
                    array_push($messages, ['status' => true, 'message' => $ansar->ansar_id . ' verified successfully']);
                    DB::commit();
                }


            }
        } catch (\Exception $e) {
            DB::rollback();
            return Response::json(['status' => false, 'messege' => $e->getMessage()]);
        }
        return Response::json(['status' => true, 'messege' => $messages]);
    }

    public function Reject(Request $request)
    {
        $user = Auth::user();
        $usertype = $user->type;
        $rules = ['reject_id' => 'required|numeric|regex:/^[0-9]+$/'];
        $valid = Validator::make($request->all(), $rules);
        if ($valid->fails()) {
            return response("Invalid request(400)", 400);
        }
        $rejectid = $request->input('reject_id');

        if ($usertype == 44) {
            $success = PersonalInfo::where('ansar_id', $rejectid)->update(['verified' => 0]);
            if ($success) {
                Event::fire(new ActionUserEvent(['ansar_id' => $rejectid, 'action_type' => 'REJECT', 'from_state' => 'VERIFIED', 'to_state' => 'ENTRY', 'action_by' => auth()->user()->id]));
                return 1;

            } else
                return 0;
        }
    }

    public function entryReport(Request $request, $ansarid, $type = 'eng')
    {
        $ansardetails = PersonalInfo::where('ansar_id', $ansarid);
        if ($request->unit) $ansardetails->where('unit_id', $request->unit);
        if ($request->range) $ansardetails->where('division_id', $request->range);
        if (!$ansardetails->exists()) abort(404);
        return View::make('HRM::Entryform.reportEntryForm')->with(['ansarAllDetails' => $ansardetails->first(), 'type' => $type, 'title' => (object)Config::get('report.title'), 'label' => (object)Config::get('report.label')]);
    }

    public function getfreezelist()
    {

    }

    public function getAllDisease()
    {
        $allDisease = AllDisease:: orderBy('id', 'desc')->get();
        return Response::json($allDisease);
    }

    public function getAllSkill()
    {
        $allSkill = AllSkill::orderBy('id', 'desc')->get();
        return Response::json($allSkill);
    }

    public function entryAdvancedSearch()
    {
        return View::make('HRM::Entryform.advancedsearch');
    }

    public function ansarOriginalInfo()
    {
        return View::make('HRM::Entryform.originalinfo');
    }

    public function getAllEducation()
    {
//        $allEducation = AllEducationName::where('id', '!=', 0)->get();
        $allEducation = AllEducationName::all();
        return Response::json($allEducation);
    }

    public function bulkUploadBankInfo(Request $request)
    {
        if (strtolower($request->method()) == "post") {
            $rules = [
                'bulk_bank_account_info' => ['required', 'mimes:xls,xlsx'],
            ];
            $this->validate($request, $rules, [
                'bulk_bank_account_info.required' => "Please attache a file.(Supported formats are xlsx,xls)",
                'bulk_bank_account_info.mimes' => "File format not supported."
            ]);
            $data = Excel::load($request->file()['bulk_bank_account_info'], function ($excel) {
            })->get();
            foreach ($data as $d) {
                $d = json_decode(json_encode($d));
                $header = $d[0];
                unset($d[0]);
                $rows = [];
                $index = 0;
//                foreach ($d as $dd) {
//                    if (!is_numeric($dd[1])) {
//                        echo "id column not found.\n";
//                    } else if (!empty(trim($dd[1])) && !empty(trim($dd[4])) && !empty(trim($dd[2]))) {
//                        $dataRow["ansar_id"] = trim($dd[1]);
//
//                        if (strtolower(trim($dd[2])) === "rocket" || strtolower(trim($dd[2])) === "bkash") {
//                            $dataRow["mobile_bank_account_no"] = trim($dd[4]);
//                            $dataRow["mobile_bank_type"] = trim($dd[2]);
//                            $dataRow["prefer_choice"] = "mobile";
//                        } else {
//                            $dataRow["bank_name"] = trim($dd[2]);
//                            $dataRow["account_no"] = trim($dd[3]);
//                            $dataRow["prefer_choice"] = "general";
//                        }
//                        $bankInfo = AnsarBankAccountInfoDetails::firstOrCreate($dataRow);
//                        $index++;
//                    } else {
//                        echo "Invalid Data\n";
//                    }
//                }
                return View::make('HRM::Entryform.upload_bank_info')->with("message", "Successfully add " . $index . " accounts.<br/>");
            }
        } else {
            return View::make('HRM::Entryform.upload_bank_info');
        }
    }
}