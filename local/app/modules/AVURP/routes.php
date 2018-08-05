<?php
Route::group(['prefix'=>'AVURP','namespace'=>'\App\modules\AVURP\Controllers','middleware'=>['auth','permission','manageDatabase','checkUserType']],function(){
    Route::get('/',['as'=>'AVURP','uses'=>'MainController@index']);
    Route::post('info/verify/{id}',['as'=>'AVURP.info.verify','uses'=>'AnsarVDPInfoController@verifyVDP']);
    Route::post('info/approve/{id}',['as'=>'AVURP.info.approve','uses'=>'AnsarVDPInfoController@approveVDP']);
    Route::post('info/verify_approve/{id}',['as'=>'AVURP.info.verify_approve','uses'=>'AnsarVDPInfoController@verifyAndApproveVDP']);
    Route::get('info/image/{id}',['as'=>'AVURP.info.image','uses'=>'AnsarVDPInfoController@loadImage']);
    Route::get('info/import',['as'=>'AVURP.info.import','uses'=>'AnsarVDPInfoController@import']);
    Route::get('info/import/download/{file_name}',['as'=>'AVURP.info.import.download','uses'=>'AnsarVDPInfoController@downloadFile']);
    Route::post('info/import',['as'=>'AVURP.info.import_upload','uses'=>'AnsarVDPInfoController@processImportedFile']);
    Route::get('info/export',['as'=>'AVURP.info.export','uses'=>'AnsarVDPInfoController@export']);
    Route::resource('info','AnsarVDPInfoController');
    Route::get('kpi/kpi_name','KpiInfoController@kpiList');
    Route::resource('kpi','KpiInfoController');
    Route::post("offer_info/select_all",['as'=>'AVURP.offer_info.select_all','uses'=>'OfferInfoController@selectAll']);
    Route::resource('offer_info','OfferInfoController');
    Route::post("embodiment/select_all",['as'=>'AVURP.embodiment.select_all','uses'=>'EmbodimentController@selectAll']);
    Route::resource('embodiment','EmbodimentController',[
        "only"=>['index','store']
    ]);
    Route::get("test", function () {
        $data = collect(\Maatwebsite\Excel\Facades\Excel::load('storage/need_to_correct.xlsx',function(){

        })->get())->toArray();
        $datas = [];
        foreach ($data as $sheet){
//            $header = $sheet[0];
            unset($sheet[0]);
            foreach ($sheet as $row){
//                $d = array_combine($header,$row);
                array_push($datas,$row);
            }
        }
        $c = 0;
        foreach ($datas as $d){
            $geo_id = $d[0];
            $account_no = $d[9];
            $bank_name = $d[10];
            $vdp = \App\modules\AVURP\Models\VDPAnsarInfo::where('geo_id',$geo_id)->first();
            if(!$vdp) continue;
            $b = $vdp->account;
            $ud = [];
            if($bank_name=="rocket"||$bank_name=="bkash"){
                $ud["mobile_bank_type"] = $bank_name;
                $ud["mobile_bank_account_no"] = \App\Helper\Facades\LanguageConverterFacades::bngToEng($account_no);
                $ud["prefer_choice"] = "mobile";
                $ud["bank_name"] = "";
                $ud["branch_name"] = "";
                $ud["account_no"] = "";
            } else{
                $ud["mobile_bank_type"] = null;
                $ud["mobile_bank_account_no"] = null;
                $ud["prefer_choice"] = "general";
                $ud["bank_name"] = $bank_name;
                $ud["branch_name"] = "";
                $ud["account_no"] = $account_no;
            }
            if(!$b) {
                $ud["vdp_id"] = $vdp->id;
                $vdp->account()->create($ud);
            }
            else $b->update($ud);
            $c++;
        }
        return "data updated ".$c;

    });
    Route::get("test1", function () {
        $data = collect(\Maatwebsite\Excel\Facades\Excel::load('storage/ansar_corrected.xlsx',function(){

        })->get())->toArray();
        $datas = [];
        foreach ($data as $sheet){
//            $header = $sheet[0];
            unset($sheet[0]);
            foreach ($sheet as $row){
//                $d = array_combine($header,$row);
                array_push($datas,$row);
            }
        }
        $c = 0;
        foreach ($datas as $d){
            $geo_id = $d[0];
            $account_no = $d[9];
            $bank_name = $d[10];
            $vdp = \App\modules\AVURP\Models\VDPAnsarInfo::where('geo_id',$geo_id)->first();
            if(!$vdp) continue;
            $b = $vdp->account;
            $ud = [];
            if($bank_name=="rocket"||$bank_name=="bkash"){
                $ud["mobile_bank_type"] = $bank_name;
                $ud["mobile_bank_account_no"] = \App\Helper\Facades\LanguageConverterFacades::bngToEng($account_no);
                $ud["prefer_choice"] = "mobile";
                $ud["bank_name"] = "";
                $ud["branch_name"] = "";
                $ud["account_no"] = "";
            } else{
                $ud["mobile_bank_type"] = null;
                $ud["mobile_bank_account_no"] = null;
                $ud["prefer_choice"] = "general";
                $ud["bank_name"] = $bank_name;
                $ud["branch_name"] = "";
                $ud["account_no"] = $account_no;
            }
            if(!$b) {
                $ud["vdp_id"] = $vdp->id;
                $vdp->account()->create($ud);
            }
            else $b->update($ud);
            $c++;
        }
        return "data updated ".$c;

    });
    Route::get("test2", function () {
        $data = collect(\Maatwebsite\Excel\Facades\Excel::load('storage/not_found_account.xlsx',function(){

        })->get())->toArray();
        $datas = [];
        foreach ($data as $sheet){
//            $header = $sheet[0];
            unset($sheet[0]);
            foreach ($sheet as $row){
//                $d = array_combine($header,$row);
                array_push($datas,$row);
            }
        }
        $c = 0;
        foreach ($datas as $d){
            $geo_id = $d[2];
            $account_no = $d[4];
            $vdp = \App\modules\AVURP\Models\VDPAnsarInfo::where('smart_card_id',$geo_id)->first();
            if(!$vdp) continue;
            $b = $vdp->account;
            $ud = [];
            $ud["mobile_bank_type"] = null;
            $ud["mobile_bank_account_no"] = null;
            $ud["prefer_choice"] = "general";
            $ud["bank_name"] = "ডাচ্ বাংলা ব্যাংক";
            $ud["branch_name"] = "";
            $ud["account_no"] = $account_no;
            if(!$b) {
                $ud["vdp_id"] = $vdp->id;
                $vdp->account()->create($ud);
            }
            else $b->update($ud);
            $c++;
        }
        return "data updated ".$c;

    });
    Route::get("update_id", function () {
        $vdps = \App\modules\AVURP\Models\VDPAnsarInfo::all();
//        return $vdps;
        $ids = [];
        foreach ($vdps as $vdp){
            if(strlen($vdp->geo_id)==13){
                \Illuminate\Support\Facades\Log::info("previous id : ".$vdp->geo_id);
                $gid = substr($vdp->geo_id,0,11);
                $c = substr($vdp->geo_id,11,2);
//                array_push($ids,compact('gid','c'));
                $c = '5010'.$c;
                $gid.=$c;
                \Illuminate\Support\Facades\Log::info("new id : ".$gid);
                $vdp->geo_id = $gid;
                $vdp->save();

            }
        }
        return $ids;
    });
});