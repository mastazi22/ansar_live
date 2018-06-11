{!! Form::open(['route'=>'SD.attendance.store']) !!}

<div class="table-responsive">
    @if(isset($date['day'])&&$date['day']&&$date['day']>0)
        <table class="table table-bordered table-condensed">
            <caption>
                <caption style="padding: 0 10px">
                    <h4 style="    box-shadow: 1px 1px 1px #c5bfbf;padding: 10px 0;" class="text-bold text-center">
                        Attendance of "{{$data->kpi_name}}"
                        <br>{{\Carbon\Carbon::create($date['year'],$date['month'],$date['day'])->format("d F, Y")}}
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
    @else
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
                        {{--{!! Form::hidden('attendance_data['.($i-1).'][id]',$attendance->id) !!}
                        {!! Form::hidden('attendance_data['.($i-1).'][is_attendance_taken]',1) !!}--}}
                    </td>
                    <td>{{$attendance->ansar_id}}</td>
                    <td>{{$attendance->ansar->ansar_name_bng}}</td>
                    <td>
                        <input readonly type="text" multi-date-picker month="{{$date['month']}}" typee="present" year="{{$date['year']}}" selected-dates="selectedDates[{{$i-1}}].present" disabled-dates="disabledDates[{{$i-1}}].leave">
                    </td>
                    <td>
                        <input readonly type="text" multi-date-picker month="{{$date['month']}}" typee="leave" year="{{$date['year']}}" selected-dates="selectedDates[{{$i-1}}].leave" disabled-dates="disabledDates[{{$i-1}}].present">
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
    @endif
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
</div>
<button type="submit" class="btn btn-primary pull-right">Confirm Attendance</button>
{!! Form::close() !!}

