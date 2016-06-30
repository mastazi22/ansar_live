<?php

namespace App\modules\HRM\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\modules\HRM\Models\AllDisease;
use App\modules\HRM\Models\AllSkill;
use App\modules\HRM\Models\CustomQuery;
use App\modules\HRM\Models\District;
use App\modules\HRM\Models\Division;
use App\modules\HRM\Models\Thana;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;

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
        $limit = Input::get('limit');
        $offset = Input::get('offset');
        $division = Input::get('division');
        $view = Input::get('view');
        if (strcasecmp($view, 'view') == 0) {
            return CustomQuery::unitInfo($offset, $limit, $division);
        } else {
            return CustomQuery::unitInfoCount($division);
        }
    }

    public function unitEntry(Request $request)
    {

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
            return $e->getMessage();
        }
        return Redirect::route('unit_view')->with('success_message', 'New Unit Entered Successfully!');
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
        $division=Input::get('division');
        $unit=Input::get('unit');
        $view = Input::get('view');
        if (strcasecmp($view, 'view') == 0) {
            return CustomQuery::thanaInfo($offset, $limit, $division, $unit);
        } else {
            return CustomQuery::thanaInfoCount($division, $unit);
        }
    }

    public function thanaEntry(Request $request)
    {

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
            return $e->getMessage();
        }
        return Redirect::route('thana_view')->with('success_message', 'New Thana Entered Successfully!');
    }

    public function unitEdit($id)
    {
        $unit_info = District::find($id);
        $division_id = $unit_info->division_id;
        $division = DB::table('tbl_division')->where('id', $division_id)->select('tbl_division.division_name_eng')->first();
        return view('HRM::GeneralSettings.unit_edit')->with(['unit_info' => $unit_info, 'division' => $division]);
    }

    public function thanaEdit($id)
    {
        $thana_info = Thana::find($id);
        $division_id = $thana_info->division_id;
        $unit_id = $thana_info->unit_id;
        $division = DB::table('tbl_division')->where('id', $division_id)->select('tbl_division.division_name_eng')->first();
        $unit = DB::table('tbl_units')->where('id', $unit_id)->select('tbl_units.unit_name_eng')->first();
        return view('HRM::GeneralSettings.thana_edit')->with(['thana_info' => $thana_info, 'division' => $division, 'unit' => $unit]);
    }

    public function updateUnit(Request $request)
    {
        $id = $request->input('id');
        DB::beginTransaction();
        try {
            $unit_info = District::find($id);
            $unit_info->unit_name_eng = $request->input('unit_name_eng');
            $unit_info->unit_name_bng = $request->input('unit_name_bng');
            $unit_info->unit_code = $request->input('unit_code');
            $unit_info->save();
            DB::commit();
            //Event::fire(new ActionUserEvent(['ansar_id' => $kpi_general->id, 'action_type' => 'ADD KPI', 'from_state' => '', 'to_state' => '', 'action_by' => auth()->user()->id]));
        } catch
        (Exception $e) {
            DB::rollback();
            return $e->getMessage();
        }

        return Redirect::route('unit_view')->with('success_message', 'Unit Updated Successfully!');

    }

    public function updateThana(Request $request)
    {
        $id = $request->input('id');
        DB::beginTransaction();
        try {
            $thana_info = Thana::find($id);
            $thana_info->thana_name_eng = $request->input('thana_name_eng');
            $thana_info->thana_name_bng = $request->input('thana_name_bng');
            $thana_info->thana_code = $request->input('thana_code');
            $thana_info->save();
            DB::commit();
            //Event::fire(new ActionUserEvent(['ansar_id' => $kpi_general->id, 'action_type' => 'ADD KPI', 'from_state' => '', 'to_state' => '', 'action_by' => auth()->user()->id]));
        } catch
        (Exception $e) {
            DB::rollback();
            return $e->getMessage();
        }
        return Redirect::route('thana_view')->with('success_message', 'Thana Updated Successfully!');
    }
    public function unitDelete($id){
        $unit_info=District::find($id);
        $unit_info->delete();
        return Redirect::route('unit_view')->with('success_message', 'Unit Deleted Successfully!');
    }
    public function thanaDelete($id){
        $thana_info=Thana::find($id);
        $thana_info->delete();
        return Redirect::route('thana_view')->with('success_message', 'Thana Deleted Successfully!');
    }
    public function diseaseView()
    {

        $disease_infos = DB::table('tbl_long_term_disease')->where('id', '>', 1)->paginate(10);
        return view('HRM::GeneralSettings.allDiseaseView')->with('disease_infos', $disease_infos);
    }

    public function addDiseaseName()
    {
        return view('HRM::GeneralSettings.addDisease');
    }

    public function diseaseEntry(Request $request)
    {
        $rules = array(
            'disease_name_eng' => 'required|unique:tbl_long_term_disease',
            'disease_name_bng' => 'required|unique:tbl_long_term_disease',
        );
        $messages = array(
            'required' => 'This field is required',
        );
        $validation = Validator::make(Input::all(), $rules, $messages);

        if ($validation->fails()) {
            return Redirect::route('add_disease_view')->withInput(Input::all())->withErrors($validation);
        } else {
            $disease_info = new AllDisease();
            $disease_info->disease_name_eng = $request->input('disease_name_eng');
            $disease_info->disease_name_bng = $request->input('disease_name_bng');
            $disease_info->save();
            return Redirect::route('disease_view')->with('success_message', 'New Disease Added Successfully!');
        }
    }

    public function diseaseEdit($id)
    {
        $unit_infos = AllDisease::find($id);
        return view('HRM::GeneralSettings.diseaseEdit')->with(['disease_infos' => $unit_infos]);
    }

    public function updateDisease(Request $request)
    {
        $id = $request->input('id');
        $rules = array(
            'disease_name_eng' => 'required|unique:tbl_long_term_disease,disease_name_eng,' . $id,
            'disease_name_bng' => 'required|unique:tbl_long_term_disease,disease_name_bng,' . $id,
        );
        $messages = array(
            'required' => 'This field is required',
        );
        $validation = Validator::make(Input::all(), $rules, $messages);

        if ($validation->fails()) {
            return Redirect::route('disease_edit')->withInput(Input::all())->withErrors($validation);
        } else {
            $disease_info = AllDisease::find($id);
            $disease_info->disease_name_eng = $request->input('disease_name_eng');
            $disease_info->disease_name_bng = $request->input('disease_name_bng');
            $disease_info->save();
            return Redirect::route('disease_view')->with('success_message', 'Disease Updated Successfully!');
        }
    }


    public function skillView()
    {

        $skill_infos = DB::table('tbl_particular_skill')->where('id', '>', 1)->paginate(10);
        return view('HRM::GeneralSettings.allSkillView')->with('skill_infos', $skill_infos);
    }

    public function addSkillName()
    {
        return view('HRM::GeneralSettings.addSkill');
    }

    public function skillEntry(Request $request)
    {
        $rules = array(
            'skill_name_eng' => 'required|unique:tbl_particular_skill',
            'skill_name_bng' => 'required|unique:tbl_particular_skill',
        );
        $messages = array(
            'required' => 'This field is required',
        );
        $validation = Validator::make(Input::all(), $rules, $messages);

        if ($validation->fails()) {
            return Redirect::route('add_skill_view')->withInput(Input::all())->withErrors($validation);
        } else {
            $skill_info = new AllSkill();
            $skill_info->skill_name_eng = $request->input('skill_name_eng');
            $skill_info->skill_name_bng = $request->input('skill_name_bng');
            $skill_info->save();
            return Redirect::route('add_skill_view')->with('success_message', 'New Skill Added Successfully!');
        }
    }

    public function skillEdit($id)
    {
        $unit_infos = AllSkill::find($id);
        return view('HRM::GeneralSettings.skillEdit')->with(['skill_infos' => $unit_infos]);
    }

    public function updateSkill(Request $request)
    {
        $id = $request->input('id');
        $rules = array(
            'skill_name_eng' => 'required|unique:tbl_particular_skill,skill_name_eng,' . $id,
            'skill_name_bng' => 'required|unique:tbl_particular_skill,skill_name_bng,' . $id,
        );
        $messages = array(
            'required' => 'This field is required',
        );
        $validation = Validator::make(Input::all(), $rules, $messages);

        if ($validation->fails()) {
            return Redirect::back()->withInput(Input::all())->withErrors($validation);
        } else {
            $skill_info = AllSkill::find($id);
            $skill_info->skill_name_eng = $request->input('skill_name_eng');
            $skill_info->skill_name_bng = $request->input('skill_name_bng');
            $skill_info->save();
            return Redirect::route('skill_view')->with('success_message', 'Skill Updated Successfully!');
        }
    }
}
