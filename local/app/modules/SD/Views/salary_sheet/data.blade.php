{!! Form::open(['route'=>'SD.salary_management.store']) !!}
{!! Form::hidden('kpi_id',$kpi_id) !!}
{!! Form::hidden('generated_for_month',$for_month) !!}
{!! Form::hidden('generated_type',$generated_type) !!}
<div class="table-responsive">
    @if($generated_type=='salary')
        <table class="table table-condensed table-bordered">
            <caption style="padding: 0 10px">
                <h4 style="    box-shadow: 1px 1px 1px #c5bfbf;padding: 10px 0;line-height: 25px;" class="text-bold text-center">
                    Salary of<br>{{$kpi_name}}<br>{{\Carbon\Carbon::parse($for_month)->format("F, Y")}}
                </h4>
            </caption>
            <tr>
                <th>SL. No</th>
                <th>Ansar ID</th>
                <th>Name</th>
                <th>Rank</th>
                <th>Total Present</th>
                <th>Total Leave(paid)</th>
                <th>Total Absent</th>
                <th>Total Salary</th>
                <th>Welfare Fee</th>
                <th>Share Fee</th>
                <th>Net Amount</th>
            </tr>
            <?php $i=0;?>
            @forelse($datas as $data)
                {!! Form::hidden("attendance_data[$i][kpi_name]",$kpi_name) !!}
                {!! Form::hidden("attendance_data[$i][ansar_id]",$data['ansar_id']) !!}
                {!! Form::hidden("attendance_data[$i][ansar_name]",$data['ansar_name']) !!}
                {!! Form::hidden("attendance_data[$i][ansar_rank]",$data['ansar_rank']) !!}
                {!! Form::hidden("attendance_data[$i][net_amount]",$data['total_amount']-$data['welfare_fee']) !!}
                {!! Form::hidden("attendance_data[$i][total_amount]",$data['total_amount']) !!}
                {!! Form::hidden("attendance_data[$i][total_present]",$data['total_present']) !!}
                {!! Form::hidden("attendance_data[$i][total_leave]",$data['total_leave']) !!}
                {!! Form::hidden("attendance_data[$i][welfare_fee]",$data['welfare_fee']) !!}
                {!! Form::hidden("attendance_data[$i][share_fee]",$data['share_amount']) !!}
                {!! Form::hidden("attendance_data[$i][month]",$for_month) !!}
                {!! Form::hidden("attendance_data[$i][account_no]",$data['account_no']) !!}
                {!! Form::hidden("attendance_data[$i][bank_type]",$data['bank_type']) !!}
                <tr>
                    <td>{{++$i}}</td>
                    <td>{{$data['ansar_id']}}</td>
                    <td>{{$data['ansar_name']}}</td>
                    <td>{{$data['ansar_rank']}}</td>
                    <td>{{$data['total_present']}}</td>
                    <td>{{$data['total_leave']}}</td>
                    <td>{{$data['total_absent']}}</td>
                    <td>{{$data['total_amount']}}</td>
                    <td>{{$data['welfare_fee']}}</td>
                    <td>{{$data['share_amount']}}</td>
                    <td>{{$data['total_amount']-($data['welfare_fee']+$data['share_amount'])}}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" class="bg-warning">No attendance data available for this month</td>
                </tr>
            @endforelse
        </table>
        @else
        <table class="table table-condensed table-bordered">
            <caption style="padding: 0 10px">
                <h4 style="    box-shadow: 1px 1px 1px #c5bfbf;padding: 10px 0;line-height: 25px;" class="text-bold text-center">
                    Bonus of<br>{{$kpi_name}}<br>Based on <span class="text text-danger">{{\Carbon\Carbon::parse($for_month)->format("F, Y")}}</span> Attendance
                </h4>
            </caption>
            <tr>
                <th>SL. No</th>
                <th>Ansar ID</th>
                <th>Name</th>
                <th>Rank</th>
                <th>Total Present</th>
                <th>Total Leave(paid)</th>
                <th>Total Absent</th>
                <th>Total Bonus</th>
                <th>Net Amount</th>
                <th>Bonus For</th>
            </tr>
            <?php $i=0;?>
            @forelse($datas as $data)
                {!! Form::hidden("attendance_data[$i][kpi_name]",$kpi_name) !!}
                {!! Form::hidden("attendance_data[$i][ansar_id]",$data['ansar_id']) !!}
                {!! Form::hidden("attendance_data[$i][ansar_name]",$data['ansar_name']) !!}
                {!! Form::hidden("attendance_data[$i][ansar_rank]",$data['ansar_rank']) !!}
                {!! Form::hidden("attendance_data[$i][net_amount]",$data['net_amount']) !!}
                {!! Form::hidden("attendance_data[$i][total_amount]",$data['total_amount']) !!}
                {!! Form::hidden("attendance_data[$i][total_present]",$data['total_present']) !!}
                {!! Form::hidden("attendance_data[$i][total_leave]",$data['total_leave']) !!}
                {!! Form::hidden("attendance_data[$i][bonus_for]",$data['bonus_for']=="eidulfitr"?"Eid-ul-fitr":"Eid-ul-adah") !!}
                {!! Form::hidden("attendance_data[$i][month]",$for_month) !!}
                {!! Form::hidden("attendance_data[$i][account_no]",$data['account_no']) !!}
                <tr>
                    <td>{{++$i}}</td>
                    <td>{{$data['ansar_id']}}</td>
                    <td>{{$data['ansar_name']}}</td>
                    <td>{{$data['ansar_rank']}}</td>
                    <td>{{$data['total_present']}}</td>
                    <td>{{$data['total_leave']}}</td>
                    <td>{{$data['total_absent']}}</td>
                    <td>{{$data['total_amount']}}</td>
                    <td>{{$data['net_amount']}}</td>
                    <td>{{$data['bonus_for']=="eidulfitr"?"Eid-ul-fitr":"Eid-ul-adah"}}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" class="bg-warning">No attendance data available for this month</td>
                </tr>
            @endforelse
        </table>
    @endif
</div>
<button type="submit" class="btn btn-primary pull-right">Confirm & Generate Salary Sheet</button>
{!! Form::close() !!}