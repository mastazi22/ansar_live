<?php

namespace App\modules\AVURP\Controllers;

use App\modules\AVURP\Repositories\VDPInfo\VDPInfoInterface;
use App\modules\AVURP\Requests\VDPInfoRequest;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;

class ApiController extends Controller
{
    private $infoRepository;

    /**
     * ApiController constructor.
     * @param VDPInfoInterface $infoRepository
     */
    public function __construct(VDPInfoInterface $infoRepository)
    {
        $this->infoRepository = $infoRepository;
    }

    public function index(Request $request)
    {
        $limit = $request->limit?$request->limit:-1;
        return response()->json($this->infoRepository->getInfos($request->all(), $limit,$request->action_user_id,$request->is("AVURP/api/*")));
    }

    public function show(Request $request,$id)
    {
        $info = $this->infoRepository->getInfo($id,$request->action_user_id);
        if(!$info) return response()->json(['message'=>'Not found'],404);
        return response()->json($info);
    }

    public function edit(Request $request,$id)
    {
        $info = $this->infoRepository->getInfoForEdit($id,$request->action_user_id);
        if(!$info) return response()->json(['message'=>'Not found'],404);
        return response()->json();
    }

    public function store(VDPInfoRequest $request)
    {
//        Log::info($request->all());
//        return $request->all();

        $response = $this->infoRepository->create($request,$request->action_user_id);
        return response()->json($response);

    }
    public function update(VDPInfoRequest $request,$id)
    {
        $response = $this->infoRepository->update($request,$id,$request->action_user_id);
        return response()->json($response);

    }
}
