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
            'session_year' => 'required',
            'session_start_month' => 'required',
            'session_end_month' => 'required',
            'session_name' => 'required',
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

            return Redirect::action('SessionController@sessionView')->with('success_message', 'New Session is Entered successfully');
        } else {
            return Redirect::action('SessionController@index')->withInput(Input::all())->withErrors($validation);
        }
    }

    public function sessionView()
    {
        $session_info = SessionModel::paginate(10);
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
        return view('HRM::Session.session_edit', ['id' => $id, 'page' => $page])->with(['session_info'=> $session_info, 'page' => $page]);
    }

    public function sessionUpdate(Request $request)
    {
        $id = $request->input('id');
        $rules = array(
            'session_year' => 'different:select-year',
            'session_start_month' => 'different:select-start-month',
            'session_end_month' => 'different:select-end-month',
            'session_name' => 'required',
        );
        $messages = array(
            'session_year.different' => 'The session year field is required.',
            'session_start_month.different' => 'The session start month field is required.',
            'session_end_month.different' => 'The session end month field is required.',
        );
        $validation = Validator::make(Input::all(), $rules, $messages);

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
            return Redirect::to('session_view?page=' . $request->input('page'))->with('success_message', 'New Session is Updated successfully');
        } else {
            return Redirect::action('SessionController@sessionEdit', ['id' => $id])->withInput(Input::all())->withErrors($validation);
        }
    }
}

