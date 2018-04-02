<?php

namespace App\modules\HRM\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\modules\HRM\Models\AllDisease;
use App\modules\HRM\Models\AllSkill;
use App\modules\HRM\Models\CustomQuery;
use App\modules\HRM\Models\District;
use App\modules\HRM\Models\Division;
use App\modules\HRM\Models\PersonalInfo;
use App\modules\HRM\Models\Thana;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;
use Intervention\Image\Facades\Image;

class GeneralSettingsController extends Controller
{
    public function unitIndex()
    {
        $divisions = DB::table('tbl_division')->select('tbl_division.id', 'tbl_division.division_name_eng')->get();
        return view('HRM::GeneralSettings.unit_entry')->with('divisions', $divisions);
    }

    public function unitView()
    {
        return view('HRM::GeneralSettings.unit_view');
    }

    public function unitViewDetails()
    {
        $view = Input::get('view');
        $limit = Input::get('limit');
        $offset = Input::get('offset');
        $division = Input::get('division');

        $rules = [
            'view'=>'regex:/[a-z]+/',
            'limit'=>'numeric',
            'offset'=>'numeric',
            'division'=>['regex:/^(all)$|^[0-9]+$/'],
        ];
        $valid = Validator::make(Input::all(),$rules);

        if($valid->fails()){
            //return print_r($valid->messages());
            return response("Invalid Request(400)",400);
        }

        if (strcasecmp($view, 'view') == 0) {
            return CustomQuery::unitInfo($offset, $limit, $division);
        } else {
            return CustomQuery::unitInfoCount($division);
        }
    }

    public function unitEntry(Request $request)
    {
        $rules = array(
            'division_id' => 'required|numeric|integer|min:0',
            'unit_name_eng' => 'required|regex:/^[a-zA-Z0-9_-]+$/',
            'unit_name_bng' => 'required',
            'unit_code' => 'required|numeric|integer',
        );
        $messages = array(
            'division_id.required' => 'Division  is required.',
            'division_id.numeric' => 'The format of Division is invalid.',
            'division_id.integer' => 'The format of Division is invalid.',
            'division_id.min' => 'The format of Division is invalid.',
            'unit_name_eng.required' => 'Unit Name in English is required.',
            'unit_name_eng.regex' => 'Unit Name in English must contain Alphabets, Numbers and Special Characters (- and _).',
            'unit_name_bng.required' => 'Unit Name in Bangla is required.',
            'unit_name_bng.regex' => 'The format of Unit Name in Bangla is invalid.',
            'unit_code.required' => 'Unit Code is required.',
            'unit_code.numeric' => 'Unit Code must be a number.',
            'unit_code.integer' => 'The format of Unit Code is invalid.',
        );
        $validation = Validator::make($request->all(), $rules, $messages);

        if ($validation->fails()) {
            return Redirect::back()->withInput(Input::all())->withErrors($validation);
        } else {
            DB::beginTransaction();
            try {
                $unit_info = new District();
                $unit_info->division_id = $request->input('division_id');
                $division_code = Division::find($request->input('division_id'));
                $unit_info->division_code = $division_code->division_code;
                $unit_info->unit_name_eng = $request->input('unit_name_eng');
                $unit_info->unit_name_bng = $request->input('unit_name_bng');
                $unit_info->unit_code = $request->input('unit_code');
                $unit_info->save();
                DB::commit();
                //Event::fire(new ActionUserEvent(['ansar_id' => $kpi_general->id, 'action_type' => 'ADD KPI', 'from_state' => '', 'to_state' => '', 'action_by' => auth()->user()->id]));
            } catch
            (Exception $e) {
                DB::rollback();
                return Redirect::route('unit_view')->with('error_message', $e->getMessage());
            }
            return Redirect::route('unit_view')->with('success_message', 'New Unit Entered Successfully!');
        }
    }

    public function thanaIndex()
    {
        return view('HRM::GeneralSettings.thana_entry');
    }

    public function thanaView()
    {
//        $thana_infos = DB::table('tbl_thana')
//            ->join('tbl_units', 'tbl_units.id', '=', 'tbl_thana.unit_id')
//            ->join('tbl_division', 'tbl_division.id', '=', 'tbl_thana.division_id')
//            ->select('tbl_thana.id', 'tbl_division.division_name_eng', 'tbl_division.division_code', 'tbl_units.unit_name_eng', 'tbl_units.unit_code', 'tbl_thana.thana_name_eng', 'tbl_thana.thana_name_bng', 'tbl_thana.thana_code')->paginate(10);
        //return $thana_infos;
        //$thana_infos=Thana::paginate(10);
        return view('HRM::GeneralSettings.thana_view');
    }

    public function thanaViewDetails()
    {
        $limit = Input::get('limit');
        $offset = Input::get('offset');
        $division = Input::get('division');
        $unit = Input::get('unit');
        $view = Input::get('view');

        $rules = [
            'view'=>'regex:/[a-z]+/',
            'limit'=>'numeric',
            'offset'=>'numeric',
            'unit'=>['regex:/^(all)$|^[0-9]+$/'],
            'division'=>['regex:/^(all)$|^[0-9]+$/'],
        ];
        $valid = Validator::make(Input::all(),$rules);

        if($valid->fails()){
            //return print_r($valid->messages());
            return response("Invalid Request(400)",400);
        }

        if (strcasecmp($view, 'view') == 0) {
            return CustomQuery::thanaInfo($offset, $limit, $division, $unit);
        } else {
            return CustomQuery::thanaInfoCount($division, $unit);
        }
    }

    public function thanaEntry(Request $request)
    {
        $rules = array(
            'division_name_eng' => 'required|numeric|integer|min:0',
            'unit_name_eng' => 'required|numeric|integer|min:0',
            'thana_name_eng' => 'required|regex:/^[\sa-zA-Z0-9_-]+$/',
            'thana_name_bng' => 'required',
            'thana_code' => 'required|numeric|integer',
        );
        $messages = array(
            'division_name_eng.required' => 'Division  is required.',
            'division_name_eng.numeric' => 'The format of Division is invalid.',
            'division_name_eng.integer' => 'The format of Division is invalid.',
            'division_name_eng.min' => 'The format of Division is invalid.',
            'unit_name_eng.required' => 'Unit is required.',
            'unit_name_eng.numeric' => 'The format of Unit is invalid.',
            'unit_name_eng.integer' => 'The format of Unit is invalid.',
            'unit_name_eng.min' => 'The format of Unit is invalid.',
            'thana_name_eng.required' => 'Thana Name in English is required.',
            'thana_name_eng.regex' => 'Thana Name in English must contain Alphabets, Numbers and Special Characters (- and _).',
            'thana_name_bng.required' => 'Thana Name in Bangla is required.',
            'thana_name_bng.regex' => 'The format of Thana Name in Bangla is invalid.',
            'thana_code.required' => 'Thana Code is required.',
            'thana_code.numeric' => 'Thana Code must be a number.',
            'thana_code.integer' => 'The format of Thana Code is invalid.',
        );
        $validation = Validator::make(Input::all(), $rules, $messages);

        if ($validation->fails()) {
            return Redirect::back()->withInput($request->all())->withErrors($validation);
        } else {
            DB::beginTransaction();
            try {
                $thana_info = new Thana();
                $thana_info->division_id = $request->input('division_name_eng');
                $division_id = Division::find($request->input('division_name_eng'));
                $thana_info->division_id = $division_id->id;
                $unit_id = District::find($request->input('unit_name_eng'));
                $thana_info->unit_id = $unit_id->id;
                $thana_info->unit_code = $unit_id->unit_code;
                $thana_info->thana_name_eng = $request->input('thana_name_eng');
                $thana_info->thana_name_bng = $request->input('thana_name_bng');
                $thana_info->thana_code = $request->input('thana_code');
                $thana_info->save();
                DB::commit();
                //Event::fire(new ActionUserEvent(['ansar_id' => $kpi_general->id, 'action_type' => 'ADD KPI', 'from_state' => '', 'to_state' => '', 'action_by' => auth()->user()->id]));
            } catch
            (Exception $e) {
                DB::rollback();
                return Redirect::route('thana_view')->with('error_message', $e->getMessage());
            }
            return Redirect::route('thana_view')->with('success_message', 'New Thana Entered Successfully!');
        }
    }

    public function unitEdit($id)
    {
        $unit_info = District::find($id);
        $division = Division::where('id','!=',0)->pluck('division_name_bng','id');
        $division->prepend("--Select a division--", '');
        return view('HRM::GeneralSettings.unit_edit')->with(['unit_info' => $unit_info, 'division' => $division]);
    }

    public function thanaEdit($id)
    {
        $thana_info = Thana::with(['division','district'])->find($id);
//        return $thana_info;
        return view('HRM::GeneralSettings.thana_edit')->with(['thana_info' => $thana_info]);
    }

    public function updateUnit(Request $request)
    {
        $id = $request->input('id');
        $rules = array(
            'id' => 'required|numeric|min:0|integer',
            'division_id' => 'required|numeric|min:0|integer',
            'unit_name_eng' => 'required|regex:/^[a-zA-Z0-9_-]+$/',
            'unit_name_bng' => 'required',
            'unit_code' => 'required|numeric|integer',
        );
        $messages = array(
            'division_id.min' => 'The format of Division is invalid.',
            'unit_name_eng.required' => 'Unit Name in English is required.',
            'unit_name_eng.regex' => 'Unit Name in English must contain Alphabets, Numbers and Special Characters (- and _).',
            'unit_name_bng.required' => 'Unit Name in Bangla is required.',
            'unit_name_bng.regex' => 'The format of Unit Name in Bangla is invalid.',
            'unit_code.required' => 'Unit Code is required.',
            'unit_code.numeric' => 'Unit Code must be a number.',
            'unit_code.integer' => 'The format of Unit Code is invalid.',
        );
        $validation = Validator::make($request->all(), $rules, $messages);

        if ($validation->fails()) {
            return Redirect::back()->withInput(Input::all())->withErrors($validation);
        } else {
            DB::beginTransaction();
            try {
                District::find($id)->update($request->all());
                DB::statement("call update_info(:did,:uid)",['did'=>$request->division_id,'uid'=>$request->id]);
                DB::commit();
                //Event::fire(new ActionUserEvent(['ansar_id' => $kpi_general->id, 'action_type' => 'ADD KPI', 'from_state' => '', 'to_state' => '', 'action_by' => auth()->user()->id]));
            } catch
            (Exception $e) {
                DB::rollback();
                return Redirect::route('unit_view')->with('error_message', $e->getMessage());
            }

            return Redirect::route('unit_view')->with('success_message', 'Unit Updated Successfully!');
        }
    }

    public function updateThana(Request $request)
    {
        $id = $request->input('id');
        $rules = array(
            'id' => 'required|numeric|integer|min:0',
            'division_id' => 'required|numeric|min:0|integer',
            'unit_id' => 'required|numeric|min:0|integer',
            'thana_name_eng' => 'required|regex:/^[\sa-zA-Z0-9_-]+$/',
            'thana_name_bng' => 'required',
            'thana_code' => 'required|numeric|integer',
        );
        $messages = array(
            'thana_name_eng.required' => 'Thana Name in English is required.',
            'thana_name_eng.regex' => 'Thana Name in English must contain Alphabets, Numbers and Special Characters (- and _).',
            'thana_name_bng.required' => 'Thana Name in Bangla is required.',
            'thana_name_bng.regex' => 'The format of Thana Name in Bangla is invalid.',
            'thana_code.required' => 'Thana Code is required.',
            'thana_code.numeric' => 'Thana Code must be a number.',
            'thana_code.integer' => 'The format of Thana Code is invalid.',
        );
        $validation = Validator::make(Input::all(), $rules, $messages);

        if ($validation->fails()) {
            return Redirect::back()->withInput(Input::all())->withErrors($validation);
        } else {
            DB::beginTransaction();
            try {
                $thana_info = Thana::find($id)->update($request->except(['action_user_id']));
                DB::statement("call update_thana(:did,:uid,:tid)",['did'=>$request->division_id,'uid'=>$request->unit_id,'tid'=>$request->id]);
                DB::commit();
                //Event::fire(new ActionUserEvent(['ansar_id' => $kpi_general->id, 'action_type' => 'ADD KPI', 'from_state' => '', 'to_state' => '', 'action_by' => auth()->user()->id]));
            } catch
            (\Exception $e) {
                DB::rollback();
                return Redirect::route('thana_view')->with('error_message', $e->getMessage());
            }
            return Redirect::route('thana_view')->with('success_message', 'Thana Updated Successfully!');
        }
    }

    public function unitDelete($id)
    {
        $unit_info = District::find($id);
        $unit_info->delete();
        return Redirect::route('unit_view')->with('success_message', 'Unit Deleted Successfully!');
    }

    public function thanaDelete($id)
    {
        $thana_info = Thana::find($id);
        $thana_info->delete();
        return Redirect::route('thana_view')->with('success_message', 'Thana Deleted Successfully!');
    }

    public function diseaseView()
    {

        $disease_infos = DB::table('tbl_long_term_disease')->where('id', '>', 0)->paginate(config('app.item_per_page'));
        return view('HRM::GeneralSettings.allDiseaseView')->with('disease_infos', $disease_infos);
    }

    public function addDiseaseName()
    {
        return view('HRM::GeneralSettings.addDisease');
    }

    public function diseaseEntry(Request $request)
    {
        $rules = array(
            'disease_name_eng' => 'required|unique:tbl_long_term_disease|regex:/^[a-zA-Z0-9 ]+$/',
            'disease_name_bng' => 'required|unique:tbl_long_term_disease',
        );
        $messages = array(
            'disease_name_eng.required' => 'Disease Name in English field is required.',
            'disease_name_eng.unique' => 'Disease Name in English has already taken.',
            'disease_name_eng.regex' => 'Disease Name in English must contain Alphabets, Numbers and Space Characters.',
            'disease_name_eng.required' => 'Disease Name in Bangla field is required.',
        );
        $validation = Validator::make(Input::all(), $rules, $messages);

        if ($validation->fails()) {
            return Redirect::route('add_disease_view')->withInput(Input::all())->withErrors($validation);
        } else {

            DB::beginTransaction();
            try {
                $disease_info = new AllDisease();
                $disease_info->disease_name_eng = $request->input('disease_name_eng');
                $disease_info->disease_name_bng = $request->input('disease_name_bng');
                $disease_info->save();
                DB::commit();
                //Event::fire(new ActionUserEvent(['ansar_id' => $kpi_general->id, 'action_type' => 'ADD KPI', 'from_state' => '', 'to_state' => '', 'action_by' => auth()->user()->id]));
            } catch
            (Exception $e) {
                DB::rollback();
                return Redirect::route('disease_view')->with('error_message', $e->getMessage());
            }
            return Redirect::route('disease_view')->with('success_message', 'New Disease Added Successfully!');
        }
    }

    public function diseaseEdit($id)
    {
        $unit_infos = AllDisease::find($id);
        return view('HRM::GeneralSettings.diseaseEdit')->with(['disease_infos' => $unit_infos, 'id' => $id]);
    }

    public function updateDisease(Request $request)
    {
        $id = $request->input('id');
        if(!preg_match('/^[0-9]+$/',$id))
        {
            return Redirect::route('disease_view')->with('error_message', 'Invalid Request');
        }
        $rules = array(
            'disease_name_eng' => 'required|regex:/^[a-zA-Z0-9 ]+$/|unique:tbl_long_term_disease,disease_name_eng,' . $id,
            'disease_name_bng' => 'required|unique:tbl_long_term_disease,disease_name_bng,' . $id,
        );
        $messages = array(
            'disease_name_eng.required' => 'Disease Name in English field is required.',
            'disease_name_eng.unique' => 'Disease Name in English has already taken.',
            'disease_name_eng.regex' => 'Disease Name in English must contain Alphabets, Numbers and Space Characters.',
            'disease_name_eng.required' => 'Disease Name in Bangla field is required.',
        );
        $validation = Validator::make(Input::all(), $rules, $messages);

        if ($validation->fails()) {
            return Redirect::route('disease_edit')->withInput(Input::all())->withErrors($validation);
        } else {
            DB::beginTransaction();
            try {
                $disease_info = AllDisease::find($id);
                $disease_info->disease_name_eng = $request->input('disease_name_eng');
                $disease_info->disease_name_bng = $request->input('disease_name_bng');
                $disease_info->save();
                DB::commit();
                //Event::fire(new ActionUserEvent(['ansar_id' => $kpi_general->id, 'action_type' => 'ADD KPI', 'from_state' => '', 'to_state' => '', 'action_by' => auth()->user()->id]));
            } catch
            (Exception $e) {
                DB::rollback();
                return Redirect::route('disease_view')->with('error_message', $e->getMessage());
            }
            return Redirect::route('disease_view')->with('success_message', 'Disease Updated Successfully!');
        }
    }


    public function skillView()
    {

        $skill_infos = DB::table('tbl_particular_skill')->where('id', '>', 1)->paginate(config('app.item_per_page'));
        return view('HRM::GeneralSettings.allSkillView')->with('skill_infos', $skill_infos);
    }

    public function addSkillName()
    {
        return view('HRM::GeneralSettings.addSkill');
    }

    public function skillEntry(Request $request)
    {
        $rules = array(
            'skill_name_eng' => 'required|unique:tbl_particular_skill|regex:/^[a-zA-Z0-9 ]+$/',
            'skill_name_bng' => 'required|unique:tbl_particular_skill',
        );
        $messages = array(
            'skill_name_eng.required' => 'Skill Name in English field is required.',
            'skill_name_eng.unique' => 'Skill Name in English has already taken.',
            'skill_name_eng.regex' => 'Skill Name in English must contain Alphabets, Numbers and Space Characters.',
            'skill_name_bng.required' => 'Skill Name in Bangla field is required.',
        );
        $validation = Validator::make(Input::all(), $rules, $messages);

        if ($validation->fails()) {
            return Redirect::route('add_skill_view')->withInput(Input::all())->withErrors($validation);
        } else {
            DB::beginTransaction();
            try {
                $skill_info = new AllSkill();
                $skill_info->skill_name_eng = $request->input('skill_name_eng');
                $skill_info->skill_name_bng = $request->input('skill_name_bng');
                $skill_info->save();
                DB::commit();
            } catch
            (Exception $e) {
                DB::rollback();
                return Redirect::route('skill_view')->with('error_message', $e->getMessage());
            }
            return Redirect::route('skill_view')->with('success_message', 'New Skill Added Successfully!');
        }
    }

    public function skillEdit($id)
    {
        $unit_infos = AllSkill::find($id);
        return view('HRM::GeneralSettings.skillEdit')->with(['skill_infos' => $unit_infos, 'id' => $id]);
    }

    public function updateSkill(Request $request)
    {
        $id = $request->input('id');
        if(!preg_match('/^[0-9]+$/',$id))
        {
            return Redirect::route('skill_view')->with('error_message', 'Invalid Request');
        }
        $rules = array(
            'skill_name_eng' => 'required|regex:/^[a-zA-Z0-9 ]+$/|unique:tbl_particular_skill,skill_name_eng,' . $id,
            'skill_name_bng' => 'required|unique:tbl_particular_skill,skill_name_bng,' . $id,
        );
        $messages = array(
            'skill_name_eng.required' => 'Skill Name in English field is required.',
            'skill_name_eng.unique' => 'Skill Name in English has already taken.',
            'skill_name_eng.regex' => 'Skill Name in English must contain Alphabets, Numbers and Space Characters.',
            'skill_name_bng.required' => 'Skill Name in Bangla field is required.',
        );
        $validation = Validator::make(Input::all(), $rules, $messages);

        if ($validation->fails()) {
            return Redirect::back()->withInput(Input::all())->withErrors($validation);
        } else {

            DB::beginTransaction();
            try {
                $skill_info = AllSkill::find($id);
                $skill_info->skill_name_eng = $request->input('skill_name_eng');
                $skill_info->skill_name_bng = $request->input('skill_name_bng');
                $skill_info->save();
                DB::commit();
            } catch
            (Exception $e) {
                DB::rollback();
                return Redirect::route('skill_view')->with('error_message', $e->getMessage());
            }
            return Redirect::route('skill_view')->with('success_message', 'Skill Updated Successfully!');
        }
    }

    public function uploadOriginalInfo(Request $request){

        $rules = [
            'ansar_id'=>'required',
            'front_side'=>'required|mimes:jpeg,jpg',
            'back_side'=>'required|mimes:jpeg,jpg',
        ];
        $valid = Validator::make($request->all(),$rules);
        if($valid->fails()){
            return $valid->messages()->toJson();
        }
        $fontPath = storage_path('data/orginalinfo/frontside');
        $backPath = storage_path('data/orginalinfo/backside');

        try{
            $p = PersonalInfo::where('ansar_id',$request->ansar_id);
            if(!$p->exists()){
                throw new \Exception("Ansar ID not found");
            }
            if(File::exists($fontPath.'/'.$request->ansar_id.'.jpg')){
                File::delete($fontPath.'/'.$request->ansar_id.'.jpg');
            }
            if(File::exists($backPath.'/'.$request->ansar_id.'.jpg')){
                File::delete($backPath.'/'.$request->ansar_id.'.jpg');
            }
            $fImage = Image::make($request->file('front_side'));
            $bImage = Image::make($request->file('back_side'));
            $fWidth = ($fImage->width()*75)/100;
            $bWidth = ($bImage->width()*75)/100;
            $fImage->resize($fWidth,null,function($constraint){
                $constraint->aspectRatio();
            })->save($fontPath.'/'.$request->ansar_id.'.jpg');
            $bImage->resize($bWidth,null,function($constraint){
                $constraint->aspectRatio();
            })->save($backPath.'/'.$request->ansar_id.'.jpg');
            return Response::json(['status'=>true,'message'=>"Original Image Upload Successfully"]);
        }catch (\Exception $e){
            return Response::json(['status'=>false,'message'=>$e->getMessage()]);
        }

    }

    public function uploadOriginalInfoView(){
        return View::make("HRM::GeneralSettings.upload_original_info");
    }
}
