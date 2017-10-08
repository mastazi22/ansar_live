<?php

namespace App\modules\HRM\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\modules\HRM\Models\AnsarStatusInfo;
use App\modules\HRM\Models\Division;
use App\modules\HRM\Models\Edication;
use App\modules\HRM\Models\Nominee;
use App\modules\HRM\Models\PersonalInfo;
use App\modules\HRM\Models\TrainingInfo;
use App\modules\HRM\Models\CustomQuery;
use App\modules\HRM\Models\District;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;
use Intervention\Image\Facades\Image;
use Mockery\Exception;

class DraftController extends Controller
{

//    Showing the entry draft values
    public function draftList()
    {
        return View::make('HRM::Entryform.entryDraft');
    }

//    Getting the entry draft values
    public function getDraftList()
    {
        $filename = "";

        $count = 0;
        $dir = storage_path() . '/drafts/';
        if (is_dir($dir)) {
            if ($dh = opendir($dir)) {
                while (($file = readdir($dh)) !== false) {
                    if (!in_array($file, array('.', '..')) && !File::isDirectory(storage_path('drafts') . '/' . $file)) {
                        $input = file_get_contents(storage_path('drafts') . '/' . $file);
                        $array[$count] = unserialize($input);
                        $array[$count]['filename'] = $file;
                        $count++;
                    }
                }
                closedir($dh);
            }
        }
        return Response::json($array);
    }

//    Deleting the draft file
    public function draftDelete($id)
    {
        $files = File::files(storage_path() . '/drafts/');
        //return $files;
        foreach($files as $file){
            //echo pathinfo($file,PATHINFO_BASENAME)."<br>";
            if(strcmp(pathinfo($file,PATHINFO_BASENAME),$id)==0){
                File::delete($file);
                return redirect()->back()->with('success',"{$id} remove from draft list successfully");
            }
        }
        return redirect()->back()->with('error',"{$id} not found in draft list");
//        if ($handle = opendir($dir)) {
//            while (($file = readdir($handle)) !== false) {
//                if (!in_array($file, array('.', '..')) && !is_dir($dir . $file))
//                    if ($id == $file) {
//                        unlink($dir . '/' . $file);
//                        return redirect()->back();
//                    }
//            }
//        }
    }

//    Showing single draft value
    public function singleDraftEdit($draftId)
    {
        return View::make('HRM::Entryform.singleDraftEdit')->with('data', $draftId);
    }

    public function entrySingleDraft($id)
    {
        $dir = storage_path() . '/drafts/';
        $input = file_get_contents($dir . '/' . $id);
        $array = unserialize($input);
        return Response::json($array);
    }

    public function editDraft($id, Request $request)
    {
        if ($request->ajax()) {
            $pd = explode(".", $id)[0];
            if (Input::get('action') == 'Update draft') {
                $time = time();
                $dir = storage_path() . '/drafts/';
                $inputall = serialize(Input::except(['profile_pic', 'sign_pic', 'thumb_pic']));
                $myfile = fopen(storage_path() . '/drafts/' . "$time.txt", "w") or die("Unable to open file!");
                $pd = explode(".", $id)[0];
                chmod(storage_path() . '/drafts/' . "$time.txt", 0777);
                fwrite($myfile, $inputall);
                fclose($myfile);
                if ($handle = opendir($dir)) {
                    while (($file = readdir($handle)) !== false) {
                        if (!in_array($file, array('.', '..')) && !is_dir($dir . $file))
                            if ($id == $file) {
                                unlink($dir . '/' . $file);
                            }
                    }
                }
                File::cleanDirectory($dir . 'photo/' . $pd);
                File::deleteDirectory($dir . 'photo/' . $pd);
                if ($request->file('profile_pic')) {
                    $profileextension = $request->file('profile_pic')->getClientOriginalExtension();
                    $path = storage_path('/drafts/photo/' . $time);
                    if (!File::exists($path)) File::makeDirectory($path, 0777, true);
                    if (File::exists($path . '/' . 'profile' . '.' . $profileextension)) {
                        File::delete($path . '/' . 'profile' . '.' . $profileextension);
                    }
                    Image::make($request->file('profile_pic'))->resize(240, 260)->save($path . '/' . 'profile' . '.' . 'jpg');
                }
                if ($request->file('sign_pic')) {

                    $signextension = $request->file('sign_pic')->getClientOriginalExtension();
                    $path = storage_path('/drafts/photo/' . $time);
                    //Log::info(File::exists($path. '/' . $ansarid . '.' . $signextension)?"true":"false");
                    if (!File::exists($path)) File::makeDirectory($path, 0777, true);
                    if (File::exists($path . '/' . 'sign' . '.' . $signextension)) {
                        File::delete($path . '/' . 'sign' . '.' . $signextension);
                    }
                    Image::make($request->file('sign_pic'))->resize(220, 90)->save($path . '/' . 'sign' . '.' . 'jpg');

                }
                if ($request->file('thumb_pic')) {
                    $thumbextension = $request->file('thumb_pic')->getClientOriginalExtension();
                    $path = storage_path('/drafts/photo/' . $time);
                    if (!File::exists($path)) File::makeDirectory($path, 0777, true);
                    if (File::exists($path . '/' . 'thumb' . '.' . $thumbextension)) {
                        File::delete($path . '/' . 'thumb' . '.' . $thumbextension);
                    }
                    Image::make($request->file('thumb_pic'))->resize(220, 90)->save($path . '/' . 'thumb' . '.' . 'jpg');

                }
                Session::put("update_draft","Draft updated complete");
                return Response::json(['status' => 'update', 'data' => $id]);
            }
            else {

                $rules = [
                    'ansar_name_eng' => 'required',
                    'ansar_name_bng' => 'required',
                    'recent_status' => 'required',
                    'father_name_eng' => 'required',
                    'father_name_bng' => 'required',
                    'mother_name_eng' => 'required',
                    'mother_name_bng' => 'required',
                    'data_of_birth' => 'required',
                    'marital_status' => 'required',
                    'national_id_no' => 'required|regex:/^[0-9]{10,17}$/',
                    'division_name_eng' => 'required',
                    'unit_name_eng' => 'required',
                    'thana_name_eng' => 'required',
                    'blood_group_name_bng' => 'required',
                    'hight_feet' => 'required',
                    'sex' => 'required',
                    'mobile_no_self' => 'required|min:11|unique:tbl_ansar_parsonal_info',

                ];

                $messages = [
                    'required' => 'This field is required',
                ];
                $validator = Validator:: make($request->all(), $rules, $messages);

                if ($validator->fails()) {
                    return Response::json(['error' => $validator->errors(), 'status' => false]);
                } else {
                    $personalinfo = new PersonalInfo();
                    $education = [];
                    $training = [];
                    $nominee = [];

//            get division and district values
                    $division = new Division();
                    $unit = new District();

                    $personalinfo->ansar_name_eng = $request->input('ansar_name_eng');
                    $personalinfo->ansar_name_bng = $request->input('ansar_name_bng');
                    $personalinfo->designation_id = $request->input('recent_status');
//            $personalinfo->recent_Status_bl=$request->input('recent_Status_bl');
                    $personalinfo->father_name_eng = $request->input('father_name_eng');
                    $personalinfo->father_name_bng = $request->input('father_name_bng');
                    $personalinfo->mother_name_eng = $request->input('mother_name_eng');
                    $personalinfo->mother_name_bng = $request->input('mother_name_bng');
                    $personalinfo->data_of_birth = Carbon::parse($request->input('data_of_birth'))->format('Y-m-d');
                    $personalinfo->marital_status = $request->input('marital_status');
//                $personalinfo->marital_status_bng = $request->input('marital_status_bng');
                    $personalinfo->spouse_name_eng = $request->input('spouse_name_eng');
                    $personalinfo->spouse_name_bng = $request->input('spouse_name_bng');
                    $personalinfo->national_id_no = $request->input('national_id_no');
                    $personalinfo->birth_certificate_no = $request->input('birth_certificate_no');

                    $personalinfo->disease_id = $request->input('long_term_disease');

                    if ($request->input('own_disease')) {
                        $personalinfo->own_disease = $request->input('own_disease');
                    }

                    $personalinfo->skill_id = $request->input('particular_skill');

                    if ($request->input('own_particular_skill')) {
                        $personalinfo->own_particular_skill = $request->input('own_particular_skill');
                    }

                    $personalinfo->criminal_case = $request->input('criminal_case');
                    $personalinfo->criminal_case_bng = $request->input('criminal_case_bng');
                    $personalinfo->certificate_no = $request->input('certificate_no');
                    $personalinfo->village_name = $request->input('village_name');
                    $personalinfo->village_name_bng = $request->input('village_name_bng');
                    $personalinfo->post_office_name = $request->input('post_office_name');
                    $personalinfo->post_office_name_bng = $request->input('post_office_name_bng');
                    $divisionid = $personalinfo->division_id = $request->input('division_name_eng');
                    $unitid = $personalinfo->unit_id = $request->input('unit_name_eng');
                    if (Auth::user()->type == 22) {
                        $divisionid = District::find($unitid)->division_id;
                    }
                    $thanaid = $personalinfo->thana_id = $request->input('thana_name_eng');
                    $personalinfo->union_name_eng = $request->input('union_name_eng');
                    $personalinfo->union_name_bng = $request->input('union_name_bng');
                    $personalinfo->hight_feet = $request->input('hight_feet');
//                $personalinfo->hight_feet_bng = $request->input('hight_feet_bng');
                    $personalinfo->hight_inch = $request->input('hight_inch');
//                $personalinfo->hight_inch_bng = $request->input('hight_inch_bng');
                    $personalinfo->blood_group_id = $request->input('blood_group_name_bng');

                    $personalinfo->eye_color = $request->input('eye_color');
                    $personalinfo->eye_color_bng = $request->input('eye_color_bng');
                    $personalinfo->skin_color = $request->input('skin_color');
                    $personalinfo->skin_color_bng = $request->input('skin_color_bng');
                    $personalinfo->sex = $request->input('sex');
//                $personalinfo->sex_bng = $request->input('sex_bng');
                    $personalinfo->identification_mark = $request->input('identification_mark');
                    $personalinfo->identification_mark_bng = $request->input('identification_mark_bng');
                    $personalinfo->land_phone_self = $request->input('land_phone_self');
//                $personalinfo->land_phone_self_bng = $request->input('land_phone_self_bng');
                    $personalinfo->land_phone_request = $request->input('land_phone_request');
//                $personalinfo->land_phone_request_bng = $request->input('land_phone_request_bng');
                    $mobile_no = $request->input('mobile_no_self');

                    if (!ctype_digit($mobile_no)) {
                        return Response::json(['numeric_value' => 'Need numeric', 'status' => 'numeric']);
                    } else {
                        $getFirstTowDigit = substr($mobile_no, 0, 2);
                        if ($getFirstTowDigit == 88) {
                            return Response::json(['numeric_value' => 'Need numeric', 'status' => 'eight']);
//                           $number = $personalinfo->mobile_no_self =  substr($mobile_no,2);
                        } else
                            $number = $personalinfo->mobile_no_self = $mobile_no;
                    }


//                $personalinfo->mobile_no_self_bng = $request->input('mobile_no_self_bng');
                    $personalinfo->mobile_no_request = $request->input('mobile_no_request');
//                $personalinfo->mobile_no_request_bng = $request->input('mobile_no_request_bng');
                    $personalinfo->email_self = $request->input('email_self');
                    $personalinfo->email_request = $request->input('email_request');

                    $personalinfo->user_id = Auth::user()->id;
//
//                list($ppwidth, $ppheight, $type, $attr) = getimagesize($request->file('profile_pic'));
//                list($signwidth, $signheight, $type, $attr) = getimagesize($request->file('sign_pic'));
//                list($thumbwidth, $thumbheight, $type, $attr) = getimagesize($request->file('thumb_pic'));
//                if ($ppwidth > 220 || $ppheight > 200)
//                    return redirect('entryform')->with('status', 'Profile picture must be less than width 220px and height 200px')->withInput();
//
//                if ($signwidth > 120 || $signheight > 50)
//                    return redirect('entryform')->with('signstatus', 'Sign image must be less than width 120px and height 50px')->withInput();
//
//                if ($thumbwidth > 120 || $thumbheight > 50)
//                    return redirect('entryform')->with('thumbstatus', 'Thumb image must be less than width 120px and height 50px')->withInput();
                    //


                    $name_of_degree = $request->input('educationIdBng');
                    $institute_name = $request->input('institute_name');
                    $passing_year = $request->input('passing_year');
                    $gade_divission = $request->input('gade_divission');
                    $institute_name_eng = $request->input('institute_name_eng');
                    $passing_year_eng = $request->input('passing_year_eng');
                    $gade_divission_eng = $request->input('gade_divission_eng');
                    $edulength = count($name_of_degree);


//
                    $training_designation = $request->input('training_designation');
                    $training_institute_name = $request->input('institute');
                    $training_start_date = $request->input('training_start');
                    $training_end_date = $request->input('training_end');
                    $trining_certificate_no = $request->input('training_sanad');
                    $training_designation_eng = $request->input('training_designation_eng');
                    $training_institute_name_eng = $request->input('institute_eng');
                    $training_start_date_eng = $request->input('training_start_eng');
                    $training_end_date_eng = $request->input('training_end_eng');
                    $trining_certificate_no_eng = $request->input('training_sanad_eng');
                    $length = count($training_institute_name);


                    $name_of_nominee = $request->input('nominee_name');
                    $relation_with_nominee = $request->input('relation');
                    $nominee_parcentage = $request->input('percentage');
                    $nominee_contact_no = $request->input('nominee_mobile');
                    $name_of_nominee_eng = $request->input('nominee_name_eng');
                    $relation_with_nominee_eng = $request->input('relation_eng');
                    $nominee_parcentage_eng = $request->input('percentage_eng');
                    $nominee_contact_no_eng = $request->input('nominee_mobile_eng');
                    $nomineelength = count($name_of_nominee);


////            get the last id serial
                    $lastid = PersonalInfo::select('ansar_id')->orderBy('ansar_id', 'desc')->first();
                    $lastid = json_decode($lastid);
                    if (!$lastid) {
                        $lastid = 1;
                    } else
                        $lastid = $lastid->ansar_id + 1;


////            generate ansar id
//                $ansarid = $divisioncode . $unitcode . $lastid;
                    $ansarid = $lastid;
//
//
                    $personalinfo->ansar_id = $ansarid;

                    DB::beginTransaction();
                    try {

                        for ($i = 0; $i < $length; $i++) {
                            if (strlen($training_designation[$i]) != 0) {
                                $training[$i] = new TrainingInfo();
                                $training[$i]->ansar_id = $ansarid;
                                $training[$i]->training_designation = $training_designation[$i];
                                $training[$i]->training_institute_name = $training_institute_name[$i];
                                $training[$i]->training_start_date = $training_start_date[$i];
                                $training[$i]->training_end_date = $training_end_date[$i];
                                $training[$i]->trining_certificate_no = $trining_certificate_no[$i];
                                $training[$i]->training_designation_eng = $training_designation_eng[$i];
                                $training[$i]->training_institute_name_eng = $training_institute_name_eng[$i];
                                $training[$i]->training_start_date_eng = Carbon::parse( $training_start_date_eng[$i])->format("Y-m-d");
                                $training[$i]->training_end_date_eng = Carbon::parse( $training_end_date_eng[$i])->format('Y-m-d');
                                $training[$i]->trining_certificate_no_eng = $trining_certificate_no_eng[$i];
                                $successtraining = $training[$i]->save();
                            }
                        }


                        for ($i = 0; $i < $edulength; $i++) {
                            if (strlen($name_of_degree[$i]) != 0) {
                                $education[$i] = new Edication();
                                $education[$i]->ansar_id = $ansarid;
                                $education[$i]->education_id = $name_of_degree[$i];
                                $education[$i]->institute_name = $institute_name[$i];
                                $education[$i]->passing_year = $passing_year[$i];
                                $education[$i]->gade_divission = $gade_divission[$i];
                                $education[$i]->institute_name_eng = $institute_name_eng[$i];
                                $education[$i]->passing_year_eng = $passing_year_eng[$i];
                                $education[$i]->gade_divission_eng = $gade_divission_eng[$i];
                                $successeducation = $education[$i]->save();
                            }
                        }


                        for ($i = 0; $i < $nomineelength; $i++) {
                            if (strlen($name_of_nominee[$i]) != 0) {
                                $nominee[$i] = new Nominee();
                                $nominee[$i]->annsar_id = $ansarid;
                                $nominee[$i]->name_of_nominee = $name_of_nominee[$i];
                                $nominee[$i]->relation_with_nominee = $relation_with_nominee[$i];
                                $nominee[$i]->nominee_parcentage = $nominee_parcentage[$i];
                                $nominee[$i]->nominee_contact_no = $nominee_contact_no[$i];
                                $nominee[$i]->name_of_nominee_eng = $name_of_nominee_eng[$i];
                                $nominee[$i]->relation_with_nominee_eng = $relation_with_nominee_eng[$i];
                                $nominee[$i]->nominee_parcentage_eng = $nominee_parcentage_eng[$i];
                                $nominee[$i]->nominee_contact_no_eng = $nominee_contact_no_eng[$i];
                                $successnominee = $nominee[$i]->save();
                            }
                        }


//
//            get the images
//                profile picture
                        $draft_image = storage_path('drafts/photo/' . $pd);
                        if ($request->file('profile_pic')) {
                            $profileextension = $request->file('profile_pic')->getClientOriginalExtension();
                            $path = storage_path('/data/photo');
                            if (!File::exists($path)) File::makeDirectory($path, 0777, true);
                            if (File::exists($path . '/' . $ansarid . '.' . $profileextension)) {
                                File::delete($path . '/' . $ansarid . '.' . $profileextension);
                            }
                            Image::make($request->file('profile_pic'))->resize(240, 260)->save($path . '/' . $ansarid . '.' . $profileextension);
                            $personalinfo->profile_pic = '/data/photo/' . $ansarid . '.' . $profileextension;
                        } else if (File::exists($draft_image . '/profile.jpg')) {
                            $path = storage_path('/data/photo');
                            File::move($draft_image . '/profile.jpg', $path . '/' . $ansarid . '.jpg');
                        }
                        $personalinfo->profile_pic = '/data/photo/' . $ansarid . '.jpg';
////                Sign picture
                        if ($request->file('sign_pic')) {

                            $signextension = $request->file('sign_pic')->getClientOriginalExtension();
                            $path = storage_path('/data/signature');
                            //Log::info(File::exists($path. '/' . $ansarid . '.' . $signextension)?"true":"false");
                            if (!File::exists($path)) File::makeDirectory($path, 0777, true);
                            if (File::exists($path . '/' . $ansarid . '.' . $signextension)) {
                                File::delete($path . '/' . $ansarid . '.' . $signextension);
                            }
                            Image::make($request->file('sign_pic'))->resize(220, 90)->save($path . '/' . $ansarid . '.' . $signextension);
                            $personalinfo->sign_pic = '/data/signature/' . $ansarid . '.' . $signextension;
                        } else if (File::exists($draft_image . '/sign.jpg')) {
                            $path = storage_path('/data/signature');
                            File::move($draft_image . '/sign.jpg', $path . '/' . $ansarid . '.jpg');
                        }
                        $personalinfo->sign_pic = '/data/signature/' . $ansarid . '.jpg';
////                Thumb image
                        if ($request->file('thumb_pic')) {
                            $thumbextension = $request->file('thumb_pic')->getClientOriginalExtension();
                            $path = storage_path('/data/fingerprint');
                            if (!File::exists($path)) File::makeDirectory($path, 0777, true);
                            if (File::exists($path . '/' . $ansarid . '.' . $thumbextension)) {
                                File::delete($path . '/' . $ansarid . '.' . $thumbextension);
                            }
                            Image::make($request->file('thumb_pic'))->resize(220, 90)->save($path . '/' . $ansarid . '.' . $thumbextension);
                            $personalinfo->thumb_pic = '/data/fingerprint/' . $ansarid . '.' . $thumbextension;
                        } else if (File::exists($draft_image . '/thumb.jpg')) {
                            $path = storage_path('/data/fingerprint');
                            File::move($draft_image . '/thumb.jpg', $path . '/' . $ansarid . '.jpg');
                        }
                        $personalinfo->thumb_pic = '/data/fingerprint/' . $ansarid . '.jpg';

                        File::cleanDirectory($draft_image);
                        File::deleteDirectory($draft_image);
                        $successpersonal = $personalinfo->save();
                        $status = new AnsarStatusInfo();
                        $status->ansar_id = $ansarid;
                        $statusSuccess = $status->save();
                        if ($successpersonal && $statusSuccess) {
                            DB::commit();
                            $dir = storage_path() . '/drafts/';
                            if ($handle = opendir($dir)) {
                                while (($file = readdir($handle)) !== false) {
                                    if (!in_array($file, array('.', '..')) && !is_dir($dir . $file))
                                        if ($id == $file) {
                                            unlink($dir . '/' . $file);
                                        }
                                }
                            }
                            CustomQuery::addActionlog(['ansar_id' => $ansarid, 'action_type' => 'ADD ENTRY', 'from_state' => '', 'to_state' => 'ENTRY', 'action_by' => auth()->user()->id]);
                            Session::flash('success', "New Ansar added. ID: {$ansarid}");
                            return Response::json(['status' => 'saved', 'data' => 'value added successfully']);
                        }
                        throw new Exception();
                    } catch (Exception $rollback) {
                        DB::rollback();
                        return Response::json(['status' => false, 'data' => 'value not added successfully']);
                    }
                }
            }
        }
        else{
            abort(401);
        }
    }
}


