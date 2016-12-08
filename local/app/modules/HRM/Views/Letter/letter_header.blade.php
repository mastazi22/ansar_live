<h4>গণপ্রজাতন্ত্রী বাংলাদেশ সরকার<br>
    আনসার ও গ্রাম প্রতিরক্ষা বাহিনী
    <br>
    @if($user&&trim($user->division)=="DMA")
        জোন অধিনায়কের কার্যালয়<br>
    @else
        জেলা কমান্ড্যান্টের কার্যালয়<br>
    @endif
    @if($user&&trim($user->division)=="DMA")
        {{$user?str_replace('ঢাকা',$user->division_bng,$user->unit):'n\a'}}
    @else
        {{$user?$user->unit:'n\a'}}
    @endif
</h4>