<div class="col-sm-12" id="print-service_record_unitwise">
    <?php $i = 0; ?>

    @forelse(collect($ansar_details)->groupBy('kpi') as $key=>$value)
        <table class="table table-bordered">
            <caption>{{$key}}</caption>
            <tr>
                <th>Rank</th>
                <th>Name</th>
                <th>Kpi</th>
                <th>Unit</th>
                <th>Date</th>
            </tr>
            @foreach($value as $v)
                <tr>
                    <td>
                    {{$v->rank}}
                    </td>
                    <td>
                    {{$v->name}}
                    </td>
                    <td>
                    {{$v->kpi}}
                    </td>
                    <td>
                    {{$v->unit}}
                    </td>
                    <td>
                    {{$v->r_date}}
                    </td>
                </tr>
                @endforeach
        </table>
        {{--<div style="overflow: hidden">--}}
            {{--<div class="max-min-button">--}}
                {{--{{$key}} &nbsp;<span><i class="fa @if($i==0) fa-minus @else fa-plus @endif"></i></span>--}}
            {{--</div>--}}
            {{--<div class="table-responsive drop-down-view print-service_record_unitwise"--}}
                 {{--ng-class="{'invisible-panel':$index>0}">--}}
                {{--<table class="table table-bordered">--}}
                    {{--<caption style="display: none">[[key]]</caption>--}}
                    {{--<tr>--}}
                        {{--<th>[[report.ansar.id]]</th>--}}
                        {{--<th>[[report.ansar.rank]]</th>--}}
                        {{--<th>[[report.ansar.name]]</th>--}}
                        {{--<th>[[report.ansar.kpi_name]]</th>--}}
                        {{--<th>[[report.ansar.district]]</th>--}}
                        {{--<th>[[report.ansar.reporting_date]]</th>--}}
                        {{--<th>[[report.ansar.joining_date]]</th>--}}
                        {{--<th>[[report.ansar.service_ended_date]]</th>--}}
                    {{--</tr>--}}
                    {{--<tr ng-show="ansars.length==0">--}}
                    {{--<td colspan="10" class="warning no-ansar">--}}
                    {{--No ansar is available to see--}}
                    {{--</td>--}}
                    {{--</tr>--}}
                    {{--@foreach($value as $v)--}}
                        {{--<tr>--}}
                            {{--<td>--}}
                                {{--<a href="{{URL::to('/entryreport')}}/[[a.id]]">[[a.id]]</a>--}}
                            {{--</td>--}}
                            {{--<td>--}}
                                {{--{{$v->rank}}--}}
                            {{--</td>--}}
                            {{--<td>--}}
                                {{--{{$v->name}}--}}
                            {{--</td>--}}
                            {{--<td>--}}
                                {{--{{$v->kpi}}--}}
                            {{--</td>--}}
                            {{--<td>--}}
                                {{--{{$v->unit}}--}}
                            {{--</td>--}}
                            {{--<td>--}}
                                {{--{{$v->r_date}}--}}
                            {{--</td>--}}
                            {{--<td>--}}
                                {{--[[dateConvert(a.j_date)]]--}}
                            {{--</td>--}}
                            {{--<td>--}}
                            {{--[[a.reason_in_bng]]--}}
                            {{--</td>--}}
                            {{--<td>--}}
                                {{--[[dateConvert(a.se_date)]]--}}
                            {{--</td>--}}
                            {{--<td>--}}
                            {{--[[calculate(a.total_service_days)]]--}}
                            {{--</td>--}}
                        {{--</tr>--}}
                        {{--@endforeach--}}
                {{--</table>--}}
            {{--</div>--}}
        {{--</div>--}}
    @empty
        <div>
            <h3 style="text-align: center">No Ansar Found</h3>
        </div>
    @endforelse

</div>