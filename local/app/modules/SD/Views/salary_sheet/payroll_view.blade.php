<html>
<head>
    <meta charset="UTF-8">
</head>
<body>
<style>
    @font-face {
        font-family: syamrupali;
        src: url('{{asset('dist/fonts/vrindab.ttf')}}');
    }

    * {
        font-family: syamrupali !important;
        line-height: 18px;
    }

    table td, table th {
        text-align: center;
        font-size: 12px !important;
        padding: 0 2px;
    }

    .sig-text {
        padding: 5px;
        border-top: 1px dotted #000000;
        display: inline-block;
    }

    tr, td {
        page-break-inside: avoid !important;
    }

    .page {
        page-break-after: auto !important;
        page-break-inside: avoid !important;
    }
</style>

<?php
$chunks = collect($datas)->chunk(8); $count = 1;
setlocale(LC_TIME, "bn_BD.utf8");
//\Carbon\Carbon::setUtf8(true);
?>
<?php $i = 1;?>
@if($generated_type=="salary")
    @foreach($chunks as $chunk)
        <div class="page">
            <h2 style="text-align: center;margin: 0;padding-top:10px;line-height:24px">
                {{$kpi->unit->unit_name_bng}} জেলার অঙ্গীভূত পিসি/এপিসি ও আনসারদের ভাতাদির
                বিল {{LanguageConverter::engToBng(\Carbon\Carbon::parse($generated_date)->format('d/m/Y'))}} <br>
                সংস্থার নাম : {{$kpi->kpi_name}}
            </h2>
            <div style="padding: 10px 0px 0;font-size: 14px">
                <div style="overflow: hidden;margin-bottom: 10px">
                    <div style="float: left;font-size:14px">
                        তারিখ : {{LanguageConverter::engToBng(\Carbon\Carbon::now()->format('d/m/Y'))}}
                    </div>
                    <div style="float: right;white-space:nowrap;font-size:14px">
                        মাসের নাম
                        : {{LanguageConverter::engToBng(\Carbon\Carbon::createFromFormat("F, Y",$generated_month)->formatLocalized("%B, %Y"))}}
                    </div>
                </div>
                <table border="1" cellpadding="0" cellspacing="0" style="width: 100%">
                    <tr>
                        <td rowspan="2">ক্র/নং</td>
                        <td rowspan="2">আইডি নং</td>
                        <td rowspan="2">পদবী</td>
                        <td rowspan="2" style="width: 200px">নাম ও পিতার নাম</td>
                        <td rowspan="2">মোট দিন</td>
                        <td rowspan="2">দৈনিক ভাতা
                            পিসি/এপিসি {{LanguageConverter::engToBng(DC::getValue('DPA')->cons_value)}}
                            /-
                            টাকা এবং আনসার {{LanguageConverter::engToBng(DC::getValue('DA')->cons_value)}}/- টাকা হারে
                        </td>
                        <td rowspan="2">দৈনিক রেশন ভাতা পিসি/এপিসি
                            আনসার {{LanguageConverter::engToBng(DC::getValue('R')->cons_value)}}/-
                            হারে
                        </td>
                        <td rowspan="2">দৈনিক ধৌত ও চুল কাটা
                            ভাতা {{LanguageConverter::engToBng(DC::getValue('CB')->cons_value)}}/- হারে
                        </td>
                        <td rowspan="2">দৈনিক যাতায়াত
                            ভাতা {{LanguageConverter::engToBng(DC::getValue('CV')->cons_value)}}/-
                            হারে
                        </td>
                        <td rowspan="2">দৈনিক চিকিৎসা
                            ভাতা {{LanguageConverter::engToBng(DC::getValue('DV')->cons_value)}}/-
                            হারে
                        </td>
                        <td rowspan="2">
                            মহার্ঘভাতা
                        </td>
                        <td rowspan="2">দৈনিক ভাতার
                            @if($kpi->details->is_special_kpi)
                                <span>{{LanguageConverter::engToBng($kpi->details->special_amount)}}%</span>
                            @elseif($kpi->details->with_weapon)
                                <span>২০%</span>
                            @else
                                <span>১৫%</span>
                            @endif

                            আনুসাঙ্গীক হারে
                        </td>
                        <td colspan="4">কর্তন</td>
                        <td rowspan="2">
                            সর্বমোট টাকার পরিমান
                        </td>
                        <td rowspan="2">
                            নীট টাকার পরিমান
                        </td>
                        <td rowspan="2">
                            প্রাপকের<br>স্বাক্ষর
                        </td>
                    </tr>
                    <tr>
                        <td>রেজিমেন্টাল তহবিল</td>
                        <td>কল্যাণ তহবিল</td>
                        <td>রেভিনিউ স্ট্যাম্প</td>
                        <td>শেয়ার ফি</td>
                    </tr>

                    @forelse($chunk as $data)
                        <tr>
                            <td>{{$i++}}</td>
                            <td>{{$data['ansar_id']}}</td>
                            <td>{{$data['rank']}}</td>
                            <td>{{$data['ansar_name']}}<br>{{$data["father_name"]}}</td>
                            <td>{{LanguageConverter::engToBng($data['total_day'])}}</td>
                            <td>{{LanguageConverter::engToBng($data['total_daily_fee'])}}</td>
                            <td>{{LanguageConverter::engToBng($data['total_ration_fee'])}}</td>
                            <td>{{LanguageConverter::engToBng($data['total_barber_fee'])}}</td>
                            <td>{{LanguageConverter::engToBng($data['total_transportation_fee'])}}</td>
                            <td>{{LanguageConverter::engToBng($data['total_medical_fee'])}}</td>
                            <td>০</td>
                            <td>{{LanguageConverter::engToBng($data['extra'])}}</td>
                            <td>{{LanguageConverter::engToBng($data['reg_fee'])}}</td>
                            <td>{{LanguageConverter::engToBng($data['welfare_fee'])}}</td>
                            <td>{{LanguageConverter::engToBng($data['revenue_stamp'])}}</td>
                            <td>{{LanguageConverter::engToBng($data['share_amount'])}}</td>
                            <td>{{LanguageConverter::engToBng($data['total_amount'])}}</td>
                            <td>{{LanguageConverter::engToBng($data['net_amount'])}}</td>
                            <td>
                                &nbsp;
                            </td>
                        </tr>
                    @empty
                    @endforelse
                    <tr>
                        <td colspan="5"></td>
                        <td>{{LanguageConverter::engToBng(collect($chunk)->sum('total_daily_fee'))}}</td>
                        <td>{{LanguageConverter::engToBng(collect($chunk)->sum('total_ration_fee'))}}</td>
                        <td>{{LanguageConverter::engToBng(collect($chunk)->sum('total_barber_fee'))}}</td>
                        <td>{{LanguageConverter::engToBng(collect($chunk)->sum('total_transportation_fee'))}}</td>
                        <td>{{LanguageConverter::engToBng(collect($chunk)->sum('total_medical_fee'))}}</td>
                        <td>০</td>
                        <td>{{LanguageConverter::engToBng(sprintf('%.2f',collect($chunk)->sum('extra')))}}</td>
                        <td>{{LanguageConverter::engToBng(collect($chunk)->sum('reg_fee'))}}</td>
                        <td>{{LanguageConverter::engToBng(collect($chunk)->sum('welfare_fee'))}}</td>
                        <td>{{LanguageConverter::engToBng(collect($chunk)->sum('revenue_stamp'))}}</td>
                        <td>{{LanguageConverter::engToBng(collect($chunk)->sum('share_amount'))}}</td>
                        <td>{{LanguageConverter::engToBng(collect($chunk)->sum('total_amount'))}}</td>
                        <td>{{LanguageConverter::engToBng(collect($chunk)->sum('net_amount'))}}</td>
                        <td></td>
                    </tr>
                    <tr>
                        @if($chunks->count()==1)
                            <td colspan="16" style="text-align: right">সর্বমোট :</td>
                        @else
                            <td colspan="16" style="text-align: right">মোট :</td>
                        @endif
                        <td>{{LanguageConverter::engToBng(collect($chunk)->sum(function($item){
            return $item["total_daily_fee"]+$item["total_ration_fee"]+$item["total_barber_fee"]+$item["total_transportation_fee"]+$item["total_medical_fee"]+$item["extra"];
            }))}}</td>
                        <td></td>
                        <td></td>
                    </tr>
                    @if(count($chunks)>1&&$count++==count($chunks))
                        <tr>
                            <td colspan="16" style="text-align: right">সর্বমোট :</td>
                            <td>{{LanguageConverter::engToBng(collect($datas)->sum(function($item){
            return $item["total_daily_fee"]+$item["total_ration_fee"]+$item["total_barber_fee"]+$item["total_transportation_fee"]+$item["total_medical_fee"]+$item["extra"];
            }))}}</td>
                            <td></td>
                            <td></td>
                        </tr>
                    @endif
                </table>
                <div style="overflow: hidden;margin-top: 6%;font-size: 14px">
                    <div style="float: left;width: 20%;text-align: center">
            <span class="sig-text">
            পিসির স্বাক্ষর
                </span>
                    </div>
                    <div style="float: left;width: 25%;text-align: center">
            <span class="sig-text">
            সংস্থা কর্মকর্তার স্বাক্ষর
                </span>
                    </div>
                    <div style="float: left;width: 26%;text-align: center">
            <span class="sig-text">
            উপজেলা আনসার ও ভিডিপি কর্মকর্তার স্বাক্ষর
                </span>
                    </div>
                    <div style="float: left;width: 29%;text-align: center">
           <span class="sig-text">
               জেলা কমান্ডেন্ট, আনসার ও গ্রাম প্রতিরক্ষা বাহিনী<br>
               {{$kpi->unit->unit_name_bng}}
           </span>

                    </div>
                </div>


            </div>
        </div>
    @endforeach
@else
    @foreach($chunks as $chunk)
        <div class="page">
            <h2 style="text-align: center;margin: 0;padding-top:10px;line-height:24px">
                {{$kpi->unit->unit_name_bng}} জেলার অঙ্গীভূত পিসি/এপিসি ও আনসারদের ঈদ-উল-ফিতর/ঈদ-উল-আযহা উৎসব ভাতাদির
                বিল {{LanguageConverter::engToBng(\Carbon\Carbon::parse($generated_date)->format('d/m/Y'))}} <br>
                সংস্থার নাম : {{$kpi->kpi_name}}
            </h2>
            <div style="padding: 10px 0px 0;font-size: 14px">
                <div style="overflow: hidden;margin-bottom: 10px">
                    <div style="float: left;font-size:14px">
                        তারিখ : {{LanguageConverter::engToBng(\Carbon\Carbon::now()->format('d/m/Y'))}}
                    </div>
                    <div style="float: right;white-space:nowrap;font-size:14px">
                        মাসের নাম
                        : {{LanguageConverter::engToBng(\Carbon\Carbon::createFromFormat("F, Y",$generated_month)->formatLocalized("%B, %Y"))}}
                    </div>
                </div>
                <table border="1" cellpadding="0" cellspacing="0" style="width: 100%">
                    <tr>
                        <td>ক্র/নং</td>
                        <td>আইডি নং</td>
                        <td>পদবী</td>
                        <td style="width: 200px">নাম ও পিতার নাম</td>
                        <td>উৎসব ভাতার পরিমান
                            (পিসি/এপিসি {{LanguageConverter::engToBng(DC::getValue('EBPA')->cons_value)}}
                            /-
                            টাকা, আনসার {{LanguageConverter::engToBng(DC::getValue('EBA')->cons_value)}}/-
                        </td>
                        <td>
                            প্রাপকের স্বাক্ষর
                        </td>
                    </tr>
                    @forelse($chunk as $data)
                        <tr>
                            <td>{{$i++}}</td>
                            <td>{{$data['ansar_id']}}</td>
                            <td>{{$data['rank']}}</td>
                            <td>{{$data['ansar_name']}}<br>{{$data["father_name"]}}</td>
                            <td>{{LanguageConverter::engToBng($data['net_amount'])}}</td>
                            <td>
                                &nbsp;
                            </td>
                        </tr>
                    @empty
                    @endforelse
                    <tr>
                        @if($chunks->count()==1)
                            <td colspan="4" style="text-align: right">সর্বমোট :</td>
                        @else
                            <td colspan="4" style="text-align: right">মোট :</td>
                        @endif
                        <td>{{LanguageConverter::engToBng(collect($chunk)->sum('net_amount'))}}</td>
                        <td></td>
                    </tr>
                    @if(count($chunks)>1&&$count++==count($chunks))
                        <tr>
                            <td colspan="4" style="text-align: right">সর্বমোট :</td>
                            <td>{{LanguageConverter::engToBng(collect($datas)->sum('net_amount'))}}</td>
                            <td></td>
                        </tr>
                    @endif
                </table>
                <div style="overflow: hidden;margin-top: 6%;font-size: 14px">
                    <div style="float: left;width: 20%;text-align: center">
            <span class="sig-text">
            পিসির স্বাক্ষর
                </span>
                    </div>
                    <div style="float: left;width: 25%;text-align: center">
            <span class="sig-text">
            সংস্থা কর্মকর্তার স্বাক্ষর
                </span>
                    </div>
                    <div style="float: left;width: 26%;text-align: center">
            <span class="sig-text">
            উপজেলা আনসার ও ভিডিপি কর্মকর্তার স্বাক্ষর
                </span>
                    </div>
                    <div style="float: left;width: 29%;text-align: center">
           <span class="sig-text">
               জেলা কমান্ডেন্ট, আনসার ও গ্রাম প্রতিরক্ষা বাহিনী<br>
               {{$kpi->unit->unit_name_bng}}
           </span>

                    </div>
                </div>


            </div>
        </div>
    @endforeach
@endif

</body>
</html>
