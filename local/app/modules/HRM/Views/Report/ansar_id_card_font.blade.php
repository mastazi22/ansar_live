<html>
<head>
    <meta charset="utf-8">
    <link rel="stylesheet" href="{{asset('dist/css/id-card.css')}}">
    <script src="{{asset('dist/js/jquery-1.11.1.js')}}"></script>
    <script>
       $(document).ready(function () {
//           alert(1);
           function getTextWidth(text, font) {
               // re-use canvas object for better performance
               var canvas = getTextWidth.canvas || (getTextWidth.canvas = document.createElement("canvas"));
               var context = canvas.getContext("2d");
               context.font = font;
               var metrics = context.measureText(text);
               return metrics.width;
           }
           var t = $("#ansar_name");
           var fs = 12;
           var v = 0;
           var font = window.getComputedStyle(document.getElementById('ansar_name'),null).getPropertyValue('font-family');
           while((v=getTextWidth(t.text(),"normal "+fs+"px "+font))>=t.width()){
               fs-=1;
               console.log(fs);

           }
           alert(v+" "+t.width())
           t.css({fontSize:fs+"px"})
       })
    </script>
</head>
<body>
<div id="ansar-id-card-front" style="font-family: 'Times New Roman',sans-serif" >

    <div class="card-header">
        <div class="card-header-left-part">
            <img src="{{asset('dist/img/idlogo.png')}}" class="img-responsive">
        </div>
        <div class="card-header-right-part">
            <h4 style="@if($type=='bng') font-size: 1em @elseif($type=='eng') font-size:1em; @endif">{{$rd['title']}}</h4>
            <h5 style="font-size: 13px">{{$rd['id_no']}}
                : {{strcasecmp($type,'bng')==0?LanguageConverter::engToBng(GlobalParameter::generateSmartCard($ad->unit_code,$ad->ansar_id)):GlobalParameter::generateSmartCard($ad->unit_code,$ad->ansar_id)}}</h5>
        </div>
    </div>
    <div class="card-body">
        <img src="data:image/png;base64,{{DNS2D::getBarcodePNG(GlobalParameter::generateSmartCard($ad->unit_code,$ad->ansar_id),'QRCODE')}}"
             style="width: 50px;height: 50px;position: absolute;z-index: 3000;left: 58%;top: 44%">

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
                <li id="ansar_name">{{$ad->name}}</li>
                <li>{{$ad->rank}}</li>
                <li>{{$ad->blood_group}}</li>
                <li>{{$ad->unit_name}}</li>
                <li>{{strcasecmp($type,'bng')==0?LanguageConverter::engToBng($id):$id}}</li>
                <li>{{strcasecmp($type,'bng')==0?LanguageConverter::engToBng($ed):$ed}}</li>
            </ul>
        </div>
        <div class="card-body-right">
            <img src="{{URL::to('/image').'?file='.$ad->profile_pic}}"
                  style="width: 76px;height: 100px">
        </div>
    </div>
    <div class="card-footer" style="margin-top: 2px">
        <div class="card-footer-sing">
            <div><img src="{{URL::to('/image').'?file='.$ad->sign_pic}}"
                      style="width: 80px;height:30px"></div>
            <div>{{$rd['bs']}}</div>
        </div>
        <div class="card-footer-barcode">
            <img src="data:image/png;base64,{{DNS1D::getBarcodePNG(GlobalParameter::generateSmartCard($ad->unit_code,$ad->ansar_id),'C128')}}"
                 style="max-width: 100%">
        </div>
        <div class="card-footer-sing" style="float: right">
            <div><img src="{{URL::to('/image').'?file=data/authority/Signature.jpg'}}"
                      style="width: 80px;height:30px"></div>
            <div>{{$rd['is']}}</div>
        </div>
    </div>
    <h5 style="text-align: center;margin-top: 0;font-size: 12px;background: limegreen">{{$rd['footer_title']}}</h5>
</div>
<div>

</div>
</body>
</html>
