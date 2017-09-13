
<?php $i=1; ?>
@foreach($data as $d)

    <span class="bg-green" style="padding: 1px 5px;border-radius: 5px;margin-bottom: 5px;display: inline-block">{{$d}}</span>
    @if($i++%4==0) <br> @endif
@endforeach