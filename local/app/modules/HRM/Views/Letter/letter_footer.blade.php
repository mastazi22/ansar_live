<div class="letter-footer">
    <div class="footer-top">
        <ul class="pull-right" style="margin-top: 80px;width:33%">
            <li>{{$user?$user->first_name.' '.$user->last_name:'n\a'}}</li>
            <li>
                @if($user&&(trim($user->division)=="DMA"||trim($user->division)=="CMA"))
                    জোন অধিনায়ক<br>
                @else
                    জেলা কমান্ড্যান্ট<br>
                @endif
            </li>
            <li>
                @if($user&&(trim($user->division)=="DMA"||trim($user->division)=="CMA"))
                    {{$user?preg_replace('/\).+/',')',preg_replace('/.+\(/',$user->division_bng.'(',$user->unit)):'n\a'}}
                @else
                    {{$user?$user->unit:'n\a'}}
                @endif
            </li>
            <li>মোবাইলঃ<span style="border-bottom: 1px dashed #000000;    top: -5px;display: inline-block;position: relative;">{{$user?$user->mobile_no:'n\a'}}</span></li>
            <li>ই-মেইলঃ{{$user?$user->email:'n\a'}}</li>
        </ul>
    </div>
    <div class="footer-bottom">
        <ul class="pull-left" style="width: 50%">
            <li>স্বারক নং-{{$mem->memorandum_id}}</li>
            <li>অনুলিপি</li>
            <li>১। অপারেশন (কেপিআই) শাখা
                <br>আনসার ও গ্রাম প্রতিরক্ষা বাহিনী
                সদর দপ্তর, ঢাকা।
            </li>
            <li>২। পরিচালক
                আনসার ও গ্রাম প্রতিরক্ষা  বাহিনী
                ………<br>{{$user?(trim($user->division)=="DMA"||trim($user->division)=="CMA"?'মেট্রোপলিটন আনসার':'রেঞ্জ'):'রেঞ্জ'}}………………………।
            </li>
            @if($user&&(trim($user->division)=="DMA"||trim($user->division)=="CMA"))
                <li>৩। উপ-পুলিশ কমিশনার……………।</li>
                <li>৪। সংস্থা…………………………।</li>
                <li>৫। উপজেলা আনসার ও ভিডিপি কর্মকর্তা (সংশ্লিষ্ট)……………………………।</li>
                <li>৬। পিসি/এপিসি/ভারপ্রাপ্ত।</li>
                <li>৭। অফিসকপি। </li>
            @else
                <li>৩। জেলা প্রশাসক…………………………।</li>
                <li>৪। পুলিশসুপার………………………।</li>
                <li>৫। সংস্থা……………………………………।</li>
                <li>৬। উপজেলা আনসার ও ভিডিপি কর্মকর্তা <br>(সংশ্লিষ্ট)……………………………।</li>
                <li>৭। পিসি/এপিসি/ভারপ্রাপ্ত।</li>
                <li>৮। অফিস কপি। </li>
            @endif
        </ul>
        <ul class="pull-right" style="width: 33% !important;">
            <li>তারিখঃ{{LanguageConverter::engToBng(date('d/m/Y',strtotime($mem->created_at)))}}  খ্রিঃ</li>
            <li>&nbsp;</li>
            <li>সদয় অবগতির জন্য<br>&nbsp;</li>
            <li class="ppp">"&nbsp;<br>"&nbsp;</li>
            {{--<li>:&nbsp;</li>--}}
            <li class="ppp">"&nbsp;</li>
            @if($user&&!(trim($user->division)=="DMA"||trim($user->division)=="CMA"))<li class="ppp">"&nbsp;</li>@endif
            <li>অবগতি ও কার্যক্রমের জন্য</li>
            <li class="ppp" >&nbsp;<br>"&nbsp;</li>
            <li class="ppp" style="padding-top: 8px" >"&nbsp;</li>

        </ul>
    </div>
    <div class="footer-bottom">
        <ul class="pull-right" style="width: 33% !important;">
            <li>
                @if($user&&(trim($user->division)=="DMA"||trim($user->division)=="CMA"))
                    জোন অধিনায়ক<br>
                @else
                    জেলা কমান্ড্যান্ট<br>
                @endif
            </li>
        </ul>
    </div>
</div>