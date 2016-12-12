@if($i==0)<h3 style="text-align: center"  class="print-hide">Transfer Letter&nbsp;&nbsp;<a href="#" id="print-report"><i class="fa fa-print"></i></a></h3>@endif
<div class="letter">
    <div class="letter-header">
        <div class="header-top" style="background: none">
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
            প্রশাসনিক কার্যক্রমের অংশ হিসেবে এবং ক্যাম্পের শৃঙ্খলার মানসমুন্নত রাখার স্বার্থে {{$user?$user->unit:'n\a'}} জেলার বিভিন্ন উপজেলার নিম্নবর্ণিত সংস্থার <span style="border-bottom: 1px dotted #000000;padding: 0 10px">{{LanguageConverter::engToBng(count($result))}}</span> জন অঙ্গীভূত আনসার সদস্যকে সংশ্লিষ্ট আনসার ক্যাম্পে বদলি করা হলো।
        </p>
        <div class="letter-content-middle">
            <table class="table table-bordered">
                <tr>
                    <th>ক্রমিক নং</th>
                    <th>আইডি নং</th>
                    <th>পদবী</th>
                    <th style="width:120px">নাম ও পিতার নাম</th>
                    <th>বর্তমান সংস্থার নাম</th>
                    <th>বদলিক্রিত সংস্থার নাম</th>
                </tr>
                <?php $ii=1; ?>
                @for($j=0;$j<count($result);$j++)
                    @if(isset($result[$j]))
                    <tr>
                        <td>{{LanguageConverter::engToBng($ii++)}}</td>
                        <td>{{LanguageConverter::engToBng($result[$j]->ansar_id)}}</td>
                        <td>{{$result[$j]->rank}}</td>
                        <td style="width:120px">{{$result[$j]->name}}<br>{{$result[$j]->father_name}}</td>
                        <td>{{$result[$j]->p_kpi_name}}</td>
                        <td>{{$result[$j]->t_kpi_name}}</td>
                    </tr>
                    @endif
                    @endfor
            </table>
        </div>
        <p class="letter-content-last">
            এ আদেশ জারীর তারিখটি যোগদান তারিখ হিসেবে গন্য হবে এবং ইহা বাস্তবায়ন নিশ্চিত করার জন্য সংশ্লিষ্ট সকলকে নির্দেশ দেয়া হল।
        </p>
        @include('HRM::Letter.letter_footer',['user'=>$user])
    </div>
</div>
<div class="page-break"></div>