{!! Form::open(['route'=>'SD.attendance.store']) !!}

<div class="table-responsive">
    @if((isset($date['day'])&&$date['day']&&$date['day']>0)&&(!isset($date['ansar_id'])||!$date['ansar_id']))
        {!! Form::hidden("type",'day_wise') !!}
        <table class="table table-bordered table-condensed">
            @if(!isset($date['ansar_id'])||!$date['ansar_id'])
                <caption style="padding: 0 10px">
                    <h4 style="    box-shadow: 1px 1px 1px #c5bfbf;padding: 10px 0;" class="text-bold text-center">
                        Attendance of "{{$data->kpi_name}}"
                        <br>{{\Carbon\Carbon::create($date['year'],$date['month'],$date['day'])->format("d F, Y")}}
                    </h4>
                </caption>
            @else

                <caption style="padding: 0 10px">
                    <h4 style="    box-shadow: 1px 1px 1px #c5bfbf;padding: 10px 0;" class="text-bold text-center">
                        Attendance of (ID:{{$date['ansar_id']}}
                        )<br>{{\Carbon\Carbon::create($date['year'],$date['month'])->format("d F, Y")}}
                    </h4>
                </caption>
            @endif
            <tr>
                <th>SL. NO</th>
                <th>Ansar ID</th>
                <th>Name</th>
                <th>Is Present</th>
                <th>Is Leave</th>
            </tr>
            <?php $i = 0; ?>

            @forelse($data->attendance as $attendance)
                <tr>
                    <td>{{++$i}}
                        {!! Form::hidden('attendance_data['.($i-1).'][id]',$attendance->id) !!}
                        {!! Form::hidden('attendance_data['.($i-1).'][is_attendance_taken]',1) !!}
                    </td>
                    <td>{{$attendance->ansar_id}}</td>
                    <td>{{$attendance->ansar->ansar_name_bng}}</td>
                    <td>
                        <div class="styled-checkbox">
                            <input id="is_present_{{$i}}" class="is_present"
                                   name="attendance_data[{{$i-1}}][is_present]" type="checkbox" value="1">
                            <label for="is_present_{{$i}}"></label>
                        </div>
                    </td>
                    <td>
                        <div class="styled-checkbox">
                            <input id="is_leave_{{$i}}" class="is_leave" name="attendance_data[{{$i-1}}][is_leave]"
                                   type="checkbox" value="1">
                            <label for="is_leave_{{$i}}"></label>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="bg-warning">
                        No Data Available
                    </td>
                </tr>
            @endforelse

        </table>
        <script>
            $(document).ready(function () {
                $(".is_leave").on('change', function () {
                    if ($(this).is(":checked")) {

                        $(this).parents('tr').find(".is_present").prop('checked', false).prop('disabled', true);
                    } else {
                        $(this).parents('tr').find(".is_present").prop('disabled', false);
                    }
                })
                $(".is_present").on('change', function () {
                    if ($(this).is(":checked")) {
                        $(this).parents('tr').find(".is_leave").prop('checked', false).prop('disabled', true);
                    } else {
                        $(this).parents('tr').find(".is_leave").prop('disabled', false);
                    }
                })
            })
        </script>
    @elseif(!isset($date['ansar_id'])||!$date['ansar_id'])
        {!! Form::hidden("type",'month_wise') !!}
        {!! Form::hidden("month",$date['month']) !!}
        {!! Form::hidden('kpi_id',$data->id) !!}
        <table class="table table-bordered table-condensed">
            <caption>
                <caption style="padding: 0 10px">
                    <h4 style="    box-shadow: 1px 1px 1px #c5bfbf;padding: 10px 0;" class="text-bold text-center">
                        Attendance of "{{$data->kpi_name}}"
                        <br>{{\Carbon\Carbon::create($date['year'],$date['month'])->format("F, Y")}}
                    </h4>
                </caption>
            </caption>
            <tr>
                <th>SL. NO</th>
                <th>Ansar ID</th>
                <th>Name</th>
                <th>Is Present</th>
                <th>Is Leave</th>
            </tr>
            <?php $i = 0; ?>

            @forelse($data->attendance as $attendance)

                <tr ng-init="initCalenderDate('{{$attendance->dates}}',{{$i}})">
                    <td>{{++$i}}
                        {!! Form::hidden('attendance_data['.($i-1).'][ansar_id]',$attendance->ansar_id) !!}

                    </td>
                    <td>{{$attendance->ansar_id}}</td>
                    <td>{{$attendance->ansar->ansar_name_bng}}</td>
                    <td>
                        <input id="is_present_{{$i}}" name="attendance_data[{{$i-1}}][present_dates]" readonly
                               type="text" multi-date-picker month="{{$date['month']}}" typee="present"
                               year="{{$date['year']}}" selected-dates="selectedDates[{{$i-1}}].present"
                               disable-elem="#is_leave_{{$i}}" disabled-dates="disabledDates[{{$i-1}}].leave">
                    </td>
                    <td>
                        <input id="is_leave_{{$i}}" name="attendance_data[{{$i-1}}][leave_dates]" readonly type="text"
                               multi-date-picker month="{{$date['month']}}" typee="leave" year="{{$date['year']}}"
                               selected-dates="selectedDates[{{$i-1}}].leave" disable-elem="#is_present_{{$i}}"
                               disabled-dates="disabledDates[{{$i-1}}].present">
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="bg-warning">
                        No Data Available
                    </td>
                </tr>
            @endforelse

        </table>
    @else
        {!! Form::hidden("type",'day_wise') !!}
        <table class="table table-bordered table-condensed">
            <caption style="padding: 0 10px">
                <h4 style="    box-shadow: 1px 1px 1px #c5bfbf;padding: 10px 0;" class="text-bold text-center">
                    Attendance of (ID:{{$date['ansar_id']}}
                    )<br>{{\Carbon\Carbon::create($date['year'],$date['month'])->format("F, Y")}}
                </h4>
            </caption>
            <tr>
                <th>SL. NO</th>
                <th>Attendance Date</th>
                <th>Is Present</th>
                <th>Is Leave</th>
            </tr>
            <?php $i = 0; ?>

            @forelse($data->attendance as $attendance)
                <tr>
                    <td>{{++$i}}
                        {!! Form::hidden('attendance_data['.($i-1).'][id]',$attendance->id) !!}
                        {!! Form::hidden('attendance_data['.($i-1).'][is_attendance_taken]',1) !!}
                    </td>
                    <td>{{\Carbon\Carbon::create($attendance->year,$attendance->month,$attendance->day)->format('d-M-Y')}}</td>
                    <td>
                        <div class="styled-checkbox">
                            <input id="is_present_{{$i}}" class="is_present"
                                   name="attendance_data[{{$i-1}}][is_present]" type="checkbox" value="1">
                            <label for="is_present_{{$i}}"></label>
                        </div>
                    </td>
                    <td>
                        <div class="styled-checkbox">
                            <input id="is_leave_{{$i}}" class="is_leave" name="attendance_data[{{$i-1}}][is_leave]"
                                   type="checkbox" value="1">
                            <label for="is_leave_{{$i}}"></label>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="bg-warning">
                        No Data Available
                    </td>
                </tr>
            @endforelse

        </table>
        <script>
            $(document).ready(function () {
                $(".is_leave").on('change', function () {
                    if ($(this).is(":checked")) {

                        $(this).parents('tr').find(".is_present").prop('checked', false).prop('disabled', true);
                    } else {
                        $(this).parents('tr').find(".is_present").prop('disabled', false);
                    }
                })
                $(".is_present").on('change', function () {
                    if ($(this).is(":checked")) {
                        $(this).parents('tr').find(".is_leave").prop('checked', false).prop('disabled', true);
                    } else {
                        $(this).parents('tr').find(".is_leave").prop('disabled', false);
                    }
                })
            })
        </script>
    @endif

</div>
<button type="submit" class="btn btn-primary pull-right">Confirm Attendance</button>
{!! Form::close() !!}
<div class="panel-group" id="accordion">
    <div class="panel panel-default" ng-repeat="att in attData">
        <div class="panel-heading">
            <h4 class="panel-title">
                <a data-toggle="collapse" data-parent="#accordion" href="#kpi_[[att.kpi_id]]">
                    [[att.kpi_name]]</a>
            </h4>
        </div>
        <div id="kpi_[[att.kpi_id]]" class="panel-collapse collapse" ng-class="{'in':$index==0}">
            <div class="panel-body">
                <div class="row">
                    <div class="col-sm-4">
                        <h3 class="text-center">Select date for present</h3>
                        <calender enabled-dates="att.dates" disabled-dates="param.disabledDates[$index].present"  selected-dates="param.selectedDates[$index].present" show-only-current-year="true" show-only-month="6"></calender>
                    </div>
                    <div class="col-sm-4">
                        <h3 class="text-center">Select date for absent</h3>
                        <calender enabled-dates="att.dates"  disabled-dates="param.disabledDates[$index].absent"  selected-dates="param.selectedDates[$index].absent" show-only-current-year="true" show-only-month="6"></calender>
                    </div>
                    <div class="col-sm-4">
                        <h3 class="text-center">Select date for leave</h3>
                        <calender enabled-dates="att.dates" disabled-dates="param.disabledDates[$index].leave"  selected-dates="param.selectedDates[$index].leave" show-only-current-year="true" show-only-month="6"></calender>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

