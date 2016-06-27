<html>
<head>
    <link rel="stylesheet" href="{{asset('dist/css/id-card.css')}}">
    <style>
        @font-face {
            font-family: banglFont;
            src: url('{{asset('dist/fonts/SolaimanLipi.ttf')}}');
        }
    </style>
</head>
<body>
<div id="ansar-id-card-front" @if($type=='bng') style="font-family: banglFont" @endif>

    <div class="card-header">
        <div class="card-header-left-part">
            <img src="{{asset('dist/img/ansar-vdp.png')}}" class="img-responsive">
        </div>
        <div class="card-header-right-part">
            <h4>{{$rd['title']}}</h4>
            <h5>{{$rd['id_no']}}
                : {{strcasecmp($type,'bng')==0?LanguageConverter::engToBng($ad->division_code.$ad->unit_code.$ad->ansar_id):$ad->division_code.$ad->unit_code.$ad->ansar_id}}</h5>
        </div>
    </div>
    <div class="card-body">
        <img src="data:image/png;base64,{{DNS2D::getBarcodePNG($ad->division_code.$ad->unit_code.$ad->ansar_id,'QRCODE')}}"
             style="width: 50px;height: 50px;position: absolute;z-index: 3000;left: 58%;top: 49%">

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
            <img src="{{file_exists(storage_path($ad->profile_pic))?storage_path($ad->profile_pic) : (public_path('dist/img/nimage.png'))}}"
                 class="img-responsive" style="">
        </div>
    </div>
    <div class="card-footer">
        <div class="card-footer-sing">
            <div>sfsfs</div>
            <div>{{$rd['bs']}}</div>
        </div>
        <div class="card-footer-barcode">
            <img src="data:image/png;base64,{{DNS1D::getBarcodePNG($ad->division_code.$ad->unit_code.$ad->ansar_id,'C128')}}"
                 style="max-width: 100%">
        </div>
        <div class="card-footer-sing">
            <div>gfghfh</div>
            <div>{{$rd['is']}}</div>
        </div>
    </div>
    <h5 style="text-align: center;margin-top: 0;margin-bottom: 5px">{{$rd['footer_title']}}</h5>
</div>
<div>

</div>
</body>
</html>
