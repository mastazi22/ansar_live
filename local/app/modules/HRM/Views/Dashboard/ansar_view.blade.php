<?php $i = $index; ?>
@if(isset($type) && strcasecmp($type,"pannel")==0)
    @forelse($ansars as $ansar)
        <tr>
            <td>{{$i++}}</td>
            <td><a href="{{URL::to('HRM/entryreport',['ansarid'=>$ansar->id])}}">{{$ansar->id}}</a></td>
            <td>{{$ansar->rank}}</td>
            <td>{{$ansar->name}}</td>
            <td>{{\Carbon\Carbon::parse($ansar->birth_date)->format('d-M-Y')}}</td>
            <td>{{$ansar->unit}}</td>
            <td>{{$ansar->thana}}</td>
            <td>{{\Carbon\Carbon::parse($ansar->created_at)->format('d-M-Y  h:i:s A')}}</td>
            <td>{{$ansar->memorandum_id}}</td>
        </tr>
    @empty
        <tr>
            <td class="warning" colspan="9">No Ansar Found</td>
        </tr>
    @endforelse
@elseif(isset($type) && strcasecmp($type,"embodied")==0)
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
@elseif(isset($type) && strcasecmp($type,"diff_embodied")==0)
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
@elseif(isset($type) && strcasecmp($type,"offer")==0)
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
@elseif(isset($type) && strcasecmp($type,"rest")==0)
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
@elseif(isset($type) && strcasecmp($type,"freeze")==0)
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
@elseif(isset($type) && strcasecmp($type,"block")==0)
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
@elseif(isset($type) && strcasecmp($type,"black")==0)
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
