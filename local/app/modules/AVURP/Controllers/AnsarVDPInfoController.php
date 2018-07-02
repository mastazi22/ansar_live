<?php

namespace App\modules\AVURP\Controllers;

use App\Helper\Facades\LanguageConverterFacades;
use App\Http\Controllers\Controller;
use App\modules\AVURP\Models\VDPAnsarInfo;
use App\modules\AVURP\Repositories\VDPInfo\VDPInfoRepository;
use App\modules\AVURP\Requests\VDPInfoRequest;
use App\modules\HRM\Models\AllEducationName;
use App\modules\HRM\Models\Blood;
use App\modules\HRM\Models\Edication;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Facades\Image;
use Maatwebsite\Excel\Facades\Excel;

class AnsarVDPInfoController extends Controller
{
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
            "division_id" => 'required',
            "unit_id" => 'required',
            "thana_id" => 'required',
            "union_id" => 'required',
            "import_file" => 'required',
        ];
        $this->validate($request, $rules);
        if ($request->hasFile('import_file')) {
            $ms = ["অবিবাহিত" => "Unmarried", "বিবাহিত" => "Married"];
            $fields = [
                "sl_no", "division", "range", "unit", "thana", "union", "union_word_id", "village_house_no", "post_office_name",
                "ansar_name_eng", "ansar_name_bng", "designation", "father_name_bng", "mother_name_bng",
                "date_of_birth", "birth_date_base", "marital_status", "spouse_name_bng", "national_id_no",
                "smart_card_id", "avub_id", "mobile_no_self", "email_fb_id", "height", "blood_group", "gender", "health_condition",
                "education", "training"
            ];
            $keys = [ "village_house_no", "post_office_name",
                "ansar_name_eng", "ansar_name_bng", "designation", "father_name_bng", "mother_name_bng",
                "marital_status", "spouse_name_bng", "national_id_no",
                "smart_card_id", "avub_id", "mobile_no_self","health_condition"];
            $sheets = Excel::load($request->file('import_file'), function () {

            })->get();
            $all_data = [];
            foreach ($sheets as $sheet) {
                $rows = collect($sheet)->toArray();

                unset($rows[0]);
                unset($rows[1]);
                unset($rows[2]);
                unset($rows[3]);
                unset($rows[4]);

                foreach ($rows as $row) {
                    array_push($all_data, array_combine($fields, array_slice($row,0,count($fields))));
//                    array_push($all_data, [count($fields),array_slice($row,0,count($fields))]);
                }
            }
//            return $all_data;
            $insertData = [];
            foreach ($all_data as $data) {
                $r = [];
                $r["division_id"] = $request->division_id;
                $r["unit_id"] = $request->unit_id;
                $r["thana_id"] = $request->thana_id;
                $r["union_id"] = $request->union_id;
                foreach ($data as $key => $value) {
                    if (in_array($key, $keys)) {
                        $r[$key] = $value;
                    } else if ($key == 'blood_group') {
                        $m = null;
                        preg_match_all('/[^\(\)VE]/', $value, $m);
                        if (count($m) > 0 && is_array($m[0])) {
                            $bg = implode('', $m[0]);
                            $b = Blood::where('blood_group_name_eng', $bg)->orWhere('blood_group_name_bng', $bg)->first();
                            $r['blood_group_id'] = $b?$b->id:0;
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
                    }else if ($key == 'marital_status') {
                        $r['marital_status'] = $ms[$value];
                    }else if ($key == 'gender') {
                        $r['gender'] = $value=="পুরুষ"?"Male":"Female";
                    } else if ($key == 'education') {
                        $r['educationInfo'][] = [
                            'education_id'=>$this->parseEducation($value)
                        ];
                    } else if ($key == 'training') {
                        if (preg_match('/ভিডিপি/',$value)) {
                            $r['training_info'][] = [
                                'training_id' => 3,
                                'sub_training_id' => 0,
                            ];
                        } else if (preg_match('/আনসার/',$value)) {
                            $r['training_info'][] = [
                                'training_id' => 7,
                                'sub_training_id' => 0,
                            ];
                        }
                    }else if ($key == 'union_word_id') {
                        $uwi = intval(LanguageConverterFacades::bngToEng($value));
                        $r["union_word_id"] = $uwi;
                    }
                }
                $insertData[] = $r;
//                return $r?;

            }
            $res = [
                "success"=>0,
                "fail"=>[]
            ];
            Log::info($insertData);
//            return $insertData?"sssss":"dddddd";
            foreach ($insertData as $i){
                if($i['smart_card_id']&&strlen($i['smart_card_id'])>5) $i['smart_card_id'] = substr($i['smart_card_id'],-5);
                $request->replace($i);
                $valid = Validator::make($i,[
                    'ansar_name_bng'=>'required',
                    'ansar_name_eng'=>'required',
                    'father_name_bng'=>'required',
                    'mother_name_bng'=>'required',
                    'designation'=>'required',
                    'date_of_birth'=>'required',
                    'marital_status'=>'required',
                    'national_id_no'=>'unique:avurp.avurp_vdp_ansar_info',
                    'mobile_no_self'=>'unique:avurp.avurp_vdp_ansar_info,mobile_no_self',
                    'height_feet'=>'',
                    'height_inch'=>'',
                    'blood_group_id'=>'',
                    'gender'=>'required',
                    'health_condition'=>'',
                    'division_id'=>'required|numeric|min:1',
                    'unit_id'=>'required|numeric|min:1',
                    'thana_id'=>'required|numeric|min:1',
                    'union_id'=>'required|numeric|min:1',
                    'union_word_id'=>'required|numeric|min:1',
                    'smart_card_id'=>'sometimes|exists:hrm.tbl_ansar_parsonal_info,ansar_id|unique:avurp.avurp_vdp_ansar_info',
                    'post_office_name'=>'required',
                    'village_house_no'=>'required',
                    //'educationInfo'=>'required',
                    //'training_info'=>'required',
                    /*'educationInfo.*.education_id'=>'required|numeric|min:1',
                    'educationInfo.*.institute_name'=>'required',*/
                    //'training_info.*.training_id'=>'required|numeric|min:1',
                    //'training_info.*.sub_training_id'=>'required|numeric|min:1',

                ]);
                if($valid->fails()){
                    $res["fail"][] = ['status'=>false,'message'=>'Invalid data format','data'=>$i];
                }
                else {
                    $response = $this->infoRepository->create($request,auth()->user()->id);
                    if($response['status']) $res["success"]++;
                    else $res["fail"][] = ['status'=>false,'message'=>$response['data']['message'],'data'=>$i];
                }
//                $res[] = $i;
            }

            return $res;
        }
        return response()->json(['status' => false, 'msg' => 'Please upload excel u want to import']);
    }
    private function parseDate($date){
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
        foreach($formats as $format){
            try{
                $d = Carbon::createFromFormat($format,$value)->format('Y-m-d');
                $validDate = $d;
                break;
            }catch(\Exception $e){

            }
        }
        return $validDate?$validDate:null;
    }
    private function parseEducation($value){
        if(preg_match('/অস্তম|অষ্টম|৮ম|8/',$value)){
            $edu = AllEducationName::where('education_deg_bng', 'LIKE', "%অষ্টম%")->first();
            return $edu?$edu->id:0;
        }
        else if(preg_match('/নবম|৯ম|9/',$value)){
            $edu = AllEducationName::where('education_deg_bng', 'LIKE', "%নবম%")->first();
            return $edu?$edu->id:0;
        }
        else if(preg_match('/সপ্তম|৭ম|7/',$value)){
            $edu = AllEducationName::where('education_deg_bng', 'LIKE', "%সপ্তম%")->first();
            return $edu?$edu->id:0;
        }
        else if(preg_match('/ষষ্ঠ|৬ষ্ঠ|৬ম|6/',$value)){
            $edu = AllEducationName::where('education_deg_bng', 'LIKE', "%ষষ্ঠ%")->first();
            return $edu?$edu->id:0;
        }
        else if(preg_match('/পঞ্চম|৫ম|5/',$value)){
            $edu = AllEducationName::where('education_deg_bng', 'LIKE', "%পঞ্চম%")->first();
            return $edu?$edu->id:0;
        }
        else if(preg_match('/দশম|১০ম|10/',$value)){
            $edu = AllEducationName::where('education_deg_bng', 'LIKE', "%দশম%")->first();
            return $edu?$edu->id:0;
        }
        else if(preg_match('/এস.এস.সি|এস,এস,সি|এস\s+এস\s+সি/',$value)){
            $edu = AllEducationName::where('education_deg_bng', 'LIKE', "%এস.এস.সি%")->first();
            return $edu?$edu->id:0;
        }
        else if(preg_match('/এইচ.এস.সি|এইচ,এস,সি|এইচ\s+এস\s+সি/',$value)){
            $edu = AllEducationName::where('education_deg_bng', 'LIKE', "%এইচ.এস.সি%")->first();
            return $edu?$edu->id:0;
        }
        else if(preg_match('/বি.এ/',$value)){
            $edu = AllEducationName::where('education_deg_bng', 'LIKE', "%স্নাতক%")->first();
            return $edu?$edu->id:0;
        }
        $edu = AllEducationName::where('education_deg_bng', 'LIKE', "%$value%")->first();
        return $edu?$edu->id:0;
    }
}
