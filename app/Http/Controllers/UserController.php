<?php

namespace App\Http\Controllers;

use App\Events\ActionUserEvent;
use App\Http\Requests;
use App\models\CustomQuery;
use App\models\EmbodimentModel;
use App\models\GlobalParameter;
use App\models\MemorandumModel;
use App\models\TransferAnsar;
use App\models\User;
use App\models\UserLog;
use App\models\UserPermission;
use App\models\UserProfile;
use App\models\UserType;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;
use Intervention\Image\Facades\Image;
use Mockery\CountValidator\Exception;

class UserController extends Controller
{

    function handleLogin()
    {
        $credential = array('user_name' => Input::get('user_name'), 'password' => Input::get('password'));
        if (Auth::attempt($credential)) {
            $user = Auth::user();
            if ($user->status == 0) {
                Auth::logout();
                return Redirect::action('UserController@login')->with('error', 'Your blocked. Please contact with administrator');
            }
            $user->userLog->last_login = Carbon::now()->addHours(6);
            if ($user->userLog->user_status == 0) $user->userLog->user_status = 1;
            $user->userLog->save();
            if(Session::get('redirect_url'))return Redirect::to(Session::get('redirect_url'));
            else return Redirect::to('/');
        } else {
            return Redirect::action('UserController@login')->with('error', 'Invalid user name or password');
        }
    }

    function logout()
    {
        Auth::logout();
        return Redirect::action('UserController@login');

    }

    function login()
    {
        if (Auth::check()) return Redirect::action('UserController@hrmDashboard');
        return View::make('login_screen');
    }

    function hrmDashboard()
    {
        $type = auth()->user()->type;
        if ($type == 22 || $type == 66) {
            return View::make('template.hrm-rc-dc');
        } else {
            return View::make('template.hrm');
        }
    }

    function userRegistration()
    {
        $types = UserType::all();
        return View::make('user_registration')->with('types', $types);
    }

    function userManagement()
    {
        $users = User::count();
        return View::make('user_management')->with('total_user', $users);
    }

    function handleRegister()
    {
        $rules = array(
            'user_name' => 'required|unique:tbl_user',
            'password' => 'required|min:5|max:12',
            'r_password' => 'required|same:password'
        );
        $validation = Validator::make(Input::all(), $rules);
        if (!$validation->fails()) {
            $user = new User;
            $user_profile = new UserProfile;
            $user_log = new UserLog;
            $user_permission = new UserPermission;
            $user->user_name = Input::get('user_name');
            $user->type = Input::get('user_type');
            if (Input::get('user_type') == 22) {
                $user->district_id = Input::get('district_name');
            } else if (Input::get('user_type') == 66) {
                $user->division_id = Input::get('division_name');
            }
            $user->password = Hash::make(Input::get('password'));
            $user->save();
            $user_profile->user_id = $user->id;
            $user_profile->save();
            $user_log->user_id = $user->id;
            $user_log->save();
            $user_permission->user_id = $user->id;
            $user_permission->save();
            return Redirect::action('UserController@userManagement')->with('success_message', 'New user created successfully');
        } else {
            return Redirect::action('UserController@userRegistration')->withInput(Input::except(array('password', 'r_password')))->withErrors($validation);
        }
    }

    function viewProfile($id)
    {
        return View::make('user_profile')->with('user', User::find($id));
    }

    function updateProfile()
    {
        $user = Auth::user();
        $user->userProfile->first_name = Input::get('first_name');
        $user->userProfile->last_name = Input::get('last_name');
        $user->userProfile->email = Input::get('email');
        $user->userProfile->office_phone_no = Input::get('office_phone_no');
        $user->userProfile->mobile_no = Input::get('mobile_no');
        $user->userProfile->contact_address = Input::get('contact_address');
        $user->userProfile->rank = Input::get('rank');
        $user->userProfile->save();
        return Response::json(['submit' => true]);
    }

    function editUser($id)
    {
        return View::make('edit_user')->with('id', $id);
    }

    function changeUserName()
    {
        $id = Input::get('user_id');
        $rules = [
            'user_name' => 'required|unique:tbl_user,user_name,' . $id
        ];
        $valid = Validator::make(Input::all(), $rules);
        if ($valid->fails()) {
            return Response::json(['validation' => true]);
        } else {
            $user = User::find($id);
            $user->user_name = Input::get('user_name');
            if ($user->save()) {
                return Response::json(['submit' => true]);
            } else {
                return Response::json(['submit' => false]);
            }
        }
    }

    function changeUserPassword()
    {
        $id = Input::get('user_id');
        if (Input::exists('old_password')) {
            $user = User::find($id);
            if (!Hash::check(Input::get('old_password'), $user->password)) {
                return Response::json(['validation' => true, 'error' => ['old_password' => 'Old password does not match']]);
            }
        }
        $rules = [
            'password' => 'required',
            'c_password' => 'same:password'
        ];
        $messages = [
            'required' => 'New password is required',
            'same' => 'Password mis-match'
        ];
        $valid = Validator::make(Input::all(), $rules, $messages);
        if ($valid->fails()) {
            return Response::json(['validation' => true, 'error' => $valid->errors()]);
        } else {
            $user = User::find($id);
            $user->password = Hash::make(Input::get('password'));
            if ($user->save()) {
                return Response::json(['submit' => true]);
            } else {
                return Response::json(['submit' => false]);
            }
        }
    }

    function blockUser()
    {
        $id = Input::get('user_id');
        $user = User::find($id);
        $user->status = 0;
        if ($user->save()) return Response::json(['status' => true]);
        else return Response::json(['status' => false]);
    }

    function unBlockUser()
    {
        $id = Input::get('user_id');
        $user = User::find($id);
        $user->status = 1;
        if ($user->save()) return Response::json(['status' => true]);
        else return Response::json(['status' => false]);
    }

    function editUserPermission($id)
    {
        $read_permission_file = file_get_contents(storage_path("user/permission/permission_list.json"));
        $routes = json_decode($read_permission_file);
        $user = User::find($id);
        if ($user->userPermission->permission_type == 0) {
            if (is_null($user->userPermission->permission_list)) {
                $permission = null;
            } else {
                $permission = json_decode($user->userPermission->permission_list);
            }
        } else {
            $permission = 'all';
        }
        return View::make('user_permission_view')->with(array('routes' => json_encode($routes), 'id' => $id, 'access' => json_encode($permission)));
    }

    function updatePermission($id)
    {
        $user = User::find($id);
        $all = Input::get('permit_all');
        if (is_null($all)) {
            $permission = json_encode(Input::get('permission'));
            $user->userPermission->permission_type = 0;
            $user->userPermission->permission_list = $permission;
            $user->userPermission->save();
        } else {
            $user->userPermission->permission_type = 1;
            $user->userPermission->permission_list = null;
            $user->userPermission->save();
        }
        return Redirect::action('UserController@userManagement')->with('success_message', $user->user_name . " permission has been updated successfully");
    }

    function getAllUser()
    {
        return response()->json(CustomQuery::getUserInformation(Input::get('limit'), Input::get('offset')));
    }

    function completeTransferProcess()
    {
        $m_id = Input::get('memorandum_id');
        $t_date = Input::get('transfer_date');
        $kpi_id = Input::get('kpi_id');
//        return $t_date;
//        return $kpi_id;
        $transferred_ansar = Input::get('transferred_ansar');
        //$p =  json_decode($transferred_ansar[0]);
//        return var_dump($transferred_ansar);
        $status = array('success' => array('count' => 0, 'data' => array()), 'error' => array('count' => 0, 'data' => array()));
        //return $status;
        DB::beginTransaction();
        try {
            $memorandum = new MemorandumModel;
            $memorandum->memorandum_id = $m_id;
            $memorandum->save();
            foreach ($transferred_ansar as $ansar) {
                DB::beginTransaction();
                try {
                    $e_id = EmbodimentModel::where('ansar_id', $ansar['ansar_id'])->first();
                    $e_id->kpi_id = $kpi_id[1];
                    $e_id->transfered_date = Carbon::createFromFormat("d-M-Y",$t_date)->format("Y-m-d");
                    $e_id->save();
                    $transfer = new TransferAnsar;
                    $transfer->ansar_id = $ansar['ansar_id'];
                    $transfer->embodiment_id = $e_id->id;
                    $transfer->transfer_memorandum_id = $m_id;
                    $transfer->present_kpi_id = $kpi_id[0];
                    $transfer->transfered_kpi_id = $kpi_id[1];
                    $transfer->present_kpi_join_date = $ansar['joining_date'];
                    $transfer->transfered_kpi_join_date = Carbon::createFromFormat("d-M-Y",$t_date)->format("Y-m-d");
                    $transfer->action_by = Auth::user()->id;
                    $transfer->save();
                    DB::commit();
                    $status['success']['count']++;
                    array_push($status['success']['data'], $ansar['ansar_id']);
                    CustomQuery::addActionlog(['ansar_id' => $ansar['ansar_id'], 'action_type' => 'TRANSFER', 'from_state' => $kpi_id[0], 'to_state' => $kpi_id[1], 'action_by' => auth()->user()->id]);
                } catch (Exception $e) {
                    DB::rollback();
                    $status['error']['count']++;
                    array_push($status['error']['data'], $ansar['ansar_id']);
                }
            }
            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            $status['error']['count'] = count($transferred_ansar);
            //return Response::json(['status'=>false,'message'=>'Can`t transfer ansar. There is an error.Please try again later']);
        }
        return Response::json(['status' => true, 'data' => $status]);
    }

    function verifyMemorandumId()
    {
        $rule = [
            'memorandum_id' => 'required|unique:tbl_memorandum_id'
        ];
        $v = Validator::make(Input::all(), $rule);
        return Response::json(array('status' => $v->fails()));
    }

    function globalParameterView()
    {
        return View::make('global_perameter')->with('gp', GlobalParameter::all());
    }

    function changeUserImage()
    {
        $file = Input::file('image_file');
        //return $file;
        $user_folder = storage_path('user/img');
        if (!File::exists($user_folder)) {
            File::makeDirectory($user_folder, 0777, true);
        }
//        if(is_null($file)) return ;
        $file_name = Auth::user()->id . "." . $file->getClientOriginalExtension();
        $path = $user_folder . '/' . $file_name;
        if (File::exists($path)) File::delete($path);
        $status = Image::make($file)->resize(200, 200)->save($path);
        if ($status) {
            $p = Auth::user()->userProfile;
            $p->profile_image = 'user/img/' . $file_name;
            $p->save();
            return Response::json(['status' => true]);
        } else return Response::json(['status' => false]);
    }

    function updateGlobalParameter()
    {
        $id = Input::get('id');
        $pv = Input::get('pv');
        $pd = Input::get('pd');
        $pp = Input::get('pp');
        $pu = Input::get('pu');
        DB::beginTransaction();
        try {
            $gp = GlobalParameter::find($id);
            $gp->param_value = $pv;
            $gp->param_description = $pd;
            $gp->param_piority = $pp;
            $gp->param_unit = $pu;
            $gp->save();
            DB::commit();
        } catch (Exception $e) {
            return Response::json(['status' => false, 'data' => 'Unable to update. try again later']);
        }

        return Response::json(['status' => true, 'data' => 'Update complete successfully']);
    }

    public function userSearch()
    {
        $username = Input::get('user_name');
        $users = DB::table('tbl_user')
            ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'tbl_user.id')
            ->join('tbl_user_log', 'tbl_user_log.user_id', '=', 'tbl_user.id')
            ->where('tbl_user.user_name', 'LIKE', '%' . $username . '%')
            ->select('tbl_user.id', 'tbl_user.user_name', 'tbl_user_details.first_name', 'tbl_user_details.last_name', 'tbl_user_details.email', 'tbl_user_log.last_login', 'tbl_user_log.user_status', 'tbl_user.status')
            ->get();
        return Response::json($users);
    }

    public function getImage()
    {
        $image = storage_path(Input::get('file'));
        if(!Input::exists('file')) return Image::make(public_path('dist/img/nimage.png'))->response();
        if (is_null($image) || !File::exists($image)|| File::isDirectory($image)) {
            return Image::make(public_path('dist/img/nimage.png'))->response();
        }
        //return $image;
        return Image::make($image)->response();
    }
} 


