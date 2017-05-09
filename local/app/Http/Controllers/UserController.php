<?php

namespace App\Http\Controllers;

use App\Events\ActionUserEvent;
use App\Http\Requests;
use App\models\User;
use App\models\UserLog;
use App\models\UserPermission;
use App\models\UserProfile;
use App\models\UserType;
use App\modules\HRM\Models\CustomQuery;
use App\modules\HRM\Models\ForgetPasswordRequest;
use App\modules\HRM\Models\PersonalInfo;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;
use Intervention\Image\Facades\Image;

class UserController extends Controller
{
    use AuthenticatesUsers, ThrottlesLogins;

    protected $maxLoginAttempts = 3; // Amount of bad attempts user can make
    protected $lockoutTime = 300; // Time for which user is going to be blocked in seconds

    function handleLogin(Request $request)
    {
        $credential = array('user_name' => Input::get('user_name'), 'password' => Input::get('password'));
        Log::info("Previous URL Handle: " . Session::get('redirect_url'));
        $throttles = $this->isUsingThrottlesLoginsTrait();
        if ($throttles && $lockedOut = $this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);
            $key = $this->getThrottleKey($request) . ':lockout';


            return $this->sendLockoutResponse($request);
        }
        if (Auth::attempt($credential)) {
            $user = Auth::user();
            if ($user->status == 0) {
                Auth::logout();
                return Redirect::action('UserController@login')->with('error', 'Your blocked. Please contact with administrator');
            }
//            if($user->logged){
//                Auth::logout();
//                return Redirect::action('UserController@login')->with('error', 'You can`t login at this moment. Someone login with your account');
//            }
            $log = $user->userLog;
            if ($log) {
                $user->userLog->last_login = Carbon::now();
                if ($user->userLog->user_status == 0) $user->userLog->user_status = 1;
                $user->userLog->save();
            } else {
                $user->userLog()->save(new UserLog([
                    'last_login' => Carbon::now(),
                    'login_status' => 1
                ]));
            }
            Log::info("Previous URL Handle: " . Session::get('redirect_url'));
            if (Session::has('redirect_url')) {
                $url = Session::get('redirect_url');
                Session::forget('redirect_url');
                return Redirect::to($url);
            } else return Redirect::to('/');
        } else {
            if ($throttles && !$lockedOut) {
                $this->incrementLoginAttempts($request);
            }
            return Redirect::action('UserController@login')->with('error', 'Invalid user name or password');
        }
    }

    function logout(Request $request)
    {
        Auth::logout();
        return Redirect::action('UserController@login');

    }

    function login()
    {
        //Log::info("Previous URL: ".Session::get('redirect_url'));
        //Session::put("redirect_url",URL::previous());
        if (Auth::check()) return Redirect::to('/');
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
        return View::make('User.user_registration')->with('types', $types);
    }

    function userManagement()
    {
        $users = User::count();
        return View::make('User.user_management')->with('total_user', $users);
    }

    function handleRegister()
    {

        $messages = [
            'user_name' => 'user name required',
            'password.required' => 'password required',
            'r_password.required' => 'confirm password required',
            'r_password.same' => 'confirm password must be same as password',
            'user_type' => 'select a user type',
        ];
        if (Input::get('user_type') == 22) {
            $rules = array(
                'user_name' => 'required|unique:hrm.tbl_user',
                'password' => 'required|min:6',
                'r_password' => 'required|same:password',
                'user_type' => 'required',
                'district_name' => 'required'
            );
        } else if (Input::get('user_type') == 66) {
            $rules = array(
                'user_name' => 'required|unique:hrm.tbl_user',
                'password' => 'required|min:6',
                'r_password' => 'required|same:password',
                'user_type' => 'required',
                'division_name' => 'required'
            );
        } else {
            $rules = array(
                'user_name' => 'required|unique:hrm.tbl_user',
                'password' => 'required|min:6',
                'r_password' => 'required|same:password',
                'user_type' => 'required'
            );
        }
        $validation = Validator::make(Input::all(), $rules, $messages);
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
            CustomQuery::addActionlog(['ansar_id' => $user->id, 'action_type' => 'CREATE USER', 'from_state' => '', 'to_state' => '', 'action_by' => auth()->user()->id]);
            return Redirect::action('UserController@userManagement')->with('success_message', 'New user created successfully');
        } else {
//            return Response::json($validation->errors());
            return Redirect::action('UserController@userRegistration')->withInput(Input::except(array('password', 'r_password')))->withErrors($validation);
        }
    }

    function viewProfile($id)
    {
        return View::make('User.user_profile')->with('user', User::find($id));
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
        return View::make('User.edit_user')->with('id', $id);
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
            'c_password' => 'required|same:password'
        ];
        $messages = [
            'password.required' => 'New password is required',
            'c_password.required' => 'Confirm password is required',
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
        if ($user->save()) {
            CustomQuery::addActionlog(['ansar_id' => $id, 'action_type' => 'BLOCK USER', 'from_state' => 'BLOCK', 'to_state' => 'UNBLOCK', 'action_by' => auth()->user()->id]);
            return Response::json(['status' => true]);
        } else return Response::json(['status' => false]);
    }

    function unBlockUser()
    {
        $id = Input::get('user_id');
        $user = User::find($id);
        $user->status = 1;
        if ($user->save()) {
            CustomQuery::addActionlog(['ansar_id' => $id, 'action_type' => 'UNBLOCK USER', 'from_state' => 'UNBLOCK', 'to_state' => 'BLOCK', 'action_by' => auth()->user()->id]);
            return Response::json(['status' => true]);
        } else return Response::json(['status' => false]);
    }

    function editUserPermission($id)
    {
        $read_permission_file = file_get_contents(storage_path("user/permission/test_list.json"));
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
//        return Res;
        return View::make('User.user_permission_view')->with(array('routes' => collect($routes), 'id' => $id, 'access' => $permission, 'user' => User::find($id)));
    }

    function updatePermission($id)
    {
//        return Input::get('permission');
        $user = User::find($id);
        $all = Input::get('permit_all');
        $permission = count(Input::get('permission')) == 0 ? null : json_encode(Input::get('permission'));
        $user->userPermission->permission_type = 0;
        $user->userPermission->permission_list = $permission;
        $user->userPermission->save();
        CustomQuery::addActionlog(['ansar_id' => $id, 'action_type' => 'EDIT USER PERMISSION', 'from_state' => '', 'to_state' => '', 'action_by' => auth()->user()->id]);
        return Redirect::action('UserController@userManagement')->with('success_message', $user->user_name . " permission has been updated successfully");
    }

    function getAllUser()
    {
        return response()->json(CustomQuery::getUserInformation(Input::get('limit'), Input::get('offset'), Input::get('user_name')));
    }


    function verifyMemorandumId()
    {
        $rule = [
            'memorandum_id' => 'required|unique:hrm.tbl_memorandum_id'
        ];
        $v = Validator::make(Input::all(), $rule);
        return Response::json(array('status' => $v->fails()));
    }


    function changeUserImage(Request $request)
    {
        $valid = Validator::make($request->all(), [
            'image_file' => 'required'
        ]);
        if ($valid->fails()) {
            return Response::json(['status' => false, 'message' => 'Image file required']);
        }
        $file = Input::file('image_file');
        //return $file;
        $user_folder = storage_path('user/img');
        if (!File::exists($user_folder)) {
            File::makeDirectory($user_folder, 0777, true);
        }
//        if(is_null($file)) return ;
        DB::beginTransaction();
        try {
            $file_name = Auth::user()->id . "." . $file->getClientOriginalExtension();
            $path = $user_folder . '/' . $file_name;
            if (File::exists($path)) File::delete($path);
            $status = Image::make($file)->resize(200, 200)->save($path);
            if ($status) {
                $p = Auth::user()->userProfile;
                $p->profile_image = 'user/img/' . $file_name;
                $p->save();
                DB::commit();
                return Response::json(['status' => true, 'message' => 'Image upload complete']);
            } else return Response::json(['status' => false, 'message' => 'An error occur while uploading image. Try again later']);
        } catch (\Exception $e) {
            DB::rollBack();
            return Response::json(['status' => false, 'message' => 'An error occur while uploading image. Try again later']);
        }
    }


    public function userSearch()
    {
        DB::enableQueryLog();
        $username = Input::get('user_name');
//        return User::where('user_name',$username)->get();
        $users = DB::table('tbl_user')
            ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'tbl_user.id')
            ->leftJoin('tbl_user_log', 'tbl_user_log.user_id', '=', 'tbl_user.id')
            ->where('tbl_user.user_name', 'LIKE', "%$username%")
            ->select('tbl_user.id', 'tbl_user.user_name', 'tbl_user_details.first_name', 'tbl_user_details.last_name', 'tbl_user_details.email', 'tbl_user_log.last_login', 'tbl_user_log.user_status', 'tbl_user.status')
            ->get();
        return Response::json($users);
    }

    public function getImage()
    {
        $image = storage_path(Input::get('file'));
        if (!Input::exists('file')) return Image::make(public_path('dist/img/nimage.png'))->response();
        if (is_null($image) || !File::exists($image) || File::isDirectory($image)) {
            return Image::make(public_path('dist/img/nimage.png'))->response();
        }
        //return $image;
        return Image::make($image)->response();
    }

    public function getSingImage($id)
    {
        $image = storage_path(PersonalInfo::where('ansar_id', $id)->first()->sign_pic);
//        return File::exists($image)?"exists":"not exists";
        try {
            return Image::make($image)->response();
        } catch (\Exception $e) {
//            return $e->getMessage();
            return Image::make(storage_path('data/signature/no-signature.jpg'))->response();
        }
    }

    public function getThumbImage($id)
    {
        $image = storage_path(PersonalInfo::where('ansar_id', $id)->first()->thumb_pic);
        try {
            return Image::make($image)->response();
        } catch (\Exception $e) {
            return Image::make(storage_path('data/fingerprint/no-thumb.jpg'))->response();
        }
    }

    function forgetPasswordRequest()
    {
        return view('forget_password');
    }

    function handleForgetRequest(Request $request)
    {
        $rules = [
            'user_name' => 'required'
        ];
        $message = [
            'required' => 'User name can`t be empty'
        ];
        $valid = Validator::make($request->all(), $rules, $message);
        //return $valid->fails()?'true':'false';
        if ($valid->fails()) {
            return Redirect::back()->withInputs($request->accepts(['_token']))->withErrors($valid);
        } else {
            $user = User::where('user_name', $request->get('user_name'));
            if (!$user->exists()) {
                return Redirect::back()->withInputs($request->accepts(['_token']))->with('error', 'This user name does not exists');
            }
            try {
                $fpr = ForgetPasswordRequest::findOrFail($request->get('user_name'));
                return Redirect::back()->withInput($request->all())->with('error', 'This user name already has a pending password change request');
            } catch (\Exception $e) {
                $fpr = new ForgetPasswordRequest;
                $fpr->user_name = $request->get('user_name');
                $fpr->save();
            }

        }
        return Redirect::back()->with('success', 'Password change request sent successfully');
    }

    public function changeForgetPassword($user)
    {
        return view('User.change_password', ['user' => $user]);
    }

    public function handleChangeForgetPassword(Request $request)
    {
        $user = User::where('user_name', $request->get('user'))->first();
        $fpr = ForgetPasswordRequest::find($request->get('user'));
        if (!$user || !$fpr) {
            return Redirect::back()->with('error', 'User does not exists with this user name');
        }
        $rules = [
            'password' => 'required',
            'c_password' => 'required|same:password'
        ];
        $messages = [
            'password.required' => 'New password is required',
            'c_password.required' => 'Confirm password is required',
            'same' => 'Password mis-match'
        ];
        $valid = Validator::make($request->all(), $rules, $messages);
        if ($valid->fails()) {
            return Redirect::back()->withErrors($valid);
        } else {
            $user->password = Hash::make(Input::get('password'));
            if ($user->save()) {
                $fpr->delete();
                return Redirect::back()->with('success', 'Password change successfully');
            } else {
                return Redirect::back()->with('error', 'An error occur while password changing. Please try again later');
            }
        }
    }

    public function removePasswordRequest($user)
    {
        try {
            $frp = ForgetPasswordRequest::findOrFail($user);
            $frp->delete();
            return Redirect::back()->with('success', $user . ' request remove successfully');
        } catch (\Exception $e) {
            return Redirect::back()->with('error', $user . ' does not exists');
        }
    }

    public function viewActionLog($id = null)
    {
        $form_date = Carbon::now()->toDateString();
        $to_date = Carbon::parse($form_date)->subHours(48)->toDateString();
        if ($id) {
            $user = User::find($id);
            $data = $user->actionLog()->whereDate('created_at', '>', $to_date)->whereDate('created_at', '<=', $form_date)->select('ansar_id', 'from_state', 'to_state', 'action_type', DB::raw('DATE_FORMAT(created_at,"%d %b. %Y") as date'), DB::raw('DATE_FORMAT(created_at,"%r") as time'))->orderBy('created_at', 'desc')->get();
        } else {
            $user = Auth::user();
            $data = $user->actionLog()->whereDate('created_at', '>', $to_date)->whereDate('created_at', '<=', $form_date)->select('ansar_id', 'from_state', 'to_state', 'action_type', DB::raw('DATE_FORMAT(created_at,"%d %b. %Y") as date'), DB::raw('DATE_FORMAT(created_at,"%r") as time'))->orderBy('created_at', 'desc')->get();
        }

        return View::make('User.user_activity_log', ['logs' => collect($data)->groupBy('date'), 'user' => $user]);
    }

    public function getUserData()
    {
        $v = Cache::remember('user_data', 10, function () {
            $user = Auth::user();

            $kpis = $user->kpi;
            $d = [];
            foreach ($kpis as $kpi) {
                $e = $kpi->embodiment->pluck('ansar_id')->toArray();
                $d = array_merge($d, $e);
            }
            $v = User::with(['district', 'division', 'usertype', 'userPermission'])->find($user->id);
            $v['embodiment'] = $d;
            return $v;
        });
        return $v;
    }

} 


