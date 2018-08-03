<?php

namespace App\modules\SD\Controllers;

use App\modules\SD\Models\CashDeposite;
use App\modules\SD\Models\DemandLog;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Intervention\Image\Facades\Image;

class KPIPaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if($request->ajax()){
            $limit = $request->limit?$request->limit:30;
            $payment_history = CashDeposite::with(['demand','kpi'])
            ->whereHas('kpi',function($q) use($request){
                if($request->range&&$request->range!='all'){
                    $q->where('division_id',$request->range);
                }
                if($request->unit&&$request->unit!='all'){
                    $q->where('unit_id',$request->unit);
                }
                if($request->thana&&$request->thana!='all'){
                    $q->where('thana_id',$request->thana);
                }
                if($request->kpi&&$request->kpi!='all'){
                    $q->where('id',$request->kpi);
                }
            })->whereHas('demand',function($q) use($request){
                    if($request->q){
                        $q->where('memorandum_no','LIKE',"%{$request->q}%");
                    }
                })->paginate();
            return view("SD::kpi_payment.data",compact('payment_history'));

        }
        return view("SD::kpi_payment.index");
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view("SD::kpi_payment.create");
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $rules = [
            "demand_id"=>'required|exists:sd.tbl_demand_log,id',
            "paid_amount"=>"required",
            "document"=>"required",
        ];
        $this->validate($request,$rules);
        DB::connection("sd")->beginTransaction();
        try{
            $demand_log = DemandLog::find($request->demand_id);
            $document = $request->file('document');
            $file_name = time().".".$document->clientExtension();
            $path = storage_path("bank_receipt");
            if(!File::exists($path)){
                File::makeDirectory($path,777,true);
            }
            $image = Image::make($document)->save($path.'/'.$file_name);
            $data = [
                'demand_id'=>$request->demand_id,
                'document'=>$file_name,
                'paid_amount'=>$request->paid_amount,
                'kpi_id'=>$demand_log->kpi->id,
                'action_user_id'=>$request->action_user_id,
            ];
            CashDeposite::create($data);
            DB::connection("sd")->commit();
        }catch(\Exception $e){
            DB::connection("sd")->rollback();
            return redirect()->route("SD.kpi_payment.index")->with('error_message',$e->getMessage());
        }
        return redirect()->route("SD.kpi_payment.index")->with('success_message',"Data saved successfully");
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
    public function showDoc($id)
    {
        $path = storage_path("bank_receipt");
        $cd = CashDeposite::find($id);
        $image = Image::make($path."/".$cd->document);
        return $image->response();
    }
}
