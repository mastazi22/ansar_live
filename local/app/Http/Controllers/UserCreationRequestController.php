<?php

namespace App\Http\Controllers;

use App\Helper\SMSTrait;
use App\models\UserCreationRequest;
use App\models\User;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserCreationRequestController extends Controller
{
    use SMSTrait;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user_create_requests = UserCreationRequest::with(['userApprove.userProfile'])->withTrashed()->where('action_user_id',auth()->user()->id)->get();
        return view('user_create_request.index',compact('user_create_requests'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('user_create_request.create');
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
            'first_name'=>'required',
            'last_name'=>'required',
            'email'=>'required',
            'mobile_no'=>'required',
            'user_type'=>['required','regex:/^(dataentry|verifier|accountant|office_assistance)$/'],
        ];
        $this->validate($request,$rules);
        $inputs = $request->all();
        $inputs['user_parent_id'] = auth()->user()->id;
        DB::beginTransaction();
        try{
            UserCreationRequest::create($inputs);
            DB::commit();
        }catch(\Exception $e){
            DB::rollback();
            return redirect()->route('user_create_request.index')->with('error_message',$e->getMessage());

        }
        return redirect()->route('user_create_request.index')->with('success_message','Request created successfully');
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
    public function approveUser($id){
        DB::connection('hrm')->beginTransaction();
        try{
            $user_detail = UserCreationRequest::findOrFail($id);
            $user_name = str_random(6);
            while(User::where('user_name',$user_name)->exists()){
                $user_name = str_random(6);
            }
            $password = str_random(6);
            if($user_detail->user_type=='dataentry') {
                $type = 55;
            } else if($user_detail->user_type=='verifier') {
                $type = 44;
            }else if($user_detail->user_type=='accountant') {
                $type = 88;
            }else if($user_detail->user_type=='office_assistance') {
                $type = 99;
            }
            $user_parent_id = $user_detail->user->id;
            $user = User::create([
                'user_name'=>$user_name,
                'password'=>Hash::make($password),
                'type'=>$type,
                'user_parent_id'=>$user_parent_id,
            ]);
            $user->userProfile()->create([
               'first_name'=>$user_detail->first_name,
               'last_name'=>$user_detail->last_name,
               'email'=>$user_detail->email,
               'mobile_no'=>$user_detail->mobile_no,
            ]);
            $user->userLog()->create([]);
            $user->userPermission()->create([]);
            $user_detail->status = 'approved';
            $user_detail->user_id = $user->id;
            $user_detail->save();
            $user_detail->delete();
            $this->sendSMS("88{$user_detail->mobile_no}","your user name: {$user_name}, password: {$password}");
            DB::connection('hrm')->commit();
        }catch(\Exception $e){
            DB::connection('hrm')->rollback();
            return redirect()->back()->with('error',$e->getMessage());
        }
        return redirect()->to("/edit_user_permission/{$user->id}")->with('success',"User approved complete");
    }
    public function cancelUser($id){
        DB::connection('hrm')->beginTransaction();
        try{
            $user_detail = UserCreationRequest::findOrFail($id);
            $user_detail->status = 'canceled';
            $user_detail->save();
            $user_detail->delete();
            DB::connection('hrm')->commit();
        }catch(\Exception $e){
            DB::connection('hrm')->rollback();
            return redirect()->back()->with('error',$e->getMessage());
        }
        return redirect()->back()->with('success',"User approved complete");
    }
}
