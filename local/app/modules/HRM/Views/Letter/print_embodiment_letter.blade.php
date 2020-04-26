@if($i==0)
    <h3 style="text-align: center" class="print-hide">Embodiment Letter&nbsp;&nbsp;<a href="#" id="print-report">
            <i class="fa fa-print"></i>
        </a>
    </h3>
@endif
<div class="letter">
    @include('HRM::Letter.letter_header',['user'=>$user])

    <div class="letter-body">
        <div class="body-top">
            <h4>“অফিস আদেশ”</h4>
        </div>
        <p class="letter-content-top">আনসার বাহিনী আইন ১৯৯৫ খ্রিঃ এর ধারা ৬ (৪), আনসার ও গ্রাম প্রতিরক্ষা বাহিনী সদর
            দপ্তরের স্মারক নং-আইন-৫১/আনস, তারিখঃ ২৪/০৩/১৯৯৬ খ্রিঃ, স্মারক নং-অপাঃ/কেপিআই/৮৮০(৩)/১২৯/আনস, তারিখঃ
            ০৩/০৩/২০০৯ খ্রিঃ এর পরিপ্রেক্ষিতে নিম্নবর্ণিত আনসার সদস্যকে অঙ্গীভূত করা হলো।</p>
        <div class="letter-content-middle">
            <h4>“তফসিল "ক" (অঙ্গীভূত)”</h4>
            <table class="table table-bordered table-condensed">
                <tr>
                    <th style="width: 1%">ক্রমিক<br>নং</th>
                    <th style="width: 1%">আইডি<br>নং</th>
                    <th style="width: 1%">পদবী</th>
                    <th>নাম ও<br>পিতার নাম</th>
                    <th>ঠিকানা:<br>গ্রাম, পোস্ট, উপজেলা ও জেলা</th>
                    <th>সংস্থার নাম ও<br>উপজেলা/থানা</th>
                    <th style="width: 1%">অঙ্গিভুতির<br>তারিখ</th>
                </tr>
                <?php $ii = 1; ?>
                @for($j=0;$j<count($result);$j++)
                    @if(isset($result[$j]))
                        <tr>
                            <td>{{LanguageConverter::engToBng($ii++)}}</td>
                            <td>{{LanguageConverter::engToBng($result[$j]->ansar_id)}}</td>
                            <td>{{$result[$j]->rank}}</td>
                            <td>{{$result[$j]->name}}<br>{{$result[$j]->father_name}}</td>
                            <td>{{$result[$j]->village_name or ''}},&nbsp;{{$result[$j]->pon or ''}}
                                ,&nbsp;{{$result[$j]->thana or ''}},&nbsp;{{$result[$j]->unit or ''}}</td>
                            <td>{{$result[$j]->kpi_name.", ".$result[$j]->kpi_thana}}</td>
                            <td>{{LanguageConverter::engToBng(date('d/m/Y',strtotime($result[$j]->joining_date)))}}</td>
                        </tr>
                    @endif
                @endfor
            </table>
        </div>
        @include('HRM::Letter.letter_footer',['user'=>$user])
    </div>
</div>