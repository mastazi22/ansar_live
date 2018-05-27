<?php

namespace App\modules\AVURP\Controllers;

use App\Http\Controllers\Controller;
use App\modules\AVURP\Models\VDPAnsarInfo;
use App\modules\AVURP\Repositories\VDPInfo\VDPInfoRepository;
use App\modules\AVURP\Requests\VDPInfoRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Session;
use Intervention\Image\Facades\Image;

class AnsarVDPInfoController extends Controller
{
    private $infoRepository;

    /**
     * AnsarVDPInfoController constructor.
     * @param VDPInfoRepository $infoRepository
     */
    public function __construct(VDPInfoRepository $infoRepository)
    {
        $this->infoRepository = $infoRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $limit = $request->limit ? $request->limit : 30;
            if(auth()->user()->usertype->type_name=="Dataentry") {
                $vdp_infos =  $this->infoRepository->getInfos($request->only(['range', 'unit', 'thana']), $limit,$request->action_user_id);
            }
            else $vdp_infos = $this->infoRepository->getInfos($request->only(['range', 'unit', 'thana']), $limit);
            return view('AVURP::ansar_vdp_info.data', compact('vdp_infos'));
        }
        return view('AVURP::ansar_vdp_info.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        return view('AVURP::ansar_vdp_info.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param VDPInfoRequest|Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(VDPInfoRequest $request)
    {
//        return $request->file('profile_pic');

        $response = $this->infoRepository->create($request);
        if (!$response['status']) {
            return response()->json($response['data'], 500);
        }
        Session::flash('success_message', 'New entry added successfully');
        return response()->json($response['data']);
    }

    /**
     * Display the specified resource.
     *
     * @param Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request,$id)
    {
        if(auth()->user()->usertype->type_name=="Dataentry") {
            $info = $this->infoRepository->getInfo($id,$request->action_user_id);
        }
        else $info = $this->infoRepository->getInfo($id);
        if($request->ajax()){
            return response()->json($info);
        }
        if(!$info) return abort(404);

        return view('AVURP::ansar_vdp_info.view', compact('info'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request,$id)
    {
        if(auth()->user()->usertype->type_name=="Dataentry") {
            $info = $this->infoRepository->getInfoForEdit($id,$request->action_user_id);
        }
        else $info = $this->infoRepository->getInfoForEdit($id);
        if($request->ajax()){
//            return $id;
            return response()->json($info);
        }
        if(!$info) return abort(404);
        return view('AVURP::ansar_vdp_info.edit', compact('id'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(VDPInfoRequest $request, $id)
    {

        $response = $this->infoRepository->update($request,$id);
        if (!$response['status']) {
            return response()->json($response['data'], 500);
        }
        Session::flash('success_message', 'data updated successfully');
        return response()->json($response['data']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
    public function verifyVDP($id)
    {
        $response = $this->infoRepository->verifyVDP($id);
        if (!$response['status']) {
            return redirect()->route('AVURP.info.index')->with('error_message',$response['data']['message']);
        }
        return redirect()->route('AVURP.info.index')->with('success_message',$response['data']['message']);
    }
    public function approveVDP($id)
    {
        $response = $this->infoRepository->approveVDP($id);
        if (!$response['status']) {
            return redirect()->route('AVURP.info.index')->with('error_message',$response['data']['message']);
        }
        return redirect()->route('AVURP.info.index')->with('success_message',$response['data']['message']);
    }
    public function verifyAndApproveVDP($id)
    {
        $response = $this->infoRepository->verifyAndApproveVDP($id);
        if (!$response['status']) {
            return redirect()->route('AVURP.info.index')->with('error_message',$response['data']['message']);
        }
        return redirect()->route('AVURP.info.index')->with('success_message',$response['data']['message']);
    }
    public function loadImage($id){
        $info  = VDPAnsarInfo::find($id);
        if($info&&$info->profile_pic){
            $image = storage_path('avurp/profile_pic').'/'.$info->profile_pic;
            if (!File::exists($image)||File::isDirectory($image)) $image = public_path('dist/img/nimage.png');
            //return $image;

        } else{
            $image = public_path('dist/img/nimage.png');
        }
        return Image::make($image)->response();
    }
    public function import(){
        return view('AVURP::ansar_vdp_info.import');
    }
    public function processImportedFile(Request $request){

    }
}
