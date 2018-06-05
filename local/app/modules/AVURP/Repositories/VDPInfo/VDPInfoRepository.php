<?php
/**
 * Created by PhpStorm.
 * User: shuvo
 * Date: 4/3/2018
 * Time: 1:31 PM
 */

namespace App\modules\AVURP\Repositories\VDPInfo;


use App\Http\Requests\Request;
use App\modules\AVURP\Models\UserActionLog;
use App\modules\AVURP\Models\VDPAnsarInfo;
use App\modules\AVURP\Requests\VDPInfoRequest;
use App\modules\HRM\Models\District;
use App\modules\HRM\Models\Division;
use App\modules\HRM\Models\Thana;
use App\modules\HRM\Models\Unions;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Intervention\Image\Facades\Image;

class VDPInfoRepository implements VDPInfoInterface
{
    public $info;

    /**
     * VDPInfoRepository constructor.
     * @param VDPAnsarInfo $info
     */
    public function __construct(VDPAnsarInfo $info)
    {
        $this->info = $info;
    }


    /**
     * @param VDPInfoRequest $request
     * @param string $user_id
     * @return mixed
     */
    public function create($request, $user_id = '')
    {
        DB::connection('avurp')->beginTransaction();
        try {

            $count = $this->info->where($request->only(['division_id', 'thana_id', 'unit_id', 'union_id', 'union_word_id', 'gender']))->count();
            if ($count >= 32) {
                throw new \Exception("32 {$request->gender} already register in this ward");
            }

            $division_code = sprintf("%02d", Division::find($request->division_id)->division_code);
            $unit_code = sprintf("%02d", District::find($request->unit_id)->unit_code);
            $thana_code = sprintf("%02d", Thana::find($request->thana_id)->thana_code);
            $union_code = sprintf("%02d", Unions::find($request->union_id)->code);
            $gender_code = $request->gender == 'Male' ? 1 : 2;
            $word_code = '0' . $request->union_word_id;
            $count += ($request->gender == 'Male' ? 1 : 33);
            $count = sprintf("%02d", $count);
            $geo_id = $division_code . $unit_code . $thana_code . $union_code . $gender_code . $word_code . $count;
            if ($request->hasFile('profile_pic') && !$request->is('AVURP/api/*')) {
                $file = $request->file('profile_pic');
                $path = storage_path('avurp/profile_pic');
                if (!File::exists($path)) File::makeDirectory($path, 777, true);
                $image_name = $geo_id . '.' . $file->clientExtension();
                Image::make($file)->save($path . '/' . $image_name);
            } else if ($request->profile_pic && $request->is('AVURP/api/*')) {
                $path = storage_path('avurp/profile_pic');
                $image = Image::make($request->profile_pic);;
                $extension = 'png';
                $mime = $image->mime();
                if ($mime == 'image/jpeg')
                    $extension = 'jpg';
                elseif ($mime == 'image/png')
                    $extension = 'png';
                elseif ($mime == 'image/gif')
                    $extension = 'gif';
                if (!File::exists($path)) File::makeDirectory($path, 777, true);
                $image_name = $geo_id . '.' . $extension;
                $image->save($path . '/' . $image_name);
            }
            $data = $request->except(['educationInfo', 'training_info', 'status','import_file']);
            $data['geo_id'] = $geo_id;
            if (isset($path) && isset($image_name)) $data['profile_pic'] = $image_name;
            else  $data['profile_pic'] = '';
            $info = $this->info->create($data);
            $info->status()->create([]);
            if ($request->educationInfo) {
                foreach ($request->educationInfo as $education) {
                    $info->education()->create($education);
                }
            }
            if ($request->training_info) {
                foreach ($request->training_info as $training) {
                    $info->training_info()->create($training);
                }
            }


            $user = auth()->user();
            $now = Carbon::now()->format('d-M-Y h:i:s A');
            UserActionLog::create([
                'action_user_id' => $user->id,
                'action_description' => "VDP ID({$geo_id}) has been created by {$user->user_name} at {$now}",
                'action_type'=>'Entry',
                'action_id'=>$info->id,
            ]);
            DB::connection('avurp')->commit();
        } catch (\Exception $e) {
            DB::connection('avurp')->rollback();
            if (isset($path) && isset($image_name)) {
                if (File::exists($path . '/' . $image_name)) {
                    File::delete($path . '/' . $image_name);
                }
            }
            Log::info($e->getTraceAsString());
            return ['data' => ['message' => $e->getMessage()], 'status' => false];
        }
        return ['data' => ['message' => "data created successfully"], 'status' => true];
    }

    /**
     * @param $id
     * @param string $user_id
     * @return mixed
     */
    public function getInfo($id, $user_id = '')
    {
        $info = $this->info->with(['division', 'unit', 'thana', 'union', 'education', 'education.education', 'bloodGroup', 'training_info'])->where('id', $id)->userQuery($user_id);
        return $info->first();
    }

    /**
     * @param array $param
     * @param int $paginate
     * @param string $user_id
     * @param bool $is_api
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Collection|static[]
     */
    public function getInfos($param = [], $paginate = 30, $user_id = '',$is_api=false)
    {
        $range = isset($param['range']) && $param['range'] ? $param['range'] : 'all';
        $unit = isset($param['unit']) && $param['unit'] ? $param['unit'] : 'all';
        $thana = isset($param['thana']) && $param['thana'] ? $param['thana'] : 'all';
        if($is_api){
            $vdp_infos = $this->info;
            $vdp_infos = $vdp_infos->select('ansar_name_bng','geo_id','id','designation');
        } else{
            $vdp_infos = $this->info->with(['division', 'unit', 'thana', 'union']);
        }
        if ($range != 'all') {
            $vdp_infos = $vdp_infos->where('division_id', $range);
        }
        if ($unit != 'all') {
            $vdp_infos = $vdp_infos->where('unit_id', $unit);
        }
        if ($thana != 'all') {
            $vdp_infos = $vdp_infos->where('thana_id', $thana);
        }
        $vdp_infos = $vdp_infos->userQuery($user_id);
        if(isset($param['q']))$vdp_infos = $vdp_infos->searchQuery($param['q']);
        if ($paginate > 0) {
            return $vdp_infos->paginate($paginate);
        }
        return $vdp_infos->get();
    }

    /**
     * @param Request $request
     * @param $id
     * @param string $user_id
     * @return mixed
     * @internal param Request $input
     */
    public function update($request, $id, $user_id = '')
    {
        DB::connection('avurp')->beginTransaction();
        try {
            $info = $this->info->findOrFail($id);
            $division_code = sprintf("%02d", Division::find($request->division_id)->division_code);
            $unit_code = sprintf("%02d", District::find($request->unit_id)->unit_code);
            $thana_code = sprintf("%02d", Thana::find($request->thana_id)->thana_code);
            $union_code = sprintf("%02d", Unions::find($request->union_id)->code);
            $gender_code = $request->gender == 'Male' ? 1 : 2;
            $word_code = '0' . $request->union_word_id;
            $geo_id = $division_code . $unit_code . $thana_code . $union_code . $gender_code . $word_code;
            $e_geo_id = substr($info->geo_id, 0, 11);
            if ($geo_id == $e_geo_id) {
                $geo_id = $info->geo_id;
            } else {
                $count = $this->info->where($request->only(['division_id', 'thana_id', 'unit_id', 'union_id', 'union_word_id', 'gender']))->count();

                if ($count >= 32) {
                    throw new \Exception("32 {
                $request->gender} already register in this ward");
                }
                $count += ($request->gender == 'Male' ? 1 : 33);
                $count = sprintf("%02d", $count);
                $geo_id .= $count;
            }

            if ($request->hasFile('profile_pic')) {
                $file = $request->file('profile_pic');
                $path = storage_path('avurp/profile_pic');
                if (!File::exists($path)) File::makeDirectory($path, 777, true);
                $image_name = $geo_id . '.' . $file->clientExtension();
                Image::make($file)->save($path . '/' . $image_name);
            }
            $data = $request->except(['training_info','educationInfo','status','action_user_id']);
            $data['geo_id'] = $geo_id;
            if (isset($path) && isset($image_name)) $data['profile_pic'] = $path . '/' . $image_name;
            else if ($request->hasFile('profile_pic')) $data['profile_pic'] = '';

            $info->update($data);
            $info->education()->delete();
            $info->training_info()->delete();
            if ($request->educationInfo) {
                foreach ($request->educationInfo as $education) {
                    $info->education()->create($education);
                }
            }

            foreach ($request->training_info as $training) {
                $info->training_info()->create($training);
            }
            $user = auth()->user();
            $now = Carbon::now()->format('d-M-Y h:i:s A');
            UserActionLog::create([
                'action_user_id' => $user->id,
                'action_description' => "VDP ID({$geo_id}) has been updated by {$user->user_name} at {$now}",
                'action_type'=>'Edit',
                'action_id'=>$info->id,
            ]);
            DB::connection('avurp')->commit();
        } catch (\Exception $e) {
            DB::connection('avurp')->rollback();
            if (isset($path) && isset($image_name)) {
                if (File::exists($path . '/' . $image_name)) {
                    File::delete($path . '/' . $image_name);
                }
            }
            Log::info($e->getTraceAsString());
            return ['data' => ['message' => $e->getMessage()], 'status' => false];
        }
        return ['data' => ['message' => "data updated successfully"], 'status' => true];
    }

    /**
     * @param $id
     * @param string $user_id
     * @return mixed
     */
    public function getInfoForEdit($id, $user_id = '')
    {
        $info = $this->info->with(['education', 'training_info', 'training_info.main_training.subTraining'])->where('id',$id)->userQuery($user_id);
        return $info->first();
    }

    /**
     * @param id $
     * @return mixed
     */
    public function verifyVDP($id)
    {
        $type = auth()->user()->usertype->type_code;
        if($type==44||$type==22||$type==66||$type==11){
            DB::connection('avurp')->beginTransaction();
            try{
                $info = $this->info->findOrFail($id);
                if($info->status!='new') throw new \Exception("He/She is already {
                $info->status}");
                $info->update(['status'=>'verified']);
                $user = auth()->user();
                $now = Carbon::now()->format('d-M-Y h:i:s A');
                UserActionLog::create([
                    'action_user_id' => $user->id,
                    'action_description' => "VDP ID({$info->geo_id}) has been verified by {$user->user_name} at {$now}",
                    'action_type'=>'Verify',
                    'action_id'=>$info->id,
                ]);
                DB::connection('avurp')->commit();
            }catch(\Exception $e){
                DB::connection('avurp')->rollback();
                return ['data' => ['message' => $e->getMessage()], 'status' => false];
            }
            return ['data' => ['message' => "VDP verified successfully"], 'status' => true];
        }
        return ['data' => ['message' => "You don`t have access to perform this action"], 'status' => false];
    }

    public function approveVDP($id)
    {
        $type = auth()->user()->usertype->type_code;
        if($type==22||$type==66||$type==11){
            DB::connection('avurp')->beginTransaction();
            try{
                $info = $this->info->findOrFail($id);
                if($info->status!='verified') throw new \Exception("His / Her status is  {
                $info->status}");
                $info->update(['status'=>'approved']);
                $user = auth()->user();
                $now = Carbon::now()->format('d-M-Y h:i:s A');
                UserActionLog::create([
                    'action_user_id' => $user->id,
                    'action_description' => "VDP ID({$info->geo_id}) has been approve by {$user->user_name} at {$now}",
                    'action_type'=>'Approve',
                    'action_id'=>$info->id,
                ]);
                DB::connection('avurp')->commit();
            }catch(\Exception $e){
                DB::connection('avurp')->rollback();
                return ['data' => ['message' => $e->getMessage()], 'status' => false];
            }
            return ['data' => ['message' => "VDP approved successfully"], 'status' => true];
        }
        return ['data' => ['message' => "You don`t have access to perform this action"], 'status' => false];
    }
    public function verifyAndApproveVDP($id)
    {
        $type = auth()->user()->usertype->type_code;
        if($type==22||$type==66||$type==11){
            DB::connection('avurp')->beginTransaction();
            try{
                $info = $this->info->findOrFail($id);
                if($info->status!='new') throw new \Exception("He/She is already {$info->status}");
                $info->update(['status'=>'approved']);
                DB::connection('avurp')->commit();
            }catch(\Exception $e){
                DB::connection('avurp')->rollback();
                return ['data' => ['message' => $e->getMessage()], 'status' => false];
            }
            return ['data' => ['message' => "VDP approved successfully"], 'status' => true];
        }
        return ['data' => ['message' => "You don`t have access to perform this action"], 'status' => false];
    }
}