<?php

namespace App\modules\recruitment\Controllers;

use App\Jobs\FeedbackSMS;
use App\modules\HRM\Models\District;
use App\modules\HRM\Models\Division;
use App\modules\HRM\Models\Thana;
use App\modules\recruitment\Models\JobAppliciant;
use App\modules\recruitment\Models\JobCircular;
use App\modules\recruitment\Models\JobSelectedApplicant;
use Carbon\Carbon;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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
            return response()->json($summery);
        }
        return view('recruitment::applicant.index');
    }

    public function searchApplicant()
    {
        return view('recruitment::applicant.search');
    }

    public function loadApplicants(Request $request)
    {
        if($request->ajax()){
            $rules = [
                'category' => ['regex:/^([0-9]+)|(all)$/'],
                'circular' => ['regex:/^([0-9]+)|(all)$/'],
                'limit'=>'regex:/^[0-9]+$/'
            ];
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
                $query->where('national_id_no',$request->q);
            }
            if($request->filter){
                foreach ($request->filter as $key=>$value){
                    if($value['value']){
                        if($key=='height'){
                            $height = ($value['feet']?floatval($value['feet']):0)*12+($value['feet']?floatval($value['inch']):0);
                            $query->whereRaw('(height_feet*12+height_inch)'.$value['comparator'].$height);
                        }
                        else if($key=='age' && $value['data']){
                            $query->whereRaw('DATEDIFF(NOW(),date_of_birth)/365'.$value['comparator'].$value['data'] );
                        }
                        else if($key=='training'){
                            $query->whereNotNull('training_info');
                        }
                        else if($key=='reference'&&$value['data']){
                            $query->whereNotNull('connection_relation');
                            $query->where('connection_relation',$value['data']);
                        }
                        else if($key=='education'){
                            $query->whereHas('appliciantEducationInfo',function ($q) use ($value){
                                $q->where('job_education_id',$value['comparator'],$value['data']);
                            });
                        }
                        else if(isset($value['data'])&&$value['data']&&$key!='applicant_quota'){
                            $query->where($key,$value['comparator'],$value['data']);
                        }
                    }
                }
                if($request->filter['applicant_quota']['value']){
                    $query->join('job_applicant_quota','job_applicant_quota.district_id','=','job_applicant.unit_id');

                    $query->selectRaw("job_applicant.*,dd.division_name_bng,uu.unit_name_bng,tt.thana_name_bng,job_applicant_quota.male as male_count,job_applicant_quota.female as female_count,@unit:= IF(@current_unit=job_applicant.`unit_id`,@unit+1,1) AS unit_limit,
@current_unit:=job_applicant_quota.district_id AS districtt")->orderBy('job_applicant_quota.district_id');
                    $q = clone $query;
                    $query = DB::table(DB::raw('('.$q->toSql().') x'))->mergeBindings($q->getQuery())->selectRaw('*')->whereRaw('unit_limit<=male_count');
                }
            }
//            return response()->json($query->paginate(50));
            if($request->select_all){
                return response()->json($query->pluck('job_applicant.applicant_id'));
            }
            return view('recruitment::applicant.part_search',['applicants'=>$query->paginate($request->limit?$request->limit:50)]);

        }
        return abort(401);
    }

    public function applicantListSupport(Request $request, $type = null)
    {
        DB::enableQueryLog();
        if ($request->q) {
            $applicants = JobAppliciant::with(['division', 'district', 'thana', 'payment'])->where(function ($query) use ($request) {
                $query->whereHas('payment', function ($q) use ($request) {
                    $q->where('txID', 'like', '%' . $request->q . '%');
                })->orWhere('mobile_no_self', 'like', '%' . $request->q . '%');
            });
            if ($type == 'applied') {
                $applicants->whereHas('payment', function ($q) {
                    $q->whereNotNull('txID');
                    $q->where('bankTxStatus', 'SUCCESS');
                });
                $applicants->where('status', $type);
            } else if ($type == 'Male' || $type == 'Female') {
                $applicants->where('gender', $type);
            } else if ($type) {
                $applicants->where('status', $type);
            }
            $applicants = $applicants->paginate(50);
        } else {
            $applicants = JobAppliciant::with(['division', 'district', 'thana', 'payment']);
            if ($type == 'applied') {
                $applicants->whereHas('payment', function ($q) {
                    $q->whereNotNull('txID');
                });
            } else if ($type == 'Male' || $type == 'Female') {
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
                if ($type == 'applied') {
                    $applicants->whereHas('payment', function ($q) {
                        $q->whereNotNull('txID');
                        $q->where('bankTxStatus', 'SUCCESS');
                    });
                    $applicants->where('status','applied');
                } else if ($type == 'Male' || $type == 'Female') {
                    $applicants->where('gender', $type);
                } else if ($type == 'pending') {
                    $applicants->where(function ($q) {
                        $q->whereHas('payment', function ($q) {
                            $q->whereNotNull('txID');
                            $q->where('bankTxStatus', 'FAIL');
                        });
                        $q->orWhere('status', 'pending');
                    });
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
                if ($type == 'applied') {
                    $applicants->whereHas('payment', function ($q) {
                        $q->whereNotNull('txID');
                        $q->where('bankTxStatus', 'SUCCESS');
                    });
                    $applicants->where('status','applied');
                }
                if ($type == 'Male' || $type == 'Female') {
                    $applicants->where('gender', $type);
                } else if ($type == 'pending') {
                    $applicants->where(function ($q) {
                        $q->whereHas('payment', function ($q) {
                            $q->whereNotNull('txID');
                            $q->where('bankTxStatus', 'FAIL');
                        });
                        $q->orWhere('status', 'pending');
                    });
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

    public function markAsPaid($type,$id)
    {
        return view('recruitment::applicant.mark_as_paid', ['id' => $id,'type'=>$type]);
    }

    public function updateAsPaid(Request $request, $id)
    {
        $rules = [
            'bankTxID' => 'required',
            'paymentOption' => 'required',
            'type' => 'required'

        ];
        $this->validate($request, $rules);
//        return $request->all();
        DB::beginTransaction();
        try {
            $applicant = JobAppliciant::where('applicant_id', $id)->first();
            $payment = $applicant->payment;
            if ($payment) {
                $payment->returntxID = $payment->txID;
                $payment->bankTxID = $request->bankTxID;
                $payment->bankTxStatus = 'SUCCESS';
                $payment->txnAmount = 200;
                $payment->spCode = '000';
                $payment->spCodeDes = 'ApprovedManual';
                $payment->paymentOption = $request->paymentOption;
                $payment->save();
                $applicant->status = $request->type == 'initial' ? 'paid' : 'applied';
                $applicant->save();
                DB::commit();
//                return $applicant;
                if ($request->type == 'initial') {
                    $message = "Your id:" . $applicant->applicant_id . " and password:" . $applicant->applicant_password;
                    $this->dispatch(new FeedbackSMS($message,$applicant->mobile_no_self));
                    }

                return redirect()->route('recruitment.applicant.list', ['type' => $request->type])->with('success_message', 'updated successfully');
            }
            return redirect()->route('recruitment.applicant.list',['type' => $request->type])->with('error_message', 'This applicant has not pay yet');
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
        return view('recruitment::applicant.applicant_edit',['id'=>$id]);

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
                if($request->sub_type==0){
                    $pre_selected = JobSelectedApplicant::where('action_user_id',auth()->user()->id)->get();
                    foreach ($pre_selected as $p){
                        $p->applicant()->update(['status'=>'applied']);
                        $p->delete();
                    }
                }
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
}
