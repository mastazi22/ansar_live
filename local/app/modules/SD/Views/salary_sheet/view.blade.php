<div class="table-responsive">
    @if($salary_sheet->generated_type=='salary')
        <table class="table table-condensed table-bordered">
            <caption style="padding: 0 10px">
                <h4 style="    box-shadow: 1px 1px 1px #c5bfbf;padding: 10px 0;line-height: 25px;"
                    class="text-bold text-center">
                    Kpi name : {{$salary_sheet->kpi->kpi_name}}<br>Month
                    : {{\Carbon\Carbon::parse($salary_sheet->generated_for_month)->format("F, Y")}}
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
                <th>Bank Account(Preferred)</th>
            </tr>
            <?php $i = 0;?>
            @forelse($salary_sheet->data as $data)
                <?php $account  = $salary_sheet->salaryHistory()->where('ansar_id',$data['ansar_id'])->first()->ansar->account;?>
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
                    <td>{{$account?($account->prefer_choice=="general"?$account->account_no:$account->mobile_bank_account_no):'--'}}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="12" class="bg-warning">No attendance data available for this month</td>
                </tr>
            @endforelse
            @if(count($salary_sheet->data)>0)
                <tr>
                    <th colspan="9" class="text-right">
                        {{$salary_sheet->kpi->details->with_weapon?"20% of daily allowance":"15% of daily allowance"}}:
                    </th>
                    <td colspan="3">
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
                        Deposit<br>(without {{$salary_sheet->kpi->details->with_weapon?"20% of daily allowance":"15% of daily allowance"}}
                        )
                    </th>
                </tr>
                <tr>
                    <td>
                        {{sprintf("%.2f",$salary_sheet->summery['extra'])}}
                    </td>
                    <td>
                        {{sprintf("%.2f",$salary_sheet->summery['welfare_fee'])}}
                    </td>
                    <td>
                        {{sprintf("%.2f",$salary_sheet->summery['reg_amount'])}}
                    </td>
                    <td>
                        {{sprintf("%.2f",$salary_sheet->summery['revenue_stamp'])}}
                    </td>
                    <td>
                        {{sprintf("%.2f",$salary_sheet->summery['share_amount'])}}
                    </td>
                    <td>
                        {{sprintf("%.2f",$salary_sheet->summery['total_net_amount'])}}
                    </td>
                    <td>
                        {{sprintf("%.2f",$salary_sheet->summery['total_max_amount'])}}
                    </td>
                    <td>
                        {{sprintf("%.2f",$salary_sheet->summery['total_min_amount'])}}
                    </td>
                </tr>
            </table>
        @endif
    @else
        <table class="table table-condensed table-bordered">
            <caption style="padding: 0 10px">
                <h4 style="    box-shadow: 1px 1px 1px #c5bfbf;padding: 10px 0;line-height: 25px;"
                    class="text-bold text-center">
                    Bonus of<br>{{$salary_sheet->data[0]["bonus_for"]=="eiduladah"?"ঈদ-উল-আযহা":"ঈদ-উল-ফিতর"}}<br>{{$salary_sheet->kpi->kpi_name}}
                </h4>
            </caption>
            <tr>
                <th>SL. No</th>
                <th>Ansar ID</th>
                <th>Name</th>
                <th>Rank</th>
                <th>Total Bonus</th>
                <th>Net Amount</th>
                <th>Bonus For</th>
            </tr>
            <?php $i = 0;?>
            @forelse($salary_sheet->data as $data)
                <tr>
                    <td>{{++$i}}</td>
                    <td>{{$data['ansar_id']}}</td>
                    <td>{{$data['ansar_name']}}</td>
                    <td>{{$data['ansar_rank']}}</td>
                    <td>{{$data['total_amount']}}</td>
                    <td>
                       {{$data['net_amount']}}
                    </td>
                    <td>{{$data['bonus_for']=="eidulfitr"?"Eid-ul-fitr":"Eid-ul-adah"}}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="bg-warning">No attendance data available for this month</td>
                </tr>
            @endforelse
            @if(count($salary_sheet->data)>0)
                <tr>
                    <th colspan="5" style="text-align: right">Total : </th>
                    <td colspan="2">
                        {{collect($salary_sheet->data)->sum("net_amount")}}
                    </td>
                </tr>
            @endif
        </table>
    @endif
</div>