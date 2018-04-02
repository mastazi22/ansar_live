<?php

namespace App\modules\AVURP\Controllers;

use App\modules\AVURP\Models\VDPAnsarInfo;
use App\modules\HRM\Models\District;
use App\modules\HRM\Models\Division;
use App\modules\HRM\Models\Thana;
use App\modules\HRM\Models\Unions;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Session;
use Intervention\Image\Facades\Image;

class AnsarVDPInfoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if($request->ajax()){
            $limit = $request->limit?$request->limit:30;
            $range = $request->range?$request->range:'all';
            $unit = $request->unit?$request->unit:'all';
            $thana = $request->thana?$request->thana:'all';
            $vdp_infos = VDPAnsarInfo::with(['division','unit','thana','union']);
            if($range!='all'){
                $vdp_infos->where('division_id',$range);
            }
            if($unit!='all'){
                $vdp_infos->where('unit_id',$unit);
            }
            if($thana!='all'){
                $vdp_infos->where('thana_id',$thana);
            }
            $vdp_infos = $vdp_infos->paginate($limit);
            return view('AVURP::ansar_vdp_info.data',compact('vdp_infos'));
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
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
//        return $request->file('profile_pic');
        $rules = [
            'ansar_name_bng'=>'required',
            'ansar_name_eng'=>'required',
            'father_name_bng'=>'required',
            'mother_name_bng'=>'required',
            'designation'=>'required',
            'date_of_birth'=>'required',
            'marital_status'=>'required',
            'national_id_no'=>'required',
            'mobile_no_self'=>'required|unique:avurp.avurp_vdp_ansar_info',
            'height_feet'=>'required',
            'height_inch'=>'required',
            'blood_group_id'=>'required',
            'gender'=>'required',
            'health_condition'=>'required',
            'division_id'=>'required',
            'unit_id'=>'required',
            'thana_id'=>'required',
            'union_id'=>'required',
            'union_word_id'=>'required',
            'post_office_name'=>'required',
            'village_house_no'=>'required',
            'educationInfo'=>'required',
            'training_info'=>'required',
            'educationInfo.*.education_id'=>'required',
            'educationInfo.*.institute_name'=>'required',
        ];
        $this->validate($request,$rules,[
            'educationInfo.*.education_id.required'=>'This field required',
            'educationInfo.*.institute_name.required'=>'This field required'
            ]);
        DB::connection('avurp')->beginTransaction();
        try{

            $division_code = Division::find($request->division_id)->division_code;
            $unit_code = District::find($request->unit_id)->unit_code;
            $thana_code = Thana::find($request->thana_id)->thana_code;
            $union_code = Unions::find($request->union_id)->code;
            $gender_code = $request->gender=='Male'?1:2;
            $word_code = '0'.$request->union_word_id;
            $count = VDPAnsarInfo::where($request->only(['division_id','thana_id','unit_id','union_id','union_word_id']))->count()+1;
            $count = $count<10?'0'.$count:$count;
            $geo_id = $division_code.$unit_code.$thana_code.$union_code.$gender_code.$word_code.$count;
            if($request->hasFile('profile_pic')){
                $file = $request->file('profile_pic');
                $path = storage_path('avurp/profile_pic');
                if(!File::exists($path)) File::makeDirectory($path,777,true);
                $image_name = $geo_id.'.'.$file->clientExtension();
                Image::make($file)->save($path.'/'.$image_name);
            }
            $data = $request->except('educationInfo');
            $data['geo_id'] = $geo_id;
            if(isset($path)&&isset($image_name)) $data['profile_pic'] = $path.'/'.$image_name;
            else $data['profile_pic']='';
            $info = VDPAnsarInfo::create($data);
            foreach ($request->educationInfo as $education){
                $info->education()->create($education);
            }
            DB::connection('avurp')->commit();
        }catch(\Exception $e){
            DB::connection('avurp')->rollback();
            if(isset($path)&&isset($image_name)){
                if(File::exists($path.'/'.$image_name)){
                    File::delete($path.'/'.$image_name);
                }
            }
            return response()->json(['message'=>$e->getMessage()],500);
        }
        Session::flash('success_message','New entry added successfully');
        return response()->json(["message"=>"success"]);
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
}
