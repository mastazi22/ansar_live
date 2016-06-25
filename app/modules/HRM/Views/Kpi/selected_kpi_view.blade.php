<?php $i = $index; ?>
@if(count($kpis)==0)
    <tr class="warning">
        <td colspan="11">No KPI to show</td>
    </tr>
@else
    @foreach($kpis as $kpi)
        <tr>
            <td>{{$i++}}</td>
            {{--<td>{{$ansar->id}}</td>--}}
            <td>{{$kpi->kpi}}</td>
            <td>{{$kpi->division}}</td>
            <td>{{$kpi->unit}}</td>
            <td>{{$kpi->thana}}</td>
            <td>{{$kpi->address}}</td>
            <td>{{$kpi->contact}}</td>
            <td>
            <div class="col-xs-1">
                <a href="{{URL::to('/kpi-edit/'.$kpi->id)}}"
                   class="btn btn-primary btn-xs" title="Edit"><span
                            class="glyphicon glyphicon-edit"></span></a>
            </div>
            <div class="col-xs-1" style="@if(Auth::user()->type==22 || Auth::user()->type==44 || Auth::user()->type==55 || Auth::user()->type==66) display: none; @endif">
                @if(($kpi->status_of_kpi)==0)
                    <a class="btn btn-success btn-xs verification" title="verify"
                       ng-click="verify('{{$kpi->id}}', '{{$index}}')"
                       ng-disabled="verified[{{$index}}]"><span
                                class="fa fa-check"
                                ng-hide="verifying[{{$index}}]"></span>
                        <i class="fa fa-spinner fa-pulse"
                           ng-show="verifying[{{$index}}]"></i>
                    </a>
                @else
                    <a class="btn btn-success btn-xs verification" title="verify"
                       ng-click="verify('{{$kpi->id}}', '{{$index}}')"
                       ng-disabled="!verified[{{$index}}]"><span
                                class="fa fa-check"
                                ng-hide="verifying[{{$index}}]"></span>
                        <i class="fa fa-spinner fa-pulse"
                           ng-show="verifying[{{$index}}]"></i>
                    </a>
                @endif
            </div>
            </td>
            {{--<td>{{$ansar->se_date}}</td>--}}
        </tr>
    @endforeach
@endif