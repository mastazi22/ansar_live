
<div class="table-responsive">
    @if($salary_sheet->generated_type=='salary')
        <table class="table table-condensed table-bordered">
            <caption style="padding: 0 10px">
                <h4 style="    box-shadow: 1px 1px 1px #c5bfbf;padding: 10px 0;line-height: 25px;"
                    class="text-bold text-center">
                    Kpi name : {{$salary_sheet->kpi->kpi_name}}<br>Month : {{\Carbon\Carbon::parse($salary_sheet->generated_for_month)->format("F, Y")}}
                </h4>
            </caption>
            <tr>
                <th>SL. No</th>
                <th>Ansar ID</th>
                <th>Name</th>
                <th>Rank</th>
                <th>Total Working Days</th>
                <th>Total Salary</th>
                <th>Welfare Fund</th>
                <th>Regimental Fund</th>
                <th>Revenue Stamp</th>
                <th>AVUB Share</th>
                <th>Net Amount</th>
            </tr>
            <?php $i = 0;?>
            @forelse($salary_sheet->data as $data)
                <tr>
                    <td>{{++$i}}</td>
                    <td>{{$data['ansar_id']}}</td>
                    <td>{{$data['ansar_name']}}</td>
                    <td>{{$data['ansar_rank']}}</td>
                    <td>{{$data['total_present']+$data['total_leave']}}</td>
                    <td>{{$data['total_amount']}}</td>
                    <td>{{$data['welfare_fee']}}</td>
                    <td>{{$data['reg_amount']}}</td>
                    <td>{{$data['revenue_stamp']}}</td>
                    <td>{{$data['share_fee']}}</td>
                    <td>{{$data['net_amount']}}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="11" class="bg-warning">No attendance data available for this month</td>
                </tr>
            @endforelse
            @if(count($salary_sheet->data)>0)
                <tr>
                    <th colspan="9" class="text-right">
                        {{$salary_sheet->kpi->details->with_weapon?"20% of daily allowance":"15% of daily allowance"}}:
                    </th>
                    <td colspan="2">
                        {{$salary_sheet->summery['extra']}}
                    </td>
                </tr>
            @endif
        </table>
        @if(count($salary_sheet->data)>0)
            <h3 class="text-center">Summary</h3>
            <table class="table table-bordered table-condensed">
                <tr>
                    <th>{{$salary_sheet->kpi->details->with_weapon?"20% of daily allowance":"15% of daily allowance"}}</th>
                    <th>Total Welfare Fee</th>
                    <th>Total Regimental Fee</th>
                    <th>Total Revenue Stamp</th>
                    <th>Total Share Fee</th>
                    <th>Total Net Salary</th>
                    <th>Total Amount Need To Deposit</th>
                    <th>Total Min Amount Need To
                        Deposit<br>(without {{$salary_sheet->kpi->details->with_weapon?"20% of daily allowance":"15% of daily allowance"}})
                    </th>
                </tr>
                <tr>
                    <td>
                        {{$salary_sheet->summery['extra']}}
                    </td>
                    <td>
                        {{$salary_sheet->summery['welfare_fee']}}
                    </td>
                    <td>
                        {{$salary_sheet->summery['reg_amount']}}
                    </td>
                    <td>
                        {{$salary_sheet->summery['revenue_stamp']}}
                    </td>
                    <td>
                        {{$salary_sheet->summery['share_amount']}}
                    </td>
                    <td>
                        {{$salary_sheet->summery['total_net_amount']}}
                    </td>
                    <td>
                        {{$salary_sheet->summery['total_max_amount']}}
                    </td>
                    <td>
                        {{$salary_sheet->summery['total_min_amount']}}
                    </td>
                </tr>
            </table>
        @endif
    @endif
</div>