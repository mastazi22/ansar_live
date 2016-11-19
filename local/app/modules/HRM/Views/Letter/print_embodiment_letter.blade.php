<h3 style="text-align: center">Embodiment Letter&nbsp;&nbsp;<a href="#" id="print-report"><span class="glyphicon glyphicon-print"></span></a></h3>
<div class="letter">
    <div class="letter-header">
        <div class="header-top" style="background: none !important;">
            <h4>গণপ্রজাতন্ত্রী বাংলাদেশ সরকার</br>
                আনসার ও গ্রাম প্রতিরক্ষা বাহিনী
                <br>
                জেলা কমান্ড্যান্টের কার্যালয়</br>
                {{$user?$user->unit:'n\a'}} জেলা
            </h4>
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
            আনসার বাহিনী আইন ১৯৯৫ খ্রিঃ এর ধারা ৬ (৪), আনসার ও গ্রাম প্রতিরক্ষা বাহিনী সদর দপ্তরের স্মারক নং-আইন-৫১/আনস, তারিখ-২৪/০৩/১৯৯৬ খ্রিঃ, স্মারক নং-অপাঃ/কেপিআই/ ৮৮০(৩)/১২৯/আনস, তারিখঃ ০৩/০৩/২০০৯ খ্রিঃ এর পরিপ্রেক্ষিতে নিম্নবর্ণিত আনসার সদস্যকে তার                   অদ্য তারিখ হতে অঙ্গীভূত করা হলো।
        </p>
        <div class="letter-content-middle">
            <h4>“তফসিল "ক" (অঙ্গীভূত)”</h4>
            <table class="table table-bordered">
                <tr>
                    <th>ক্রমিক নং</th>
                    <th>আইডি নং</th>
                    <th>পদবী</th>
                    <th>নাম ও পিতার নাম</th>
                    <th>ঠিকানা:<br>গ্রাম , পোস্ট, উপজেলা ও জেলা</th>
                    <th>সংস্থার নাম </th>
                    <th>অঙ্গিভুতির তারিখ </th>
                </tr>
                <?php $i=1; ?>
                @foreach($result as $r)
                    <tr>
                        <td>{{LanguageConverter::engToBng($i++)}}</td>
                        <td>{{LanguageConverter::engToBng($r->ansar_id)}}</td>
                        <td>{{$r->rank}}</td>
                        <td>{{$r->name}}<br>{{$r->father_name}}</td>
                        <td>{{$r->village_name}},&nbsp;{{$r->pon}},&nbsp;{{$r->thana}},&nbsp;{{$r->unit}}</td>
                        <td>{{$r->kpi_name}}</td>
                        <td>{{LanguageConverter::engToBng(date('d/m/Y',strtotime($r->joining_date)))}}</td>
                    </tr>
                @endforeach
            </table>
        </div>
        @include('HRM::Letter.letter_footer',['user'=>$user])
    </div>
</div>