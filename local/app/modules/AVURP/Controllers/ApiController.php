<?php

namespace App\modules\AVURP\Controllers;

use App\modules\AVURP\Repositories\VDPInfo\VDPInfoInterface;
use App\modules\AVURP\Requests\VDPInfoRequest;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

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
        return response()->json($this->infoRepository->getInfos($request->all(), $limit));
    }

    public function show($id)
    {
        return response()->json($this->infoRepository->getInfo($id));
    }

    public function edit($id)
    {
        return response()->json($this->infoRepository->getInfoForEdit($id));
    }

    public function store(VDPInfoRequest $request)
    {
        $response = $this->infoRepository->create($request);
        return response()->json($response);

    }
    public function update(VDPInfoRequest $request,$id)
    {
        $response = $this->infoRepository->update($request,$id);
        return response()->json($response);

    }
}
