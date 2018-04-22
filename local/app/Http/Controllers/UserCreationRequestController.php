<?php

namespace App\Http\Controllers;

use App\models\UserCreationRequest;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\DB;

class UserCreationRequestController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user_create_requests = UserCreationRequest::where('action_user_id',auth()->user()->id)->get();
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
            'user_type'=>'required',
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
}
