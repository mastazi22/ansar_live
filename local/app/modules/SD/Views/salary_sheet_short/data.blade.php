{!! Form::open(['route'=>'SD.salary_management_short.store']) !!}
{!! Form::hidden('kpi_id',$kpi_id) !!}
{!! Form::hidden('generated_for_month',$for_month) !!}
<div class="table-responsive">
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
            <th>Total Duration</th>
            <th>Total Daily Fee</th>
            <th>Other</th>
            <th>Deduct</th>
            <th>Net Amount</th>
        </tr>
        <?php $i=0;?>
        @forelse($datas as $data)
            {!! Form::hidden("salary_data[$i][kpi_name]",$kpi_name) !!}
            {!! Form::hidden("salary_data[$i][ansar_id]",$data['ansar_id']) !!}
            {!! Form::hidden("salary_data[$i][ansar_name]",$data['ansar_name']) !!}
            {!! Form::hidden("salary_data[$i][ansar_rank]",$data['ansar_rank']) !!}
            {!! Form::hidden("salary_data[$i][total_duration]",$data['total_duration']) !!}
            {!! Form::hidden("salary_data[$i][total_daily_fee]",$data['total_daily_fee']) !!}
            {!! Form::hidden("salary_data[$i][other_fee]",$data['other_fee']) !!}
            {!! Form::hidden("salary_data[$i][deduct_fee]",$data['deduct_fee']) !!}
            {!! Form::hidden("salary_data[$i][month]",$for_month) !!}
            {!! Form::hidden("salary_data[$i][net_amount]",$data['total_daily_fee']+$data['other_fee']-$data['deduct_fee']) !!}
            {!! Form::hidden("salary_data[$i][account_no]",$data['account_no']) !!}
            {!! Form::hidden("salary_data[$i][bank_type]",$data['bank_type']) !!}
            <tr>
                <td>{{$i+1}}</td>
                <td>{{$data['ansar_id']}}</td>
                <td>{{$data['ansar_name']}}</td>
                <td>{{$data['ansar_rank']}}</td>
                <td>{{$data['total_duration']}}</td>
                <td>{{$data['total_daily_fee']}}</td>
                <td>{{$data['other_fee']}}</td>
                <td>{{$data['deduct_fee']}}</td>
                <td>
                    {{$data['total_daily_fee']+$data['other_fee']-$data['deduct_fee']}}
                </td>
            </tr>
            <?php $i++;?>
        @empty
            <tr>
                <td colspan="10" class="bg-warning">No VDP found in this kpi</td>
            </tr>
        @endforelse
    </table>
</div>
<button type="submit" class="btn btn-primary pull-right">Confirm & Generate Salary Sheet</button>
{!! Form::close() !!}