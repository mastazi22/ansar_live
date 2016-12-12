@if($i==0)<h3 style="text-align: center">Embodiment Letter&nbsp;&nbsp;<a href="#" id="print-report"><i class="fa fa-print"></i></a></h3>@endif
<div class="letter">
    <div class="letter-header">
        <div class="header-top" style="background: none !important;">
           @include('HRM::Letter.letter_header',['user'=>$user])
        </div>
        <div class="header-bottom">
                                    <span class="pull-left">
                                        স্বারক নং-{{$mem->memorandum_id}}
                                    </span>
                                    <span class="pull-right">
                                        তারিখঃ{{LanguageConverter::engToBng(date('d/m/Y',strtotime($mem->created_at)))}} খ্রিঃ
                                    </span>
        </div>
    </div>
    <div class="letter-body">
        <div class="body-top">
            <h4>“অফিস আদেশ”</h4>
        </div>
        <p class="letter-content-top">
            আনসার বাহিনী আইন ১৯৯৫ খ্রিঃ এর ধারা ৬ (৪), আনসার ও গ্রাম প্রতিরক্ষা বাহিনী সদর দপ্তরের স্মারক নং-আইন-৫১/আনস, তারিখ-২৪/০৩/১৯৯৬ খ্রিঃ, স্মারক নং-অপাঃ/কেপিআই/ ৮৮০(৩)/১২৯/আনস, তারিখঃ ০৩/০৩/২০০৯ খ্রিঃ এর পরিপ্রেক্ষিতে নিম্নবর্ণিত আনসার সদস্যকে অঙ্গীভূত করা হলো।
        </p>
        <div class="letter-content-middle">
            <h4>“তফসিল "ক" (অঙ্গীভূত)”</h4>
            <table class="table table-bordered table-condensed">
                <tr>
                    <th>ক্রমিক নং</th>
                    <th>আইডি নং</th>
                    <th>পদবী</th>
                    <th style="width:120px">নাম ও পিতার নাম</th>
                    <th>ঠিকানা:<br>গ্রাম , পোস্ট, উপজেলা ও জেলা</th>
                    <th>সংস্থার নাম </th>
                    <th>অঙ্গিভুতির তারিখ </th>
                </tr>
                <?php $ii=1; ?>
                @for($j=$i*5;$j<($i+1)*5;$j++)
                    @if(isset($result[$j]))
                    <tr>
                        <td>{{LanguageConverter::engToBng($ii++)}}</td>
                        <td>{{LanguageConverter::engToBng($result[$j]->ansar_id)}}</td>
                        <td>{{$result[$j]->rank}}</td>
                        <td style="width:120px">{{$r->name}}<br>{{$result[$j]->father_name}}</td>
                        <td>{{$result[$j]->village_name}},&nbsp;{{$result[$j]->pon}},&nbsp;{{$result[$j]->thana}},&nbsp;{{$result[$j]->unit}}</td>
                        <td>{{$result[$j]->kpi_name}}</td>
                        <td>{{LanguageConverter::engToBng(date('d/m/Y',strtotime($result[$j]->joining_date)))}}</td>
                    </tr>
                    @endif
                @endfor
            </table>
        </div>
        @include('HRM::Letter.letter_footer',['user'=>$user])
    </div>
</div>