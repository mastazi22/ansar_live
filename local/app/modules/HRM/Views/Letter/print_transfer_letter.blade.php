@if($i==0)
    <h3 style="text-align: center" class="print-hide">Transfer Letter&nbsp;&nbsp;<a href="#" id="print-report">
            <i class="fa fa-print"></i>
        </a>
    </h3>
@endif
<div class="letter">
    <div class="letter-header">
        <div class="header-top" style="background: none;position: relative;">
            @include('HRM::Letter.letter_header',['user'=>$user])
        </div>
        <div class="header-bottom">
            <div class="pull-left" style="margin-top: 2%;">
                স্মারক নং&nbsp;-&nbsp;<b>{{$mem->memorandum_id}}</b>
            </div>
            <div class="pull-right">
                <table border="0" width="100%">
                    <tr>
                        <td rowspan="2" width="10px">তারিখঃ</td>
                        <td style="border-bottom: solid 1px #000;text-align: center;" class="jsDateConvert">
                            @if($mem->created_at)
                                <span>{{\Carbon\Carbon::parse($mem->created_at)->format('d/m/Y')}}</span> বঙ্গাব্দ
                            @else
                                <span>{{\Carbon\Carbon::now()->format('d/m/Y')}}</span> বঙ্গাব্দ
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td style="text-align: center;">
                            @if($mem->created_at)
                                {{LanguageConverter::engToBngWS(\Carbon\Carbon::parse($mem->created_at)->format('d/m/Y'))}}
                                খ্রিষ্টাব্দ
                            @else
                                {{LanguageConverter::engToBngWS(\Carbon\Carbon::now()->format('d/m/Y'))}} খ্রিষ্টাব্দ
                            @endif
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    <div class="letter-body">
        <div class="body-top"><h4>“অফিস আদেশ”</h4></div>
        <div class="letter-content-top">প্রশাসনিক কার্যক্রমের অংশ হিসেবে এবং ক্যাম্পের শৃঙ্খলার মান সমুন্নত রাখার
            স্বার্থে বাংলাদেশ আনসার ও গ্রাম প্রতিরক্ষা বাহিনী,
            @if($user&&(trim($user->division)=="DMA"||trim($user->division)=="CMA"))
                {{$user?$user->unit:''}} জোনের&nbsp;
            @else
                {{$user?$user->unit:''}} জেলার বিভিন্ন উপজেলার/থানার&nbsp;
            @endif
            নিম্নবর্ণিত সংস্থার&nbsp;<span style="border-bottom: 1px dashed #000000">{{LanguageConverter::engToBng(count($result))}}
            </span>&nbsp;জন অঙ্গীভূত আনসার সদস্যকে সংশ্লিষ্ট আনসার ক্যাম্পে বদলি করা হলো।</div>
        <div class="letter-content-middle">
            <table class="table table-bordered" width="100%">
                <tr>
                    <th style="width: 1%">ক্রমিক<br>নং</th>
                    <th style="width: 1%">আইডি<br>নং</th>
                    <th style="width: 1%">পদবী</th>
                    <th>নাম ও<br>পিতার নাম</th>
                    <th>বর্তমান সংস্থার নাম ও<br>উপজেলা/থানা</th>
                    <th>বদলিকৃত সংস্থার নাম ও<br>উপজেলা/থানা</th>
                </tr>
                <?php $ii = 1; ?>
                @for($j=0;$j<count($result);$j++)
                    @if(isset($result[$j]))
                        <tr>
                            <td>{{LanguageConverter::engToBng($ii++)}}</td>
                            <td>{{LanguageConverter::engToBng($result[$j]->ansar_id)}}</td>
                            <td>{{$result[$j]->rank}}</td>
                            <td>{{$result[$j]->name}}<br>{{$result[$j]->father_name}}</td>
                            <td>{{$result[$j]->p_kpi_name.", ".$result[$j]->pk_thana}}</td>
                            <td>{{$result[$j]->t_kpi_name.", ".$result[$j]->tk_thana}}</td>
                        </tr>
                    @endif
                @endfor
            </table>
        </div>
        <div class="letter-content-last">
            এ আদেশ জারীর তারিখটি যোগদান তারিখ হিসেবে গন্য হবে এবং ইহা বাস্তবায়ন নিশ্চিত করার জন্য সংশ্লিষ্ট সকলকে
            নির্দেশ দেয়া হল।
        </div>
        @include('HRM::Letter.letter_footer',['user'=>$user])
    </div>
</div>