<?php

namespace App\modules\HRM\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\modules\HRM\Models\SessionModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Mockery\Exception;

class SessionController extends Controller
{
    public function SessionName(){
        $sessions=SessionModel::all();
        return Response::json($sessions);
    }
    public function index()
    {
        return view('HRM::Session.session_entry');
    }

    public function saveSessionEntry(Request $request)
    {
        $rules = array(
            'session_year' => 'required|numeric|min:2016|max:3000',
            'session_start_month' => 'required|regex:/[A-Z]([a-z]+)/|min:1|max:9',
            'session_end_month' => 'required|regex:/[A-Z]([a-z]+)/|min:1|max:9',
            'session_name' => 'required|regex:/^[0-9]{4}\-[0-9]{4}$/'
        );
        $validation = Validator::make(Input::all(), $rules);

        if (!$validation->fails()) {
            DB::beginTransaction();
            try {
                $session_entry = new SessionModel();
                $session_entry->session_year = $request->input('session_year');
                $session_entry->session_start_month = $request->input('session_start_month');
                $session_entry->session_end_month = $request->input('session_end_month');
                $session_entry->session_name = $request->input('session_name');
                $session_entry->save();
                DB::commit();
            } catch
            (Exception $e) {
                DB::rollback();
                return $e->getMessage();
            }

            return Redirect::route('view_session_list')->with('success_message', 'New Session is Entered Successfully!');
        } else {
            return Redirect::route('create_session')->withInput(Input::all())->withErrors($validation);
        }
    }

    public function sessionView()
    {
        $session_info = SessionModel::paginate(config('app.item_per_page'));
        return view('HRM::Session.session_view')->with('session_info', $session_info);
    }

    public function sessionDelete($id)
    {
        SessionModel::find($id)->delete();
        return redirect('HRM/session_view');
    }
    public function sessionEdit($id, $page)
    {
        $session_info = SessionModel::find($id);
        return view('HRM::Session.session_edit', ['id' => $id, 'page' => $page])->with(['session_info'=> $session_info, 'page' => $page, 'id' => $id]);
    }

    public function sessionUpdate(Request $request)
    {
        $id = $request->input('id');

        $rules = array(
            'session_year' => 'required|numeric|min:2016|max:3000',
            'session_start_month' => 'required|regex:/[A-Z]([a-z]+)/|min:1|max:9',
            'session_end_month' => 'required|regex:/[A-Z]([a-z]+)/|min:1|max:9',
            'session_name' => 'required|regex:/^[0-9]{4}\-[0-9]{4}$/',
        );
        $validation = Validator::make(Input::all(), $rules);

        if (!$validation->fails()) {
            DB::beginTransaction();
            try {
                $session_info = SessionModel::find($id);
                $session_info->session_year = $request->input('session_year');
                $session_info->session_start_month = $request->input('session_start_month');
                $session_info->session_end_month = $request->input('session_end_month');
                $session_info->session_name = $request->input('session_name');
                $session_info->save();
                DB::commit();
            } catch
            (Exception $e) {
                DB::rollback();
                return $e->getMessage();
            }
            return Redirect::to('HRM/session_view?page=' . $request->input('page'))->with('success_message', 'New Session is Updated Successfully!');
        } else {
            return Redirect::back()->withInput(Input::all())->withErrors($validation);
        }
    }
}

