<h3 style="text-align: center">Disembodiment Letter&nbsp;&nbsp;<a href="#" id="print-report"><span class="glyphicon glyphicon-print"></span></a></h3>
<div class="letter">
    <div class="letter-header">
        <div class="header-top" style="background: none">
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
            আনসার বাহিনী আইন ১৯৯৫ খ্রিঃ এর ধারা ৬ (৪), আনসার ও গ্রাম প্রতিরক্ষা বাহিনী সদর দপ্তরের স্মারক নং-আইন-৫১/আনস, তারিখ-২৪/০৩/১৯৯৬ খ্রিঃ, স্মারক নং-অপাঃ/কেপিআই/ ৮৮০(৩)/১২৯/আনস, তারিখঃ ০৩/০৩/২০০৯ খ্রিঃ এর পরিপ্রেক্ষিতে নিম্নবর্ণিত আনসার সদস্যকে তার                   অদ্য তারিখ হতে <span style="border-bottom: 1px dashed #000000">{{$mem->reason}}</span> কারনে অ-অঙ্গীভূত করা হলো।
        </p>
        <div class="letter-content-middle">
            <h4>“তফসিল "ক" (অ-অঙ্গীভূত)”</h4>
            <table class="table table-bordered">
                <tr>
                    <th>ক্রমিক নং</th>
                    <th>আইডি নং</th>
                    <th>পদবী</th>
                    <th>নাম ও পিতার নাম</th>
                    <th>ঠিকানা:<br>গ্রাম , পোস্ট, উপজেলা ও জেলা</th>
                    <th>সংস্থার নাম </th>
                    <th>অঙ্গিভুতির তারিখ </th>
                    <th>অ-অঙ্গিভুতির তারিখ </th>
                </tr>
                <?php $i=1; ?>
                @forelse($result as $r)
                    <tr>
                        <td>{{LanguageConverter::engToBng($i++)}}</td>
                        <td>{{LanguageConverter::engToBng($r->ansar_id)}}</td>
                        <td>{{$r->rank}}</td>
                        <td>{{$r->name}}<br>{{$r->father_name}}</td>
                        <td>{{$r->village_name}},&nbsp;{{$r->pon}},&nbsp;{{$r->thana}},&nbsp;{{$r->unit}}</td>
                        <td>{{$r->kpi_name}}</td>
                        <td>{{$r->joining_date}}</td>
                        <td>{{$r->release_date}}</td>
                    </tr>
                @empty
                    <tr>
                        <td style="font-size: 1.5em;" colspan="8">No Ansar Found</td>
                    </tr>
                @endforelse
            </table>
        </div>
        <div class="letter-footer">
            <div class="footer-top">
                <ul class="pull-right" style="margin-top: 90px">
                    <li>{{$user?$user->first_name.' '.$user->last_name:'n\a'}}</li>
                    <li>জেলা কমাণ্ডান্ট</li>
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
                        ………রেঞ্জ……………।
                    </li>
                    <li>৩। জেলা প্রশাসক………………।</li>
                    <li>৪। পুলিশসুপার……………।</li>
                    <li>৫। সংস্থা…………………………।</li>
                    <li>৬। উপজেলা আনসার ও ভিডিপি কর্মকর্তা (সংশ্লিষ্ট)…………………।</li>
                    <li>৭। পিসি/এপিসি/ভারপ্রাপ্ত।</li>
                    <li>৮। অফিসকপি। </li>
                </ul>
                <ul class="pull-right">
                    <li>তারিখঃ{{LanguageConverter::engToBng(date('d/m/Y',strtotime($mem->created_at)))}}  খ্রিঃ</li>
                    <li>&nbsp;</li>
                    <li>সদয় অবগতির জন্য
                        <br>&nbsp;</li>
                    <li>&nbsp;<br>&nbsp;</li>
                    <li>&nbsp;</li>
                    <li>&nbsp;</li>
                    <li>অবগতি ও কার্যক্রমের জন্য।</li>

                </ul>
            </div>
            <div class="footer-bottom">
                <ul class="pull-right">
                    {{--<li>তারিখঃ{{date('d/m/Y',strtotime($mem->created_at))}}</li>--}}
                    <li>জেলা কমাণ্ডান্ট</li>
                    <li>মোবাইলঃ<span style="border-bottom: 1px dashed #000000;    top: -5px;display: inline-block;position: relative;">{{$user?$user->mobile_no:'n\a'}}</span></li>
                    <li>ই-মেইলঃ{{$user?$user->email:'n\a'}}</li>
                </ul>
            </div>
        </div>
    </div>
</div>