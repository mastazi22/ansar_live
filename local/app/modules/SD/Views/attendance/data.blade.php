<div class="table-responsive">
    @if($type=="count")
        <table class="table table-condensed table-bordered">
            <caption style="padding: 0 10px">
                <h4 style="    box-shadow: 1px 1px 1px #c5bfbf;padding: 10px 0;" class="text-bold text-center">
                    Attendance of<br>{{\Carbon\Carbon::parse($first_date)->format("F, Y")}}
                </h4>
            </caption>
            <tr>
                <th style="width: 100px">Date</th>
                <th>Attendance Status</th>
                <th style="width: 50px">Action</th>
            </tr>
            @for($i=0;$i<\Carbon\Carbon::parse($first_date)->daysInMonth;$i++)
                <?php $ad = $data->where('day',$i+1)->first(); ?>
                <tr>
                    <td>{{\Carbon\Carbon::parse($first_date)->addDays($i)->format('d-M-Y')}}</td>
                    @if($ad)
                        <td>{{$ad}}</td>
                        @else
                        <td class="bg-danger">{{"No data available"}}</td>
                    @endif
                    <td>
                        <button class="btn btn-primary btn-xs">
                            <i class="fa fa-eye"></i>&nbsp;View
                        </button>
                    </td>
                </tr>
                @endfor
        </table>
        @elseif($type=='view')
        <table class="table table-condensed table-bordered">
            <caption style="padding: 0 10px">
                <h4 style="    box-shadow: 1px 1px 1px #c5bfbf;padding: 10px 0;" class="text-bold text-center">
                    Attendance of (ID:{{$ansar_id}})<br>{{\Carbon\Carbon::parse($first_date)->format("F, Y")}}
                </h4>
            </caption>
            <tr>
                <th style="width: 100px">Date</th>
                <th>Present</th>
                <th>Absent</th>
                <th>Leave</th>
                <th style="width: 50px">Action</th>
            </tr>
            @for($i=0;$i<\Carbon\Carbon::parse($first_date)->daysInMonth;$i++)
                <?php $ad = $data->where('day',$i+1)->first(); ?>
                <tr>
                    <td>{{\Carbon\Carbon::parse($first_date)->addDays($i)->format('d-M-Y')}}</td>
                    @if($ad)
                        <td>{!! $ad->is_present?"<span class='label-success'>Present</span>":'--' !!}</td>
                        <td>{!! !$ad->is_present?"<span class='label-danger'>Absent</span>":'--' !!}</td>
                        <td>{!! $ad->is_leave?"<span class='label-success'>Leave</span>":'--' !!}</td>
                    @else
                        <td class="bg-danger" colspan="3">{{"No data available"}}</td>
                    @endif
                    <td>
                        <button class="btn btn-primary btn-xs">
                            <i class="fa fa-eye"></i>&nbsp;View
                        </button>
                    </td>
                </tr>
            @endfor
        </table>
    @endif
</div>