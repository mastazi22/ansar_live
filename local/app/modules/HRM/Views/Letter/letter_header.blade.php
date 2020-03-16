<h4 style="font-weight: 500;">গণপ্রজাতন্ত্রী বাংলাদেশ সরকার<br>বাংলাদেশ আনসার ও গ্রাম প্রতিরক্ষা বাহিনী<br>
    @if($user&&(trim($user->division)=="DMA"||trim($user->division)=="CMA"))
        জোন অধিনায়কের কার্যালয়,&nbsp;
    @else
        জেলা কমান্ড্যান্টের কার্যালয়,&nbsp;
    @endif
    @if($user&&(trim($user->division)=="DMA"||trim($user->division)=="CMA"))
        {{$user?preg_replace('/\).+/',')',preg_replace('/.+\(/',$user->division_bng.'(',$user->unit)):''}}
    @else
        {{$user?$user->unit:''}}
    @endif
    <br><span style="text-decoration: underline;">www.ansarvdp.gov.bd</span>
</h4>
<img src="{{asset('dist/img/mujib-logo.png')}}" class="img-responsive mujib-logo" alt="Mujib100Logo">