<?php

namespace App\modules\recruitment\Controllers;

use App\modules\recruitment\Models\CircularApplicantQuota;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class JobCircularApplicantQuota extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if($request->ajax()){
            $quotas = CircularApplicantQuota::all();
            return response()->json(compact('quotas'));
        }
        $quotas = CircularApplicantQuota::withTrashed()->get();
        return view('recruitment::circular_applicant_quota.index',compact('quotas'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        return view('recruitment::circular_applicant_quota.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $rules = [
            'quota_name_eng'=>'required',
            'quota_name_bng'=>'required'
        ];
        $messages = [
            'quota_name_eng'=>'Quota type name eng require',
            'quota_name_bng'=>'Quota type name bng require'
        ];
        $this->validate($request,$rules,$messages);
        DB::beginTransaction();
        try{
            CircularApplicantQuota::create($request->only(['quota_name_eng','quota_name_bng']));
            DB::commit();
        }catch(\Exception $e){
            DB::rollback();
            return redirect()->route('recruitment.quota.index')->with('error_message',$e->getMessage());
        }
        return redirect()->route('recruitment.quota.index')->with('success_message','New Quota Type Created Successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        abort(404);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $quota = CircularApplicantQuota::find($id);
        return view('recruitment::circular_applicant_quota.edit',compact('quota'));
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
        $type = $request->type;
        if(!$type) {
            $rules = [
                'quota_name_eng' => 'required',
                'quota_name_bng' => 'required'
            ];
            $messages = [
                'quota_name_eng' => 'Quota type name eng require',
                'quota_name_bng' => 'Quota type name bng require'
            ];
            $this->validate($request, $rules, $messages);
        }
        DB::beginTransaction();
        try{
            $quota = CircularApplicantQuota::withTrashed()->find($id);
            if($quota->trashed()){
                $quota->restore();
            }else{
                $quota->update($request->only(['quota_name_eng','quota_name_bng']));
            }
            DB::commit();
        }catch(\Exception $e){
            DB::rollback();
            return redirect()->route('recruitment.quota.index')->with('error_message',$e->getMessage());
        }
        return redirect()->route('recruitment.quota.index')->with('success_message','Quota Type Updated Successfully');

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request,$id)
    {

        DB::beginTransaction();
        try{
            $type = $request->type;
            if($type<0||$type>1||!is_numeric($type)) throw new \Exception('Invalid Request');
            $quota = CircularApplicantQuota::withTrashed()->find($id);
            if($type==0){
                $quota->delete();
            } else{
                $quota->forceDelete();
            }
            DB::commit();
        }catch(\Exception $e){
            DB::rollback();
            return redirect()->route('recruitment.quota.index')->with('error_message',$e->getMessage());
        }
        return redirect()->route('recruitment.quota.index')->with('success_message','Quota Type Deleted Successfully');

    }
}
