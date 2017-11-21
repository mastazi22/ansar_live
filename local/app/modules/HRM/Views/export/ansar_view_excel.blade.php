<html>
<head>
    <meta charset="UTF-8">
</head>
<body>
<?php $i = $index; ?>
<table border="1">
@if(isset($type) && strcasecmp($type,"paneled_ansar")==0)
    <tr>
        <th>SL. No</th>
        <th>Ansar ID</th>
        <th>Rank</th>
        <th>Name</th>
        <th>Birth Date</th>
        <th>Home District</th>
        <th>Thana</th>
        <th>Panel Date</th>
        <th>Panel Id</th>

    </tr>
    @forelse($ansars as $ansar)
        <tr>
            <td>{{$i++}}</td>
            <td><a href="{{URL::to('HRM/entryreport',['ansarid'=>$ansar->id])}}">{{$ansar->id}}</a></td>
            <td>{{$ansar->rank}}</td>
            <td>{{$ansar->name}}</td>
            <td>{{\Carbon\Carbon::parse($ansar->birth_date)->format('d-M-Y')}}</td>
            <td>{{$ansar->unit}}</td>
            <td>{{$ansar->thana}}</td>
            <td>{{\Carbon\Carbon::parse($ansar->panel_date)->format('d-M-Y')}}</td>
            <td>{{$ansar->memorandum_id}}</td>
        </tr>
    @empty
        <tr>
            <td class="warning" colspan="9">No Ansar Found</td>
        </tr>
    @endforelse
@elseif(isset($type) && strcasecmp($type,"embodied_ansar")==0)
    <tr>
        <th>SL. No</th>
        <th>Ansar ID</th>
        <th>Rank</th>
        <th>Name</th>
        <th>Birth Date</th>
        <th>Home District</th>
        <th>Thana</th>
        <th>Kpi Name</th>
        <th>Embodiment Date</th>
        <th>Embodiment Id</th>

    </tr>
    @forelse($ansars as $ansar)
        <tr>
            <td>{{$i++}}</td>
            <td><a href="{{URL::to('HRM/entryreport',['ansarid'=>$ansar->id])}}">{{$ansar->id}}</a></td>
            <td>{{$ansar->rank}}</td>
            <td>{{$ansar->name}}</td>
            <td>{{\Carbon\Carbon::parse($ansar->birth_date)->format('d-M-Y')}}</td>
            <td>{{$ansar->unit}}</td>
            <td>{{$ansar->thana}}</td>
            <td>{{$ansar->kpi_name}}</td>
            <td>{{\Carbon\Carbon::parse($ansar->joining_date)->format('d-M-Y')}}</td>
            <td>{{$ansar->memorandum_id}}</td>
        </tr>
    @empty
        <tr>
            <td class="warning" colspan="10">No Ansar Found</td>
        </tr>
    @endforelse
@elseif(isset($type) && strcasecmp($type,"embodied_ansar_in_different_district")==0)
    <tr>
        <th>SL. No</th>
        <th>Ansar ID</th>
        <th>Rank</th>
        <th>Name</th>
        <th>Birth Date</th>
        <th>Home District</th>
        <th>Thana</th>
        <th>Kpi Name</th>
        <th>Embodiment Date</th>
        <th>Embodiment Id</th>

    </tr>
    @forelse($ansars as $ansar)
        <tr>
            <td>{{$i++}}</td>
            <td><a href="{{URL::to('HRM/entryreport',['ansarid'=>$ansar->id])}}">{{$ansar->id}}</a></td>
            <td>{{$ansar->rank}}</td>
            <td>{{$ansar->name}}</td>
            <td>{{\Carbon\Carbon::parse($ansar->birth_date)->format('d-M-Y')}}</td>
            <td>{{$ansar->unit}}</td>
            <td>{{$ansar->thana}}</td>
            <td>{{$ansar->kpi_name}}</td>
            <td>{{\Carbon\Carbon::parse($ansar->joining_date)->format('d-M-Y')}}</td>
            <td>{{$ansar->memorandum_id}}</td>
        </tr>
    @empty
        <tr>
            <td class="warning" colspan="10">No Ansar Found</td>
        </tr>
    @endforelse
@elseif(isset($type) && strcasecmp($type,"offerred_ansar")==0)
    <tr>
        <th>SL. No</th>
        <th>Ansar ID</th>
        <th>Rank</th>
        <th>Name</th>
        <th>Birth Date</th>
        <th>Home District</th>
        <th>Thana</th>
        <th>Offer District</th>
        <th>Offer Date</th>

    </tr>
    @forelse($ansars as $ansar)
        <tr>
            <td>{{$i++}}</td>
            <td><a href="{{URL::to('HRM/entryreport',['ansarid'=>$ansar->id])}}">{{$ansar->id}}</a></td>
            <td>{{$ansar->rank}}</td>
            <td>{{$ansar->name}}</td>
            <td>{{\Carbon\Carbon::parse($ansar->birth_date)->format('d-M-Y')}}</td>
            <td>{{$ansar->unit}}</td>
            <td>{{$ansar->thana}}</td>
            <td>{{\Carbon\Carbon::parse($ansar->sms_send_datetime)->format('d-M-Y h:i:s')}}</td>
        </tr>
    @empty
        <tr>
            <td class="warning" colspan="8">No Ansar Found</td>
        </tr>
    @endforelse
@elseif(isset($type) && strcasecmp($type,"rest_ansar")==0)
    <tr>
        <th>SL. No</th>
        <th>Ansar ID</th>
        <th>Rank</th>
        <th>Name</th>
        <th>Birth Date</th>
        <th>Home District</th>
        <th>Thana</th>
        <th>Rest Date</th>

    </tr>
    @forelse($ansars as $ansar)
        <tr>
            <td>{{$i++}}</td>
            <td><a href="{{URL::to('HRM/entryreport',['ansarid'=>$ansar->id])}}">{{$ansar->id}}</a></td>
            <td>{{$ansar->rank}}</td>
            <td>{{$ansar->name}}</td>
            <td>{{\Carbon\Carbon::parse($ansar->birth_date)->format('d-M-Y')}}</td>
            <td>{{$ansar->unit}}</td>
            <td>{{$ansar->thana}}</td>
            <td>{{\Carbon\Carbon::parse($ansar->rest_date)->format('d-M-Y')}}</td>
        </tr>
    @empty
        <tr>
            <td class="warning" colspan="8">No Ansar Found</td>
        </tr>
    @endforelse
@elseif(isset($type) && strcasecmp($type,"freezed_ansar")==0)
    <tr>
        <th>SL. No</th>
        <th>Ansar ID</th>
        <th>Rank</th>
        <th>Name</th>
        <th>Birth Date</th>
        <th>Home District</th>
        <th>Thana</th>
        <th>Freeze Reason</th>
        <th>Freeze Date</th>

    </tr>
    @forelse($ansars as $ansar)
        <tr>
            <td>{{$i++}}</td>
            <td><a href="{{URL::to('HRM/entryreport',['ansarid'=>$ansar->id])}}">{{$ansar->id}}</a></td>
            <td>{{$ansar->rank}}</td>
            <td>{{$ansar->name}}</td>
            <td>{{\Carbon\Carbon::parse($ansar->birth_date)->format('d-M-Y')}}</td>
            <td>{{$ansar->unit}}</td>
            <td>{{$ansar->thana}}</td>
            <td>{{$ansar->freez_reason}}</td>
            <td>{{\Carbon\Carbon::parse($ansar->freez_date)->format('d-M-Y')}}</td>
        </tr>
    @empty
        <tr>
            <td class="warning" colspan="9">No Ansar Found</td>
        </tr>
    @endforelse
@elseif(isset($type) && strcasecmp($type,"blocked_ansar")==0)
    <tr>
        <th>SL. No</th>
        <th>Ansar ID</th>
        <th>Rank</th>
        <th>Name</th>
        <th>Birth Date</th>
        <th>Home District</th>
        <th>Thana</th>
        <th>Block Reason</th>
        <th>Block Date</th>

    </tr>
    @forelse($ansars as $ansar)
        <tr>
            <td>{{$i++}}</td>
            <td><a href="{{URL::to('HRM/entryreport',['ansarid'=>$ansar->id])}}">{{$ansar->id}}</a></td>
            <td>{{$ansar->rank}}</td>
            <td>{{$ansar->name}}</td>
            <td>{{\Carbon\Carbon::parse($ansar->birth_date)->format('d-M-Y')}}</td>
            <td>{{$ansar->unit}}</td>
            <td>{{$ansar->thana}}</td>
            <td>{{$ansar->comment_for_block}}</td>
            <td>{{\Carbon\Carbon::parse($ansar->date_for_block)->format('d-M-Y')}}</td>
        </tr>
    @empty
        <tr>
            <td class="warning" colspan="9">No Ansar Found</td>
        </tr>
    @endforelse
@elseif(isset($type) && strcasecmp($type,"blacked_ansar")==0)
    <tr>
        <th>SL. No</th>
        <th>Ansar ID</th>
        <th>Rank</th>
        <th>Name</th>
        <th>Birth Date</th>
        <th>Home District</th>
        <th>Thana</th>
        <th>Black Reason</th>
        <th>Black Date</th>

    </tr>
    @forelse($ansars as $ansar)
        <tr>
            <td>{{$i++}}</td>
            <td><a href="{{URL::to('HRM/entryreport',['ansarid'=>$ansar->id])}}">{{$ansar->id}}</a></td>
            <td>{{$ansar->rank}}</td>
            <td>{{$ansar->name}}</td>
            <td>{{\Carbon\Carbon::parse($ansar->birth_date)->format('d-M-Y')}}</td>
            <td>{{$ansar->unit}}</td>
            <td>{{$ansar->thana}}</td>
            <td>{{$ansar->reason}}</td>
            <td>{{\Carbon\Carbon::parse($ansar->date)->format('d-M-Y')}}</td>
        </tr>
    @empty
        <tr>
            <td class="warning" colspan="9">No Ansar Found</td>
        </tr>
    @endforelse

@else
    <tr>
        <th>SL. No</th>
        <th>Ansar ID</th>
        <th>Rank</th>
        <th>Name</th>
        <th>Birth Date</th>
        <th>Home District</th>
        <th>Thana</th>

    </tr>
    @forelse($ansars as $ansar)
        <tr>
            <td>{{$i++}}</td>
            <td><a href="{{URL::to('HRM/entryreport',['ansarid'=>$ansar->id])}}">{{$ansar->id}}</a></td>
            <td>{{$ansar->rank}}</td>
            <td>{{$ansar->name}}</td>
            <td>{{\Carbon\Carbon::parse($ansar->birth_date)->format('d-M-Y')}}</td>
            <td>{{$ansar->unit}}</td>
            <td>{{$ansar->thana}}</td>
        </tr>
    @empty
        <tr>
            <td class="warning" colspan="7">No Ansar Found</td>
        </tr>
    @endforelse
@endif
</table>
</body>
</html>