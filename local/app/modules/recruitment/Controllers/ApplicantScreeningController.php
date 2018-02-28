<?php

namespace App\modules\recruitment\Controllers;

use App\Jobs\FeedbackSMS;
use App\modules\HRM\Models\District;
use App\modules\HRM\Models\Division;
use App\modules\HRM\Models\Thana;
use App\modules\recruitment\Models\JobApplicantHRMDetails;
use App\modules\recruitment\Models\JobApplicantMarks;
use App\modules\recruitment\Models\JobApplicantQuota;
use App\modules\recruitment\Models\JobAppliciant;
use App\modules\recruitment\Models\JobCircular;
use App\modules\recruitment\Models\JobSelectedApplicant;
use App\modules\recruitment\Models\JobSettings;
use Barryvdh\Snappy\Facades\SnappyPdf;
use Carbon\Carbon;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Intervention\Image\Facades\Image;
use Maatwebsite\Excel\Facades\Excel;
use Psy\Exception\Exception;

class ApplicantScreeningController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            DB::enableQueryLog();
            $cicular_summery = JobCircular::with('category')->withCount([
                'appliciant',
                'appliciantMale',
                'appliciantFemale',
                'appliciantPaid',
                'appliciantNotPaid',
                'appliciantInitial',
                'appliciantPaidNotApply'
            ]);
            if ($request->exists('category') && $request->category != 'all') {
                $cicular_summery->where('job_category_id', $request->category);
            }
            if ($request->exists('circular') && $request->circular != 'all') {
                $cicular_summery->where('id', $request->circular);
            }
            if ($request->exists('status') && $request->status != 'all') {
                $cicular_summery->where('circular_status', $request->status);
                /*$cicular_summery->where(function($q) use($request){
                    $q->whereHas('category',function($q) use($request){
                        $q->where('status',$request->status);
                    });
                    $q->orWhere('status',$request->status);
                });*/
            }
            $summery = $cicular_summery->get();
            Log::info(DB::getQueryLog());
            Log::info($summery);
//            return "ssddsddds";
            return response()->json($summery);
        }
        return view('recruitment::applicant.index');
    }

    public function searchApplicant()
    {
        if(auth()->user()->type==11) {
            return view('recruitment::applicant.search');
        }else{
            return view('recruitment::applicant.search_non_admin');
        }
    }

    public function loadApplicants(Request $request)
    {
        if($request->ajax()){
            $rules = [
                'category' => ['regex:/^([0-9]+)|(all)$/'],
                'circular' => ['regex:/^([0-9]+)|(all)$/'],
                'limit'=>'regex:/^[0-9]+$/'
            ];
            if(auth()->user()->type==66||auth()->user()->type==22){
                $rules['q']='required';
            }
            $this->validate($request, $rules);
            $query = JobAppliciant::whereHas('circular', function ($q) use ($request) {
                $q->where('circular_status', 'running');
                if ($request->exists('circular') && $request->circular != 'all') {
                    $q->where('id', $request->circular);
                }
                $q->whereHas('category', function ($q) use ($request) {
                    $q->where('status', 'active');
                    if ($request->exists('category') && $request->category != 'all') {
                        $q->where('id', $request->category);
                    }
                });
            })->where('status', 'applied');
            $query->join('db_amis.tbl_division as dd','dd.id','=','job_applicant.division_id');
            $query->join('db_amis.tbl_units as uu','uu.id','=','job_applicant.unit_id');
            $query->join('db_amis.tbl_thana as tt','tt.id','=','job_applicant.thana_id');
            if($request->q){
                $query->where(function($q)use($request){
                    $q->where('national_id_no','like',"%{$request->q}%");
                    $q->orWhere('applicant_id','like',"%{$request->q}%");
                    if(strtotime($request->q)){
                        $q->orwhere('date_of_birth',Carbon::parse($request->q)->format('Y-m-d'));
                    }
                });
            }
//            return response()->json($query->paginate(50));
            if(auth()->user()->type==66){
                $query->where('job_applicant.division_id',auth()->user()->division_id);
            }
            if(auth()->user()->type==22){
                $query->where('job_applicant.unit_id',auth()->user()->district_id);
            }
            if($request->select_all){
                return response()->json($query->pluck('job_applicant.applicant_id'));
            }
            if(auth()->user()->type==11)return view('recruitment::applicant.part_search',['applicants'=>$query->paginate($request->limit?$request->limit:50)]);
            else return view('recruitment::applicant.applicant_info',['applicants'=>$query->first()]);

        }
        return abort(401);
    }
    public function loadApplicantsByStatus(Request $request)
    {
        if($request->ajax()&&strcasecmp($request->method(),'post')==0){
            $rules = [
                'circular' => ['required','regex:/^([0-9]+)|(all)$/'],
                'status' => ['required','regex:/^(applied|selected|accepted)$/'],
                'limit'=>'regex:/^[0-9]+$/'
            ];
            $this->validate($request, $rules);
            $query = JobAppliciant::with(['division','district','thana'])->whereHas('circular', function ($q) use ($request) {
                $q->where('circular_status', 'running');
                if ($request->exists('circular') && $request->circular != 'all') {
                    $q->where('id', $request->circular);
                }
            })->where('status', $request->status);
            if($request->q){
                $query->where(function($q) use($request){
                    $q->orWhere('national_id_no','like','%'.$request->q.'%');
                    $q->orWhere('applicant_id','like','%'.$request->q.'%');
                    $q->orWhere('mobile_no_self','like','%'.$request->q.'%');
                    $q->orWhere('applicant_name_bng','like','%'.$request->q.'%');
                    $q->orWhere('applicant_name_eng','like','%'.$request->q.'%');
                });
            }
            if($request->range!='all'){
                $query->where('division_id',$request->range);
            }
            if($request->unit!='all'){
                $query->where('unit_id',$request->unit);
            }
            if($request->thana!='all'){
                $query->where('thana_id',$request->thana);
            }
            return view('recruitment::applicant.part_applicant_info',[
                'applicants'=>$query->paginate($request->limit?$request->limit:50),
                'status'=>$request->status
            ]);


        }
        return view('recruitment::applicant.applicant_edit_info');
    }
    public function loadApplicantsForRevert(Request $request)
    {
        if($request->ajax()&&strcasecmp($request->method(),'post')==0){
            $rules = [
                'circular' => ['required','regex:/^([0-9]+)|(all)$/'],
                'status' => ['required','regex:/^(applied|selected|accepted)$/'],
                'limit'=>'regex:/^[0-9]+$/'
            ];
            $this->validate($request, $rules);
            $query = JobAppliciant::with(['division','district','thana'])->whereHas('circular', function ($q) use ($request) {
                $q->where('circular_status', 'running');
                if ($request->exists('circular') && $request->circular != 'all') {
                    $q->where('id', $request->circular);
                }
            })->where('status', $request->status);
            if($request->q){
                $query->where(function($q) use($request){
                    $q->orWhere('national_id_no','like','%'.$request->q.'%');
                    $q->orWhere('applicant_id','like','%'.$request->q.'%');
                    $q->orWhere('mobile_no_self','like','%'.$request->q.'%');
                    $q->orWhere('applicant_name_bng','like','%'.$request->q.'%');
                    $q->orWhere('applicant_name_eng','like','%'.$request->q.'%');
                });
            }
            if($request->range!='all'){
                $query->where('division_id',$request->range);
            }
            if($request->unit!='all'){
                $query->where('unit_id',$request->unit);
            }
            if($request->thana!='all'){
                $query->where('thana_id',$request->thana);
            }
            return view('recruitment::applicant.part_applicant_status_revert',[
                'applicants'=>$query->paginate($request->limit?$request->limit:50),
                'status'=>$request->status
            ]);


        }
        return view('recruitment::applicant.applicant_status_revert');
    }
    public function revertApplicantStatus(Request $request)
    {
        if($request->ajax()){

            $status = ['applied','selected','accepted'];
            $rules = [
                'applicant_id'=>['required'],
                'status' => ['required','regex:/^(applied|selected|accepted)$/']
            ];
            $this->validate($request,$rules);
            DB::beginTransaction();
            try{
                $applicant = JobAppliciant::where('applicant_id',$request->applicant_id)->first();
                if(!$applicant){
                    throw new \Exception("Invalid applicant");
                }
                if($applicant->status==$request->status){
                    throw new \Exception("Applicant can`t change to same status");
                }
                $change_index = array_search($request->status,$status);
                $index = array_search($applicant->status,$status);
                if($change_index>=$index){
                    throw new \Exception("Applicant can`t revert to ".$request->status);
                }
                if($applicant->status=='selected'){
                    $applicant->selectedApplicant->delete();
                    if($applicant->marks)$applicant->marks->delete();
                }
                else if($applicant->status=='accepted'){
                    $applicant->accepted->delete();
                    if($applicant->marks&&$request->status=='applied')$applicant->marks->delete();
                }
                $applicant->status = $request->status;
                $applicant->save();
                DB::commit();

            }catch(\Exception $e){
                DB::rollback();
                return response()->json(['status'=>'error','message'=>$e->getMessage()]);
            }
            return response()->json(['status'=>'success','message'=>'Status change successfully']);
        }
        abort(401);
    }
    public function loadSelectedApplicant(Request $request)
    {
        if($request->ajax()){
            $query = JobAppliciant::whereHas('circular', function ($q) use ($request) {
                $q->where('circular_status', 'running');
                if ($request->exists('circular') && $request->circular != 'all') {
                    $q->where('id', $request->circular);
                }
                $q->whereHas('category', function ($q) use ($request) {
                    $q->where('status', 'active');
                    if ($request->exists('category') && $request->category != 'all') {
                        $q->where('id', $request->category);
                    }
                });
            })->where('status', 'applied');
            $query->join('db_amis.tbl_division as dd','dd.id','=','job_applicant.division_id');
            $query->join('db_amis.tbl_units as uu','uu.id','=','job_applicant.unit_id');
            $query->join('db_amis.tbl_thana as tt','tt.id','=','job_applicant.thana_id');
            if(auth()->user()->type==66){
                $query->where('job_applicant.division_id',auth()->user()->division_id);
            }
            if(auth()->user()->type==22){
                $query->where('job_applicant.unit_id',auth()->user()->district_id);
            }
            $query->where('job_applicant.applicant_id',$request->applicant_id);
            return $query->first();

        }
        return abort(401);
    }

    public function applicantListSupport(Request $request,$circular_id, $type = null)
    {
        DB::enableQueryLog();
        if ($request->q) {
            $applicants = JobAppliciant::with(['division', 'district', 'thana', 'payment'=>function($p) use ($request){
                $p->with(['paymentHistory']);
            }])->where('job_circular_id',$circular_id)
                ->where(function ($query) use ($request) {
                $query->whereHas('payment', function ($q) use ($request) {
                    $q->whereHas('paymentHistory',function ($qq) use($request){
                        $qq->where('txID','like',"%{$request->q}%");
                    });
                })->orWhere('mobile_no_self', 'like', '%' . $request->q . '%');
            });
            if ($type == 'Male' || $type == 'Female') {
                $applicants->where('gender', $type);
            } else if ($type) {
                $applicants->where('status', $type);
            }
            $applicants = $applicants->paginate(50);
        } else {
            $applicants = JobAppliciant::with(['division', 'district', 'thana', 'payment'=>function($q){
                $q->with('paymentHistory');
            }])
            ->where('job_circular_id',$circular_id); if ($type == 'Male' || $type == 'Female') {
                $applicants->where('gender', $type);
            } else if ($type) {
                $applicants->where('status', $type);
            }
            $applicants = $applicants->paginate(50);
        }
//        return DB::getQueryLog();
        return view('recruitment::applicant.applicants_support', ['applicants' => $applicants, 'type' => $type]);
    }

    public function applicantList(Request $request, $circular_id, $type = null)
    {
        if ($request->ajax()) {
            DB::enableQueryLog();
            if ($request->q) {
                $applicants = JobAppliciant::with(['division', 'district', 'thana', 'payment'])->where(function ($query) use ($request) {
                    $query->whereHas('payment', function ($q) use ($request) {
                        $q->where('txID', 'like', '%' . $request->q . '%');
                    })->orWhere('mobile_no_self', 'like', '%' . $request->q . '%');
                });
                if ($type == 'Male' || $type == 'Female') {
                    $applicants->where('gender', $type);
                } else if ($type == 'pending'||$type == 'applied'||$type == 'initial'||$type == 'paid'||$type=='selected') {
                    $applicants->where('status',$type);
                }
                if ($request->range && $request->range != 'all') {
                    $applicants->where('division_id', $request->range);
                }
                if ($request->unit && $request->unit != 'all') {
                    $applicants->where('unit_id', $request->unit);
                }
                if ($request->thana && $request->thana != 'all') {
                    $applicants->where('thana_id', $request->thana);
                }
                $applicants->where('job_circular_id', $circular_id);
                $applicants = $applicants->paginate(50);
            } else {
                $applicants = JobAppliciant::with(['division', 'district', 'thana', 'payment']);
                if ($type == 'Male' || $type == 'Female') {
                    $applicants->where('gender', $type);
                } else if ($type == 'pending'||$type == 'applied'||$type == 'initial'||$type == 'paid'||$type=='selected') {
                    $applicants->where('status',$type);
                }
                if ($request->range && $request->range != 'all') {
                    $applicants->where('division_id', $request->range);
                }
                if ($request->unit && $request->unit != 'all') {
                    $applicants->where('unit_id', $request->unit);
                }
                if ($request->thana && $request->thana != 'all') {
                    $applicants->where('thana_id', $request->thana);
                }
                $applicants->where('job_circular_id', $circular_id);
                $applicants = $applicants->paginate(50);
            }
//        return DB::getQueryLog();
            return view('recruitment::applicant.data', ['applicants' => $applicants]);
        }

        return view('recruitment::applicant.applicants', ['type' => $type, 'circular_id' => $circular_id]);
    }

    public function markAsPaid($type,$id,$circular_id)
    {
        return view('recruitment::applicant.mark_as_paid', ['id' => $id,'type'=>$type,'circular_id'=>$circular_id]);
    }

    public function updateAsPaid(Request $request, $id)
    {
        $rules = [
            'bankTxID' => 'required',
            'txID' => 'required',
            'paymentOption' => 'required',
            'type' => 'required'

        ];
        $this->validate($request, $rules);
//        return $request->all();
        DB::beginTransaction();
        try {
            $applicant = JobAppliciant::where('applicant_id', $id)->where('job_circular_id',$request->job_circular_id)->first();
            $payment = $applicant->payment;
            if ($payment) {
                $payment->returntxID = $request->txID;
                $payment->txID = $request->txID;
                $payment->bankTxID = $request->bankTxID;
                $payment->bankTxStatus = 'SUCCESS';
                $payment->txnAmount = 200;
                $payment->spCode = '000';
                $payment->spCodeDes = 'ApprovedManual';
                $payment->paymentOption = $request->paymentOption;
                $payment->save();
                $applicant->status = $request->type == 'initial' ? 'paid' : 'applied';
                $applicant->save();
                $ph = $payment->paymentHistory()->where('txID',$request->txID)->first();
                if($ph){
                    $ph->bankTxID = $request->bankTxID;
                    $ph->bankTxStatus = 'SUCCESS';
                    $ph->txnAmount = 200;
                    $ph->spCode = '000';
                    $ph->spCodeDes = 'ApprovedManual';
                    $ph->paymentOption = $request->paymentOption;
                    $ph->save();
                }
                DB::commit();
//                return $applicant;
                if ($request->type == 'initial') {
                    $message = "Your id:" . $applicant->applicant_id . " and password:" . $applicant->applicant_password;
                    $this->dispatch(new FeedbackSMS($message,$applicant->mobile_no_self));
                    }

                return redirect()->route('recruitment.applicant.list', ['type' => $request->type,'circular_id'=>$request->job_circular_id])->with('success_message', 'updated successfully');
            }
            return redirect()->route('recruitment.applicant.list',['type' => $request->type,'circular_id'=>$request->job_circular_id])->with('error_message', 'This applicant has not pay yet');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('flash_error', $e->getMessage());
        }
        return view('recruitment::applicant.mark_as_paid', ['id' => $id]);
    }

    public function updateAsPaidByFile(Request $request)
    {
        if (!strcasecmp($request->method(), 'post')) {
            $rules = [
                'file' => 'required'

            ];
            $this->validate($request, $rules);
            // return $request->file('file')->path();
            DB::beginTransaction();
            try {
                $data = Excel::load($request->file('file'), function ($reader) {

                })->get();
//                return $data;
                foreach ($data as $d) {
                    $applicant = JobAppliciant::whereHas('payment', function ($q) use ($d) {
                        $q->where('txID', trim($d['txid']));
                    })->first();
                    if ($applicant) {
                        $payment = $applicant->payment;
                        if ($payment) {
                            if ($applicant->status == 'applied') {
                                Log::info('Found exists');
                                continue;
                            }
                            $payment->returntxID = trim($d->returntxid);
                            $payment->bankTxID = trim($d->banktxid);
                            $payment->bankTxStatus = 'SUCCESS';
                            $payment->txnAmount = 200;
                            $payment->spCode = '000';
                            $payment->spCodeDes = 'ApprovedManual';
                            $payment->paymentOption = trim($d->paymentoption);
                            $payment->save();
                            $applicant->status = 'applied';
                            $applicant->save();
                            Log::info('Found ' . $d);
                            DB::commit();

                        } else {
                            Log::info('not found ' . $d);
                        }
                    } else {
                        Log::info('not found a' . $d);
                    }
//                return redirect()->route('recruitment.applicant.list')->with('error_message', 'This applicant has not pay yet');
                }
                return redirect()->route('recruitment.applicant.list', ['type' => 'pending'])->with('success_message', 'updated successfully');
            } catch (Exception $e) {
                DB::rollback();
                Log::info($e->getTraceAsString());
                return $e->getTraceAsString();
                return redirect()->back()->with('flash_error', $e->getMessage());
            }
        } else {
            return view('recruitment::applicant.mark_as_paid_file');
        }
    }

    public function getApplicantData($id){
        try {
            $data = JobAppliciant::with('appliciantEducationInfo')->where('applicant_id',$id);
            $data = $data->first();
            if($data) return ['data'=>$data,'units'=>District::where('division_id',$data->division_id)->get(),'thanas'=>Thana::where('unit_id',$data->unit_id)->get()];
            throw new \Exception('error');
        }catch (\Exception $e){
            return response()->json(['message'=>'Not found'])->setStatusCode(404);
        }

    }
    public function applicantDetailView($id){
        return response()->json(['view'=>view('recruitment::applicant.applicant_edit')->render(),'id'=>$id]);

    }
    public function updateApplicantData(Request $request){
        $rules = [
            'applicant_name_eng' => 'required',
            'applicant_name_bng' => 'required',
            'father_name_bng' => 'required',
            'mother_name_bng' => 'required',
            'date_of_birth' => 'required',
            'marital_status' => 'required',
            'national_id_no' => 'required|numeric|regex:/^[0-9]{10,17}$/',
            'division_id' => 'required',
            'unit_id' => 'required',
            'thana_id' => 'required',
            'height_feet' => 'required|numeric',
            'height_inch' => 'required|numeric',
            'gender' => 'required',
            'mobile_no_self' => 'required|regex:/^(\+?88)?0[0-9]{10}$/|unique:job_applicant,mobile_no_self,'.$request->id,
            'connection_mobile_no' => 'regex:/^(\+?88)?0[0-9]{10}$/',
        ];
        $this->validate($request,$rules);
        DB::beginTransaction();
        try{
            $educations = $request->appliciant_education_info;
            $new_data = serialize($request->all());
            unset($request['appliciant_education_info']);
            $applicant = JobAppliciant::with('appliciantEducationInfo')->find($request->id);
            unset($request['id']);
            $current_data = serialize($applicant->toArray());
            $request['date_of_birth'] = Carbon::parse($request->date_of_birth)->format('Y-m-d');
            $data = $request->except('action_user_id');

            for($i=0;$i<count($educations);$i++){
                unset($educations[$i]['id']);
                unset($educations[$i]['created_at']);
                unset($educations[$i]['updated_at']);
                unset($educations[$i]['gade_divission_eng']);
                unset($educations[$i]['name_of_degree_eng']);
                unset($educations[$i]['name_of_degree']);
                unset($educations[$i]['institute_name_eng']);
                unset($educations[$i]['passing_year_eng']);
            }
//                        return $educations;
            $applicant->update($data);
            $applicant->appliciantEducationInfo()->delete();
            $applicant->appliciantEducationInfo()->insert($educations);
            $applicant->editHistory()->create([
                'new_data'=>$new_data,
                'previous_data'=>$current_data,
                'action_user_id'=>auth()->user()->id
            ]);
            DB::commit();
            return response()->json(['status'=>'success','message'=>'info updated successfully']);
        }catch(\Exception $e){
            DB::rollback();
            return response()->json(['status'=>'error','message'=>$e->getMessage()]);
        }
    }
    public function confirmSelectionOrRejection(Request $request){
        if($request->type==='selection'){
            DB::beginTransaction();
            try{
                foreach ($request->applicants as $applicant_id){
                    $applicant = JobAppliciant::where('applicant_id',$applicant_id)->first();
                    if($applicant){
                        $applicant->update(['status'=>'selected']);
                        $applicant->selectedApplicant()->create([
                            'action_user_id'=>auth()->user()->id,
                            'message'=>$request->message
                        ]);
                    }
                }
                DB::commit();
            }catch(\Exception $e){
                DB::rollback();
                return response()->json(['status'=>'error','message'=>$e->getMessage()]);
            }
            return response()->json(['status'=>'success','message'=>'Applicants selected successfully']);
        }
        else if($request->type==='rejection'){
            DB::beginTransaction();
            try{
                foreach ($request->applicants as $applicant_id){
                    $applicant = JobAppliciant::where('applicant_id',$applicant_id)->first();
                    if($applicant){
                        $applicant->update(['status'=>'rejected']);
                    }
                }
                DB::commit();
            }catch(\Exception $e){
                DB::rollback();
                return response()->json(['status'=>'error','message'=>$e->getMessage()]);
            }
            return response()->json(['status'=>'success','message'=>'Applicants rejected successfully']);
        }
    }
    public function confirmAccepted(Request $request){
        $rules = [
            'unit'=>'required|regex:/^[0-9]+$/',
            'circular'=>'required|regex:/^[0-9]+$/',
        ];
        $this->validate($request,$rules);
        DB::beginTransaction();
        try{
            $accepted = JobAppliciant::whereHas('accepted',function($q){

            })->where('status','accepted')->where('job_circular_id',$request->circular)->where('unit_id',$request->unit)->count();
            $quota = JobApplicantQuota::where('district_id',$request->unit)->first();
            $applicant_male = JobApplicantMarks::with(['applicant'=>function($q){
                $q->with('accepted');
            }])->whereHas('applicant',function($q) use($request){

                $q->whereHas('selectedApplicant',function(){

                })->where('status','selected')->where('job_circular_id',$request->circular)->where('unit_id',$request->unit);
            })->select(DB::raw('*,(written+viva+physical+edu_training) as total_mark'))->havingRaw('total_mark>0')->orderBy('total_mark','desc');
            if($quota){
                if(intval($quota->male)-$accepted>0)$applicants = $applicant_male->limit(intval($quota->male)-$accepted)->get();
                else $applicants = [];
            }
            else $applicants = [];
            if(count($applicants)){
                foreach ($applicants as $applicant) {
                    $applicant->applicant->update(['status' => 'accepted']);
                    if(!$applicant->applicant->accepted) {
                        $applicant->applicant->accepted()->create([
                            'action_user_id' => auth()->user()->id
                        ]);
                    }
                }
            }else{
                throw new \Exception('No applicants within quota available');
            }
            DB::commit();
        }catch(\Exception $e){
            DB::rollback();
            return response()->json(['status'=>'error','message'=>$e->getMessage()]);
        }
        return response()->json(['status'=>'success','message'=>'Applicant accepted successfully']);
    }

    public function loadImage(Request $request){
        $image = base64_decode($request->file);
        if (!$request->exists('file')) return Image::make(public_path('dist/img/nimage.png'))->response();
        if (is_null($image) || !File::exists($image) || File::isDirectory($image)) {
            return Image::make(public_path('dist/img/nimage.png'))->response();
        }
        //return $image;
        return Image::make($image)->response();
    }
    public function applicantEditField(Request $request){
        return view('recruitment::applicant.applicant_edit_field');
    }
    public function saveApplicantEditField(Request $request){
        $fields = array_filter($request->fields);
        $js = JobSettings::where('field_type','applicants_field')->first();
        if($js){
            $js->update(['field_value'=>implode(',',$fields)]);
        }
        else{
            JobSettings::create([
                'field_type'=>'applicants_field',
                'field_value'=>implode(',',$fields)
            ]);
        }
        return response()->json(['status'=>'success','message'=>'operation complete successfully']);
    }
    public function loadApplicantEditField(){
        $js = JobSettings::where('field_type','applicants_field')->first();
        return $js;
    }
    public function acceptedApplicantView()
    {
        return view('recruitment::applicant.applicant_accepted');
    }
    public function loadApplicantByQuota(Request $request)
    {

        DB::enableQueryLog();
        $rules = [
            'unit'=>'required|regex:/^[0-9]+$/',
            'circular'=>'required|regex:/^[0-9]+$/',
        ];
        $this->validate($request,$rules);
        $quota = JobApplicantQuota::where('district_id',$request->unit)->first();
        $accepted = JobAppliciant::whereHas('accepted',function($q){

        })->where('status','accepted')->where('job_circular_id',$request->circular)->where('unit_id',$request->unit)->count();
        $applicant_male = JobApplicantMarks::with(['applicant'=>function($q){
            $q->with(['district','division','thana']);
        }])->whereHas('applicant',function($q) use($request){

            $q->whereHas('selectedApplicant',function(){

            })->where('status','selected')->where('job_circular_id',$request->circular)->where('unit_id',$request->unit);
        })->select(DB::raw('DISTINCT *,(written+viva+physical+edu_training) as total_mark'))->havingRaw('total_mark>0')->orderBy('total_mark','desc');
        $applicants=[];
        if($quota){
            if(intval($quota->male)-$accepted>0)$applicants = $applicant_male->limit(intval($quota->male)-$accepted)->get();
//            else return view('recruitment::applicant.data_accepted',['applicants'=>[]]);
        }
//        else return view('recruitment::applicant.data_accepted',['applicants'=>[]]);
       // $a = $applicant_male->get();
       // return DB::getQueryLog();
        if($request->exists('export')&&$request->export=='excel'){
            Excel::create('accepted_list',function ($excel) use($applicants){
                $excel->sheet('sheet1',function ($sheet) use($applicants){
                    $sheet->loadView('recruitment::applicant.data_accepted',['applicants'=>$applicants]);
                });
            })->download('xls');
        }
        else return view('recruitment::applicant.data_accepted',['applicants'=>$applicants]);

    }


    public function moveApplicantToHRM(Request $request){
        if(strcasecmp($request->method(),'post')==0){
            if($request->ajax()){
                $applicants= JobAppliciant::with(['division','district','thana'])
                    ->where('status','accepted')->where('job_circular_id',$request->circular);
                if($request->range&&$request->range!='all'){
                    $applicants->where('division_id',$request->range);
                }
                if($request->unit&&$request->unit!='all'){
                    $applicants->where('unit_id',$request->unit);
                }
                if($request->thana&&$request->thana!='all'){
                    $applicants->where('thana_id',$request->thana);
                }
                if($request->q){
                    $applicants->where(function ($q)use ($request){
                        $q->where('applicant_name_eng','LIKE','%'.$request->q.'%');
                        $q->orWhere('applicant_name_bng','LIKE','%'.$request->q.'%');
                        $q->orWhere('mobile_no_self',$request->q);
                        $q->orWhere('national_id_no',$request->q);
                    });
                }
                $limit = $request->limit?$request->limit:50;
                return view('recruitment::applicant.part_hrm_applicant_info',['applicants'=>$applicants->paginate($limit),'type'=>'download']);
            }
            else{
                abort(401);
            }
        }
        return view('recruitment::applicant.move_applicant_to_hrm');
    }
    public function editApplicantForHRM(Request $request){
        if(strcasecmp($request->method(),'post')==0){
            if($request->ajax()){
                $applicants= JobAppliciant::with(['division','district','thana'])
                    ->doesnthave('hrmDetail')
                    ->where('status','accepted')->where('job_circular_id',$request->circular);
                if($request->range&&$request->range!='all'){
                    $applicants->where('division_id',$request->range);
                }
                if($request->unit&&$request->unit!='all'){
                    $applicants->where('unit_id',$request->unit);
                }
                if($request->thana&&$request->thana!='all'){
                    $applicants->where('thana_id',$request->thana);
                }
                if($request->q){
                    $applicants->where(function ($q)use ($request){
                        $q->where('applicant_name_eng','LIKE','%'.$request->q.'%');
                        $q->orWhere('applicant_name_bng','LIKE','%'.$request->q.'%');
                        $q->orWhere('mobile_no_self',$request->q);
                        $q->orWhere('national_id_no',$request->q);
                    });
                }
                $limit = $request->limit?$request->limit:50;
                return view('recruitment::applicant.part_hrm_applicant_info',['applicants'=>$applicants->paginate($limit),'type'=>'edit']);
            }
            else{
                abort(401);
            }
        }
        return view('recruitment::applicant.edit_applicant_to_hrm');
    }
    public function applicantEditForHRM($type,$id){
        $applicant = JobAppliciant::with(['division','district','thana','appliciantEducationInfo'=>function($q){
            $q->with('educationInfo');
        }])->where('applicant_id',$id)->first();
        if($type=='download') {
            $pdf = SnappyPdf::loadView('recruitment::hrm.hrm_form_download', ['ansarAllDetails' => $applicant])
                ->setOption('encoding', 'UTF-8')
                ->setOption('zoom', 0.73);
            return $pdf->download();
        }else{
            return response()->json(['view'=>view('recruitment::applicant.applicant_edit_for_hrm')->render(),'id'=>$id]);
        }
//        return view('recruitment::hrm.hrm_form_download',['ansarAllDetails'=>$applicant]);
    }

    public function storeApplicantHRmDetail(Request $request){
        $rules = [
            'applicant_id' => 'required|unique:recruitment.job_applicant_hrm_details',
            'ansar_name_eng' => 'required',
            'ansar_name_bng' => 'required',
            'designation_id' => 'required',
            'father_name_bng' => 'required',
            'mother_name_bng' => 'required',
            'data_of_birth' => 'required',
            'marital_status' => 'required',
            'national_id_no' => 'required|numeric|regex:/^[0-9]{10,17}$/',
            'division_id' => 'required',
            'unit_id' => 'required',
            'thana_id' => 'required',
            'blood_group_id' => 'required',
            'hight_feet' => 'required',
            'sex' => 'required',
            'mobile_no_self' => 'required|regex:/^(\+88)?0[0-9]{10}$/|unique:hrm.tbl_ansar_parsonal_info',
        ];
        $this->validate($request,$rules);
        $columns = Schema::connection('recruitment')->getColumnListing('job_applicant_hrm_details');
        $inputs = $request->all();
        unset($columns['id']);
        unset($inputs['id']);
        unset($inputs['created_at']);
        unset($columns['created_at']);
        unset($inputs['updated_at']);
        unset($columns['updated_at']);
        $c = [];
        foreach ($inputs as $key=>$value){
            if(in_array($key,$columns)){
                continue;
            }
            unset($inputs[$key]);
        }

        $inputs['applicant_nominee_info'] = json_encode($inputs['applicant_nominee_info']);
        $inputs['applicant_training_info'] = json_encode($inputs['applicant_training_info']);
        $inputs['appliciant_education_info'] = json_encode($inputs['appliciant_education_info']);
//        return $inputs;
        DB::beginTransaction();
        try{
            $file_path = storage_path('rece');
            if(!File::exists($file_path)) File::makeDirectory($file_path);
            if(isset($inputs['sign_pic'])){
                $img = Image::make($inputs['sign_pic']);
                $img->save($file_path.'/'.$inputs['applicant_id'].'_sign.jpg');
                $inputs['sign_pic'] = $file_path.'/'.$inputs['applicant_id'].'_sign.jpg';
            }
            if(isset($inputs['profile_pic'])){
                $img = Image::make($inputs['profile_pic']);
                $img->save($file_path.'/'.$inputs['applicant_id'].'_profile.jpg');
                $inputs['profile_pic'] = $file_path.'/'.$inputs['applicant_id'].'_profile.jpg';
            }
            $applicant = JobAppliciant::where('applicant_id',$inputs['applicant_id'])->where('status','accepted')->first();
            if(!$applicant) throw new \Exception('Invalid applicant');
            $applicant->hrmDetail()->save(new JobApplicantHRMDetails($inputs));
            DB::commit();
            return response()->json(['status'=>'success','message'=>'Data inserted successfully']);
        }catch(\Exception $e){
            DB::rollback();
            return response()->json(['status'=>'error','message'=>$e->getMessage()]);
        }
    }

}
