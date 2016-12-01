<div class="letter-footer">
    <div class="footer-top">
        <ul class="pull-right" style="margin-top: 20px">
            <li>{{$user?$user->first_name.' '.$user->last_name:'n\a'}}</li>
            <li>জেলা কমান্ড্যান্টের</li>
            <li>মোবাইলঃ<span style="border-bottom: 1px dashed #000000;    top: -5px;display: inline-block;position: relative;">{{$user?$user->mobile_no:'n\a'}}</span></li>
            <li>ই-মেইলঃ{{$user?$user->email:'n\a'}}</li>
        </ul>
    </div>
    <div class="footer-bottom">
        <ul class="pull-left">
            <li>স্বারক নং-{{$mem->memorandum_id}}</li>
            <li>অনুলিপি সংরক্ষণঃ</li>
            <li>১। অপারেশন (কেপিআই) শাখা
                <br>আনসার ও গ্রামপ্রতিরক্ষাবাহিনী
                সদর দপ্তর, ঢাকা।
            </li>
            <li>২। পরিচালক
                আনসার ও গ্রাম প্রতিরক্ষা বাহিনী
                ………{{$user?(trim($user->division)=="DMA"||trim($user->division)=="CMA"?'মেট্রোপলিটন আনসার':'রেঞ্জ'):'রেঞ্জ'}}……………।
            </li>
            @if($user&&(trim($user->division)=="DMA"||trim($user->division)=="CMA"))
                <li>৩। পুলিশ কমিশনার……………।</li>
                <li>৪। সংস্থা…………………………।</li>
                <li>৫। উপজেলা আনসার ও ভিডিপি কর্মকর্তা (সংশ্লিষ্ট)…………………।</li>
                <li>৬। পিসি/এপিসি/ভারপ্রাপ্ত।</li>
                <li>৭। অফিসকপি। </li>
            @else
                <li>৩। জেলা প্রশাসক………………।</li>
                <li>৪। পুলিশসুপার……………।</li>
                <li>৫। সংস্থা…………………………।</li>
                <li>৬। উপজেলা আনসার ও ভিডিপি কর্মকর্তা (সংশ্লিষ্ট)…………………।</li>
                <li>৭। পিসি/এপিসি/ভারপ্রাপ্ত।</li>
                <li>৮। অফিসকপি। </li>
            @endif
        </ul>
        <ul class="pull-right">
            <li>তারিখঃ{{LanguageConverter::engToBng(date('d/m/Y',strtotime($mem->created_at)))}}  খ্রিঃ</li>
            <li>&nbsp;</li>
            <li>সদয় অবগতির জন্য
                <br>&nbsp;</li>
            <li>&nbsp;</li>
            <li>&nbsp;</li>
            <li>অবগতি ও কার্যক্রমের জন্য।</li>
            <li>&nbsp;</li>
            <li>&nbsp;</li>
            <li>জেলা কমান্ড্যান্টের</li>
            <li>মোবাইলঃ<span style="border-bottom: 1px dashed #000000;    top: -5px;display: inline-block;position: relative;">{{$user?$user->mobile_no:'n\a'}}</span></li>
            <li>ই-মেইলঃ&nbsp;{{$user?$user->email:'n\a'}}</li>

        </ul>
    </div>
    <div class="footer-bottom">
        <ul class="pull-right" style="width: 32%;">
            {{--<li>তারিখঃ{{date('d/m/Y',strtotime($mem->created_at))}}</li>--}}

        </ul>
    </div>
</div>