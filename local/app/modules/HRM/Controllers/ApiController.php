<?php

namespace App\modules\HRM\Controllers;

use App\modules\AVURP\Repositories\VDPInfo\VDPInfoInterface;
use App\modules\AVURP\Requests\VDPInfoRequest;
use App\modules\HRM\Repositories\data\DataRepository;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class ApiController extends Controller
{
    private $dataRepo;

    /**
     * ApiController constructor.
     * @param VDPInfoInterface $dataRepo
     */
    public function __construct(DataRepository $dataRepo)
    {
        $this->dataRepo = $dataRepo;
    }

    public function division(Request $request)
    {

        return response()->json($this->dataRepo->getDivisions($request->id));
    }

    public function unit(Request $request)
    {
        return response()->json($this->dataRepo->getUnits($request->range_id,$request->id));
    }

    public function thana(Request $request)
    {
        return response()->json($this->dataRepo->getThanas($request->range_id,$request->unit_id,$request->id));
    }

    public function union(Request $request)
    {
        return response()->json($this->dataRepo->getUnions($request->range_id,$request->unit_id,$request->thana_id,$request->id));

    }
}
