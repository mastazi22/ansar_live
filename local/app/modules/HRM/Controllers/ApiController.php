<?php

namespace App\modules\HRM\Controllers;

use App\modules\AVURP\Repositories\VDPInfo\VDPInfoInterface;
use App\modules\AVURP\Requests\VDPInfoRequest;
use App\modules\HRM\Models\MainTrainingInfo;
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
        $divisions = collect($this->dataRepo->getDivisions($request->id))
            ->pluck('id', 'division_name_bng')
            ->prepend('', 'বিভাগ নির্বাচন করুন');
        return response()->json($divisions);
    }

    public function unit(Request $request)
    {
        $units = collect($this->dataRepo->getUnits($request->range_id, $request->id))
            ->pluck('id', 'unit_name_bng')
            ->prepend('', 'জেলা নির্বাচন করুন');
        return response()->json($units);
    }

    public function thana(Request $request)
    {
        $thanas = collect($this->dataRepo->getThanas($request->range_id, $request->unit_id, $request->id))
            ->pluck('id', 'thana_name_bng')
            ->prepend('', 'থানা নির্বাচন করুন');
        return response()->json($thanas);
    }

    public function union(Request $request)
    {
        $units = collect($this->dataRepo->getUnions($request->range_id, $request->unit_id, $request->thana_id, $request->id))
            ->pluck('id', 'union_name_bng')
            ->prepend('', 'ইউনিয়ন নির্বাচন করুন');
        return response()->json($this->dataRepo->getUnions($request->range_id, $request->unit_id, $request->thana_id, $request->id));

    }
    public function main_training()
    {
        $data = MainTrainingInfo::all();
        return response()->json($data);

    }
    public function sub_training(Request $request)
    {

        if(!$request->has('id')) $data = [];
        else {
            $data = MainTrainingInfo::find($request->id);
            if($data) $data = $data->subTraining;
            else $data = [];
        }
        return response()->json($data);

    }
}