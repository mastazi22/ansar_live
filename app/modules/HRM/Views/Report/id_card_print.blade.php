
<h3 style="margin-top: 0;"><a href="#" id="print-report"><i class="glyphicon glyphicon-print"></i></a></h3>
<div id="ansar_id_card">
    <div id="ansar-id-card-front">

        <div class="card-header">
            <div class="card-header-left-part">
                <img src="{{asset('dist/img/ansar-vdp.png')}}" class="img-responsive">
            </div>
            <div class="card-header-right-part">
                <h4>{{$rd['title']}}</h4>
                <h5>{{$rd['id_no']}} : {{strcasecmp($type,'bng')==0?LanguageConverter::engToBng($ad->division_code.$ad->unit_code.$ad->ansar_id):$ad->division_code.$ad->unit_code.$ad->ansar_id}}</h5>
            </div>
        </div>
        <div class="card-body">
            <img src="data:image/png;base64,{{DNS2D::getBarcodePNG($ad->division_code.$ad->unit_code.$ad->ansar_id,'QRCODE')}}" style="width: 50px;height: 50px;position: absolute;z-index: 3000;left: 58%;top: 49%">
            <div class="card-body-left">
                <ul>
                    <li>{{$rd['name']}}<span class="pull-right">:</span></li>
                    <li>{{$rd['rank']}}<span class="pull-right">:</span></li>
                    <li>{{$rd['bg']}}<span class="pull-right">:</span></li>
                    <li>{{$rd['unit']}}<span class="pull-right">:</span></li>
                    <li>{{$rd['id']}}<span class="pull-right">:</span></li>
                    <li>{{$rd['ed']}}<span class="pull-right">:</span></li>
                </ul>
            </div>
            <div class="card-body-middle">
                <ul>
                    <li>{{$ad->name}}</li>
                    <li>{{$ad->rank}}</li>
                    <li>{{$ad->blood_group}}</li>
                    <li>{{$ad->unit_name}}</li>
                    <li>{{strcasecmp($type,'bng')==0?LanguageConverter::engToBng($id):$id}}</li>
                    <li>{{strcasecmp($type,'bng')==0?LanguageConverter::engToBng($ed):$ed}}</li>
                </ul>
            </div>
            <div class="card-body-right">
                <img src="{{action('UserController@getImage',['file'=>$ad->profile_pic])}}" class="img-responsive" style="">
            </div>
        </div>
        <div class="card-footer">
            <div class="card-footer-sing">
                <div>sfsfs</div>
                <div>{{$rd['bs']}}</div>
            </div>
            <div class="card-footer-barcode">
                <img src="data:image/png;base64,{{DNS1D::getBarcodePNG($ad->division_code.$ad->unit_code.$ad->ansar_id,'C128')}}" style="max-width: 100%">
            </div>
            <div class="card-footer-sing">
                <div>gfghfh</div>
                <div>{{$rd['is']}}</div>
            </div>
        </div>
        <h5 style="text-align: center;margin-top: 0;margin-bottom: 5px">{{$rd['footer_title']}}</h5>
    </div>
</div>
<div class="ansar_history" style="margin-top: 20px">
    <div class="table-responsive">
        <table class="table table-bordered">
            <tr>
                <th>SL.No</th>
                <th>Print Type</th>
                <th>Issue Date</th>
                <th>Expire Date</td>
                <th>Status</th>
            </tr>
            <?php $i=1; ?>
            @forelse($history as $h)
                <tr>
                    <td>{{$i++}}</td>
                    <td>{{strcasecmp($h->type,"eng")==0?"English":"Bangla"}}</td>
                    <td>{{\Carbon\Carbon::createFromFormat("Y-m-d",$h->issue_date)->format("d-M-Y")}}</td>
                    <td>{{\Carbon\Carbon::createFromFormat("Y-m-d",$h->expire_date)->format("d-M-Y")}}</td>
                    <td>{{$h->status?"Active":"Blocked"}}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5">No history found</td>
                </tr>
            @endforelse
        </table>
    </div>
</div>