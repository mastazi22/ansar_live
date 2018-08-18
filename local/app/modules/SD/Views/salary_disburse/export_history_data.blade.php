<?php $i = 1; ?>
<div class="table-responsive">

    <table class="table table-bordered table-condensed">
        <caption><span style="font-size: 20px;">Total({{count($histories)}})</span>
        </caption>

        <tr>
            <th>#</th>
            <th>KPI name</th>
            <th>KPI Division</th>
            <th>KPI Unit</th>
            <th>KPI Thana</th>
            <th>Disburse Type</th>
            <th>Disburse For Month</th>
            <th>Total Salary</th>
            <th>Total AVUB Share</th>
            <th>Total Welfare</th>
            <th>Total Regimental</th>
            <th>Total Stamp</th>
            <th>Total 15%/20%</th>

        </tr>

        @if(count($histories))
            @foreach($histories as $history)
                <tr>
                    <td>{{$i++}}</td>
                    <td>{{$history->salarySheet->kpi->kpi_name}}</td>
                    <td>
                        {{$history->salarySheet->kpi->division->division_name_bng}}
                    </td>
                    <td>
                        {{$history->salarySheet->kpi->unit->unit_name_bng}}
                    </td>
                    <td>
                        {{$history->salarySheet->kpi->thana->thana_name_bng}}
                    </td>
                    <td>{{$history->salarySheet->generated_type}}</td>
                    <td>{{$history->salarySheet->generated_for_month}}</td>
                    <td>{{$history->salarySheet->salaryHistory->sum('amount')}}</td>
                    <td>{{$history->share_account_amount}}</td>
                    <td>{{$history->welfare_account_amount}}</td>
                    <td>{{$history->regimental_account_amount}}</td>
                    <td>{{$history->stampAmount()}}</td>
                    <td>{!!$history->extra_amount_include?$history->extra_amount:'<strong style="color:red">Not Include</strong>'!!}</td>
                </tr>
            @endforeach
        @else
            <tr>
                <td colspan="11" class="bg-warning">
                    No Payment History Available
                </td>
            </tr>
        @endif

    </table>
</div>
