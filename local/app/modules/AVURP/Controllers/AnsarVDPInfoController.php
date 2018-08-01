<?php

namespace App\modules\AVURP\Controllers;

use App\Helper\EmailHelper;
use App\Helper\Facades\LanguageConverterFacades;
use App\Http\Controllers\Controller;
use App\modules\AVURP\Models\VDPAnsarInfo;
use App\modules\AVURP\Repositories\VDPInfo\VDPInfoRepository;
use App\modules\AVURP\Requests\VDPInfoRequest;
use App\modules\HRM\Models\AllEducationName;
use App\modules\HRM\Models\Blood;
use App\modules\HRM\Models\Edication;
use App\modules\HRM\Models\Thana;
use App\modules\HRM\Models\Unions;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Facades\Image;
use Maatwebsite\Excel\Facades\Excel;

class AnsarVDPInfoController extends Controller
{
    use EmailHelper;
    private $infoRepository;

    /**
     * AnsarVDPInfoController constructor.
     * @param VDPInfoRepository $infoRepository
     */
    public function __construct(VDPInfoRepository $infoRepository)
    {
        $this->infoRepository = $infoRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $limit = $request->limit ? $request->limit : 30;
            if (auth()->user()->usertype->type_name == "Dataentry") {
                $vdp_infos = $this->infoRepository->getInfos($request->only(['range', 'unit', 'thana']), $limit, $request->action_user_id);
            } else $vdp_infos = $this->infoRepository->getInfos($request->only(['range', 'unit', 'thana']), $limit);
            return view('AVURP::ansar_vdp_info.data', compact('vdp_infos'));
        }
        return view('AVURP::ansar_vdp_info.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        return view('AVURP::ansar_vdp_info.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param VDPInfoRequest|Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(VDPInfoRequest $request)
    {
//        return $request->file('profile_pic');

        $response = $this->infoRepository->create($request);
        if (!$response['status']) {
            return response()->json($response['data'], 500);
        }
        Session::flash('success_message', 'New entry added successfully');
        return response()->json($response['data']);
    }

    /**
     * Display the specified resource.
     *
     * @param Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        if (auth()->user()->usertype->type_name == "Dataentry") {
            $info = $this->infoRepository->getInfo($id, $request->action_user_id);
        } else $info = $this->infoRepository->getInfo($id);
        if ($request->ajax()) {
            return response()->json($info);
        }
        if (!$info) return abort(404);

        return view('AVURP::ansar_vdp_info.view', compact('info'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
        if (auth()->user()->usertype->type_name == "Dataentry") {
            $info = $this->infoRepository->getInfoForEdit($id, $request->action_user_id);
        } else $info = $this->infoRepository->getInfoForEdit($id);
        if ($request->ajax()) {
//            return $id;
            return response()->json($info);
        }
        if (!$info) return abort(404);
        return view('AVURP::ansar_vdp_info.edit', compact('id'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(VDPInfoRequest $request, $id)
    {

        $response = $this->infoRepository->update($request, $id);
        if (!$response['status']) {
            return response()->json($response['data'], 500);
        }
        Session::flash('success_message', 'data updated successfully');
        return response()->json($response['data']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function verifyVDP($id)
    {
        $response = $this->infoRepository->verifyVDP($id);
        if (!$response['status']) {
            return redirect()->route('AVURP.info.index')->with('error_message', $response['data']['message']);
        }
        return redirect()->route('AVURP.info.index')->with('success_message', $response['data']['message']);
    }

    public function approveVDP($id)
    {
        $response = $this->infoRepository->approveVDP($id);
        if (!$response['status']) {
            return redirect()->route('AVURP.info.index')->with('error_message', $response['data']['message']);
        }
        return redirect()->route('AVURP.info.index')->with('success_message', $response['data']['message']);
    }

    public function verifyAndApproveVDP($id)
    {
        $response = $this->infoRepository->verifyAndApproveVDP($id);
        if (!$response['status']) {
            return redirect()->route('AVURP.info.index')->with('error_message', $response['data']['message']);
        }
        return redirect()->route('AVURP.info.index')->with('success_message', $response['data']['message']);
    }

    public function loadImage($id)
    {
        $info = VDPAnsarInfo::find($id);
        if ($info && $info->profile_pic) {
            $image = storage_path('avurp/profile_pic') . '/' . $info->profile_pic;
            if (!File::exists($image) || File::isDirectory($image)) $image = public_path('dist/img/nimage.png');
            //return $image;

        } else {
            $image = public_path('dist/img/nimage.png');
        }
        return Image::make($image)->response();
    }

    public function import()
    {
        return view('AVURP::ansar_vdp_info.import');
    }

    public function processImportedFile(Request $request)
    {
//        return $request->all();
        $rules = [
            "entry_unit" => 'required|regex:/^[1-5]{1}$/',
            "division_id" => 'required',
            "unit_id" => 'required',
//            "thana_id" => 'required',
            "union_id" => 'required_if:entry_unit,3|required_if:entry_unit,4|required_if:entry_unit,5',
            "import_file" => 'required',

        ];
        $this->validate($request, $rules);
//        return ($request->entry_unit=='3'||$request->entry_unit=='4')?"dddd":'false';
        if ($request->hasFile('import_file')) {
            $ms = ["অবিবাহিত" => "Unmarried", "বিবাহিত" => "Married"];
            $fields = [
                "sl_no", "division", "range", "unit", "thana", "union_id", "union_word_id", "village_house_no", "post_office_name",
                "ansar_name_eng", "ansar_name_bng", "designation", "father_name_bng", "mother_name_bng",
                "date_of_birth", "birth_date_base", "marital_status", "spouse_name_bng", "national_id_no",
                "smart_card_id", "avub_id", "mobile_no_self", "email_fb_id", "height", "blood_group", "gender", "health_condition",
                "education", "training"
            ];
            $fields_extended = [
                "sl_no", "division", "range", "unit", "thana", "union_id", "union_word_id", "village_house_no", "post_office_name",
                "ansar_name_eng", "ansar_name_bng", "designation", "father_name_bng", "mother_name_bng",
                "date_of_birth", "birth_date_base", "marital_status", "spouse_name_bng", "national_id_no",
                "smart_card_id", "avub_id", "mobile_no_self", "email_fb_id", "height", "blood_group", "gender", "health_condition",
                "education", "training","bank_account_no","bank_name","bank_branch"
            ];
            $keys = ["village_house_no", "post_office_name",
                "ansar_name_eng", "ansar_name_bng", "designation", "father_name_bng", "mother_name_bng",
                "marital_status", "spouse_name_bng", "national_id_no",
                "smart_card_id", "avub_id",  "health_condition"];
            $sheets = Excel::load($request->file('import_file'), function () {

            })->get();
//return $sheets;
            $all_data = [];
            $error_headers = [];
            foreach ($sheets as $sheet) {
                $rows = collect($sheet)->toArray();
                $error_headers = [$rows[0],$rows[1],$rows[2],$rows[3],$rows[4]];
                unset($rows[0]);
                unset($rows[1]);
                unset($rows[2]);
                unset($rows[3]);
                unset($rows[4]);

                foreach ($rows as $row) {
                    if(count($row)>32) $row = array_slice($row, 0, 32);
                    if(count($row)==count($fields))array_push($all_data, array_combine($fields, array_slice($row, 0, count($fields))));
                    else if(count($row)==count($fields_extended))array_push($all_data, array_combine($fields_extended, array_slice($row, 0, count($fields_extended))));
                   // array_push($all_data, [count($row),count($fields_extended),count($fields)]);
                }
            }
//            return $all_data;
            $insertData = [];
            foreach ($all_data as $data) {
                $r = [];
                $r["division_id"] = $request->division_id;
                $r["unit_id"] = $request->unit_id;
                if($request->thana_id)$r["thana_id"] = $request->thana_id;
                if($request->entry_unit==3||$request->entry_unit==4||$request->entry_unit==5)$r["union_id"] = $request->union_id;

                $r["entry_unit"] = $request->entry_unit;
                foreach ($data as $key => $value) {
                    if (in_array($key, $keys)) {
                        $r[$key] = $value;
                    } else if ($key == 'blood_group') {
                        $m = null;
                        preg_match_all('/[^\(\)VE]/', $value, $m);
                        if (count($m) > 0 && is_array($m[0])) {
                            $bg = implode('', $m[0]);
                            $b = Blood::where('blood_group_name_eng', $bg)->orWhere('blood_group_name_bng', $bg)->first();
                            $r['blood_group_id'] = $b ? $b->id : 0;
                        } else {
                            $r['blood_group_id'] = 0;
                        }
                    } else if ($key == 'height') {
                        $m = [];
                        preg_match_all('/(?:(?![ফিটফুট\-ইঞ্চি\'\"”\s]+).)/', LanguageConverterFacades::bngToEng($value), $m);
                        $r['height_feet'] = isset($m[0][0]) > 0 ? $m[0][0] : '';
                        $r['height_inch'] = isset($m[0][1]) > 0 ? $m[0][1] : '';
//                        $r['height'] = $m;
//                        return $m;
                    } else if ($key == 'date_of_birth') {
                        $r['date_of_birth'] = $this->parseDate($value);
                    }else if ($key == 'mobile_no_self') {
                        $split = str_split($value);
                        if(intval($split[0])>0){
                            $mobile_no = "0".$value;
                        } else{
                            $mobile_no = $value;
                        }
                        $r['mobile_no_self'] = $mobile_no;
                    } else if ($key == 'marital_status') {
                        $r['marital_status'] = $ms[$value];
                    } else if ($key == 'gender') {
                        $r['gender'] = $value == "পুরুষ" ? "Male" : "Female";
                    } else if ($key == 'education') {
                        $r['educationInfo'][] = [
                            'education_id' => $this->parseEducation($value)
                        ];
                    } else if ($key == 'training') {
                        if (preg_match('/ভিডিপি/', $value)) {
                            $r['training_info'][] = [
                                'training_id' => 3,
                                'sub_training_id' => 0,
                            ];
                        } else if (preg_match('/আনসার/', $value)) {
                            $r['training_info'][] = [
                                'training_id' => 7,
                                'sub_training_id' => 0,
                            ];
                        }
                    } else if ($key == 'union_word_id') {
                        $uwi = intval(LanguageConverterFacades::bngToEng($value));
                        $r["union_word_id"] = $uwi;
                    }else if ($key == 'thana'&&($request->entry_unit==1)) {
                        DB::enableQueryLog();
                        $uni =  Thana::where('division_id',$r["division_id"])
                            ->where('unit_id',$r["unit_id"])
                            ->where(DB::raw("INSTR(\"$value\",thana_name_bng)"),'>',0)
                            ->first();
                        if($uni){
                            Log::info("$value found");
                            $r["thana_id"] = $uni->id;
                        }
                    }else if ($key == 'union_id'&&($request->entry_unit==1)&&isset($r["thana_id"])) {
                        DB::enableQueryLog();
                        $uni =  Unions::where('division_id',$r["division_id"])
                            ->where('unit_id',$r["unit_id"])
                            ->where('thana_id',$r["thana_id"])
                            ->where(DB::raw("INSTR(\"$value\",union_name_bng)"),'>',0)
                            ->first();
                        if($uni){
                            Log::info("$value found");
                            $r["union_id"] = $uni->id;
                        }
                    }else if ($key == 'bank_account_no') {
                        $ban = intval(LanguageConverterFacades::bngToEng($value));
                        if(!isset($r["bank_account_info"])) $r["bank_account_info"] = [];
                        $r["bank_account_info"]["account_no"] = $ban;
                    }else if ($key == 'bank_name') {
                        if(!isset($r["bank_account_info"])) $r["bank_account_info"] = [];
                        if(strpos($value,"রকেট")!==false){
                            $r["bank_account_info"]["mobile_bank_type"] = "rocket";
                            $r["bank_account_info"]["prefer_choice"] = "mobile";
                        } else if(strpos($value,"বিকাশ")!==false){
                            $r["bank_account_info"]["mobile_bank_type"] = "bkash";
                            $r["bank_account_info"]["prefer_choice"] = "mobile";
                        } else{
                            $r["bank_account_info"]["bank_name"] = $value;
                            $r["bank_account_info"]["prefer_choice"] = "general";
                        }

                    }else if ($key == 'bank_branch') {
                        if(!isset($r["bank_account_info"])) $r["bank_account_info"] = [];
                        $r["bank_account_info"]["branch_name"] = $value;

                    }
                }
                $insertData[] = $r;
//                return $r?;

            }
            $res = [
                "success" => 0,
                "fail" => 0
            ];
//            return $insertData;
//            Log::info($insertData);
//            return $insertData?"sssss":"dddddd";
            $error_data = [];
            $index = 0;
            foreach ($insertData as $i) {
                if ($i['smart_card_id'] && strlen($i['smart_card_id']) > 5) $i['smart_card_id'] = substr($i['smart_card_id'], -5);
                $request->replace($i);
                $valid = Validator::make($i, [
                    'ansar_name_bng' => 'required',
//                    'ansar_name_eng' => 'required',
//                    'father_name_bng' => 'required',
//                    'mother_name_bng' => 'required',
//                    'designation' => 'required',
//                    'date_of_birth' => 'required',
//                    'marital_status' => 'required',
                    'national_id_no' => 'unique:avurp.avurp_vdp_ansar_info',
                    'mobile_no_self' => 'required|unique:avurp.avurp_vdp_ansar_info,mobile_no_self',
                    'height_feet' => '',
                    'height_inch' => '',
                    'blood_group_id' => '',
                    'gender' => 'required',
                    'health_condition' => '',
                    'division_id' => 'required|numeric|min:1',
                    'unit_id' => 'required|numeric|min:1',
                    'thana_id' => 'required|numeric|min:1',
                    'union_id' => 'required|numeric|min:1',
                    'union_word_id' => 'required|numeric|min:1',
                    'smart_card_id' => 'sometimes|exists:hrm.tbl_ansar_parsonal_info,ansar_id|unique:avurp.avurp_vdp_ansar_info',
                    'post_office_name' => '',
                    'village_house_no' => '',
                    //'educationInfo'=>'required',
                    //'training_info'=>'required',
                    /*'educationInfo.*.education_id'=>'required|numeric|min:1',
                    'educationInfo.*.institute_name'=>'required',*/
                    //'training_info.*.training_id'=>'required|numeric|min:1',
                    //'training_info.*.sub_training_id'=>'required|numeric|min:1',

                ]);
                if ($valid->fails()) {
                    if(count($all_data[$index])==count($fields)) array_push($error_data, ["dd"=>array_merge(array_combine($fields,array_values($all_data[$index])),["errors"=>$this->validationErrorsToString($valid->messages())]),"err"=>array_keys(collect($valid->messages())->toArray())]);
                    if(count($all_data[$index])==count($fields_extended)) array_push($error_data, ["dd"=>array_merge(array_combine($fields_extended,array_values($all_data[$index])),["errors"=>$this->validationErrorsToString($valid->messages())]),"err"=>array_keys(collect($valid->messages())->toArray())]);
                    $res["fail"]++;
                } else {
                    $response = $this->infoRepository->create($request, auth()->user()->id);
                    if ($response['status']) $res["success"]++;
                    else {
                        $res["fail"]++;
                        if(count($all_data[$index])==count($fields))array_push($error_data, ["dd"=>array_merge(array_combine($fields,array_values($all_data[$index])),["errors"=>$response['data']['message']]),"err"=>[]]);
                        else if(count($all_data[$index])==count($fields_extended))array_push($error_data, ["dd"=>array_merge(array_combine($fields_extended,array_values($all_data[$index])),["errors"=>$response['data']['message']]),"err"=>[]]);
//                        array_push($error_data, array_merge($all_data[$index],["errors"=>$response['data']['message']]));
                    }
                }
                $index++;

            }
            if (count($error_data) > 0) {
                for ($i=0;$i<count($error_headers);$i++){
                    array_push($error_headers[$i],"errors");
                }
                $file_name = 'error_date_' . time();
                Excel::create($file_name, function ($excel) use ($error_data, $error_headers) {

                    $excel->sheet('sheet1', function ($sheet) use ($error_data, $error_headers) {
                        $sheet->setAutoSize(false);
                        $sheet->setWidth('A', 5);
                        $sheet->loadView('AVURP::ansar_vdp_info.import_error', ['error_datas' => $error_data, 'headers' => $error_headers]);
                    });
                })->store('xls', storage_path());
                $this->sendEmailRaw("error data","arafat@shurjomukhi.com.bd","ERROR",storage_path($file_name.".xls"));
            }

            return response()->json(['data' => $res, 'error' => isset($file_name) ? $file_name : false]);
        }
        return response()->json(['status' => false, 'msg' => 'Please upload excel u want to import']);
    }

    private function parseDate($date)
    {
        $value = LanguageConverterFacades::bngToEng($date);
        $formats = [
            "d-m-y",
            "d/m/y",
            "d.m.y",
            "d-m-Y",
            "d/m/Y",
            "d.m.Y",
            "j-m-y",
            "j/m/y",
            "j.m.y",
            "j-m-Y",
            "j/m/Y",
            "j.m.Y",
            "d-n-y",
            "d/n/y",
            "d.n.y",
            "d-n-Y",
            "d/n/Y",
            "d.n.Y",
            "j-n-y",
            "j/n/y",
            "j.n.y",
            "j-n-Y",
            "j/n/Y",
            "j.n.Y"
        ];
        $validDate = false;
        if(!$value) return null;
        foreach ($formats as $format) {
            try {
                $d = Carbon::createFromFormat($format, $value)->format('Y-m-d');
                $validDate = $d;
                break;
            } catch (\Exception $e) {

            }
        }
        return $validDate ? $validDate : null;
    }

    private function parseEducation($value)
    {
        if (preg_match('/অস্তম|অষ্টম|৮ম|8/', $value)) {
            $edu = AllEducationName::where('education_deg_bng', 'LIKE', "%অষ্টম%")->first();
            return $edu ? $edu->id : 0;
        } else if (preg_match('/নবম|৯ম|9/', $value)) {
            $edu = AllEducationName::where('education_deg_bng', 'LIKE', "%নবম%")->first();
            return $edu ? $edu->id : 0;
        } else if (preg_match('/সপ্তম|৭ম|7/', $value)) {
            $edu = AllEducationName::where('education_deg_bng', 'LIKE', "%সপ্তম%")->first();
            return $edu ? $edu->id : 0;
        } else if (preg_match('/ষষ্ঠ|৬ষ্ঠ|৬ম|6/', $value)) {
            $edu = AllEducationName::where('education_deg_bng', 'LIKE', "%ষষ্ঠ%")->first();
            return $edu ? $edu->id : 0;
        } else if (preg_match('/পঞ্চম|৫ম|5/', $value)) {
            $edu = AllEducationName::where('education_deg_bng', 'LIKE', "%পঞ্চম%")->first();
            return $edu ? $edu->id : 0;
        } else if (preg_match('/দশম|১০ম|10/', $value)) {
            $edu = AllEducationName::where('education_deg_bng', 'LIKE', "%দশম%")->first();
            return $edu ? $edu->id : 0;
        } else if (preg_match('/এস.এস.সি|এস,এস,সি|এস\s+এস\s+সি/', $value)) {
            $edu = AllEducationName::where('education_deg_bng', 'LIKE', "%এস.এস.সি%")->first();
            return $edu ? $edu->id : 0;
        } else if (preg_match('/এইচ.এস.সি|এইচ,এস,সি|এইচ\s+এস\s+সি/', $value)) {
            $edu = AllEducationName::where('education_deg_bng', 'LIKE', "%এইচ.এস.সি%")->first();
            return $edu ? $edu->id : 0;
        } else if (preg_match('/বি.এ/', $value)) {
            $edu = AllEducationName::where('education_deg_bng', 'LIKE', "%স্নাতক%")->first();
            return $edu ? $edu->id : 0;
        }
        $edu = AllEducationName::where('education_deg_bng', 'LIKE', "%$value%")->first();
        return $edu ? $edu->id : 0;
    }

    public function downloadFile($file_name)
    {
        $path = storage_path($file_name . ".xls");
        if (File::exists($path)) {
            return response()->download($path)->deleteFileAfterSend(true);
        }
        abort(404);
    }

    private function validationErrorsToString($errArray)
    {
        $valArr = array();
        $errStrFinal = '';
        foreach ($errArray->toArray() as $key => $value) {
            $errStr = $key . ' ' . $value[0];
            array_push($valArr, $errStr);
        }
        if (!empty($valArr)) {
            $errStrFinal = implode(',', $valArr);
        }
        return $errStrFinal;
    }
}
