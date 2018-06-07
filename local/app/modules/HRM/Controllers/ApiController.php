<?php

namespace App\modules\HRM\Controllers;

use App\modules\AVURP\Repositories\VDPInfo\VDPInfoInterface;
use App\modules\AVURP\Requests\VDPInfoRequest;
use App\modules\HRM\Models\MainTrainingInfo;
use App\modules\HRM\Models\PersonalInfo;
use App\modules\HRM\Repositories\data\DataRepository;
use Barryvdh\Snappy\Facades\SnappyPdf;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use Intervention\Image\Facades\Image;

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
            ->map(function ($item, $key) {
                return ['id' => $item->id, 'name' => $item->division_name_bng];
            });
//            ->pluck('division_name_bng', 'id');
        $divisions = $divisions->prepend(['id' => '', 'name' => 'বিভাগ নির্বাচন করুন']);
        return response()->json($divisions);
    }

    public function unit(Request $request)
    {
        $units = collect($this->dataRepo->getUnits($request->range_id, $request->id))
            ->map(function ($item, $key) {
                return ['id' => $item->id, 'name' => $item->unit_name_bng];
            })
            ->prepend(['id' => '', 'name' => 'জেলা নির্বাচন করুন']);
        return response()->json($units);
    }

    public function thana(Request $request)
    {
        $thanas = collect($this->dataRepo->getThanas($request->range_id, $request->unit_id, $request->id))
            ->map(function ($item, $key) {
                return ['id' => $item->id, 'name' => $item->thana_name_bng];
            })
            ->prepend(['id' => '', 'name' => 'থানা নির্বাচন করুন']);
        return response()->json($thanas);
    }

    public function union(Request $request)
    {
        $unions = collect($this->dataRepo->getUnions($request->range_id, $request->unit_id, $request->thana_id, $request->id))
            ->map(function ($item, $key) {
                return ['id' => $item->id, 'name' => $item->union_name_bng];
            })
            ->prepend(['id' => '', 'name' => 'ইউনিয়ন নির্বাচন করুন']);
        return response()->json($unions);

    }

    public function main_training()
    {
        $data = MainTrainingInfo::all()
            ->map(function ($item, $key) {
                return ['id' => $item->id, 'name' => $item->training_name_bng];
            })
            ->prepend(['id' => '', 'name' => 'নির্বাচন করুন']);
        return response()->json($data);

    }

    public function bloodGroup()
    {
        $data = $this->dataRepo->getBloodGroup()
            ->map(function ($item, $key) {
                return ['id' => $item->id, 'name' => $item->blood_group_name_bng];
            })
            ->prepend(['id' => '', 'name' => 'রক্তের গ্রুপ নির্বাচন করুন']);
        return response()->json($data);

    }

    public function educationList()
    {
        $data = collect($this->dataRepo->getEducationList())
            ->map(function ($item, $key) {
                return ['id' => $item->id, 'name' => $item->education_deg_bng];
            })
            ->prepend(['id' => '', 'name' => 'নির্বাচন করুন']);
        return response()->json($data);

    }

    public function sub_training(Request $request)
    {

        if (!$request->has('id')) $data = [];
        else {
            $data = MainTrainingInfo::find($request->id);
            if ($data) {
                $data = collect($data->subTraining)
                    ->map(function ($item, $key) {
                        return ['id' => $item->id, 'name' => $item->training_name_bng];
                    })
                    ->prepend(['id' => '', 'name' => 'নির্বাচন করুন']);
            }
            else $data = [];
        }
        return response()->json($data);

    }
    public function loadProfileImage($id){
        $a = PersonalInfo::where('ansar_id',$id)->first();
        if($a){
            $path = storage_path($a->profile_pic);
            if(File::exists($path)){
                $image = Image::make($path);
                return ['data'=>$image->encode('data-url')];
            }
        }
        return response()->json(["message"=>"Not found"],400);
    }

    public function generateAndDownloadPDF(Request $request){
        $html  = $request->data;
        $pdf = SnappyPdf::loadHtml($html);
        $pdf->setOption('encoding', 'UTF-8');
        return $pdf->download();
    }
}
