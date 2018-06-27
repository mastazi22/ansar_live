<div class="table-responsive">
    <style>
        .count-box {
            font-weight: bold;
            font-size: 16px;
            padding: 0 10px;
            box-shadow: 1px 1px 3px -1px;
            background: #ffffff;
        }

        .count-box:not(:last-child) {
            margin-right: 10px;
        }
        .att-label{
            padding: 5px;
            display: block;
            border-radius: 5px;
        }
    </style>
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
                <?php
                $d = \Carbon\Carbon::parse($first_date)->addDays($i);
                $ad = $data->where('day', intval($d->format('d')))->first();
                ?>
                <tr>
                    <td>{{$d->format('d-M-Y')}}</td>
                    @if($ad)
                        <td>
                            <span class="text-success count-box">Total Present-{{$ad->total_present}}</span>
                            <span class="text-danger count-box">Total Absent-{{$ad->total_absent}}</span>
                            <span class="text-warning count-box">Total Leave-{{$ad->total_leave}}</span>
                        </td>
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
                <th>Attendance Status</th>
                <th style="width: 50px">Action</th>
            </tr>
            @for($i=0;$i<\Carbon\Carbon::parse($first_date)->daysInMonth;$i++)
                <?php $ad = $data->where('day', $i + 1)->first(); ?>
                <tr>
                    <td>{{\Carbon\Carbon::parse($first_date)->addDays($i)->format('d-M-Y')}}</td>
                    @if($ad)
                        <td>
                            @if($ad->is_present)
                                <span class='att-label label-success'>Present</span>
                            @elseif(!$ad->is_present&&!$ad->is_leave)
                                <span class='att-label label-danger'>Absent</span>
                            @elseif($ad->is_leave)
                                <span class='att-label label-warning'>Leave</span>
                            @endif
                        </td>
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
    @endif
</div>