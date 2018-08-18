<?php $i = (intVal($histories->currentPage() - 1) * $histories->perPage()) + 1; ?>
<div class="table-responsive">

    <table class="table table-bordered table-condensed">
        <caption><span style="font-size: 20px;">Total({{$histories->total()}})</span>
            <a href="{{URL::route('SD.salary_disburse.index')}}?type=export" class="btn btn-primary btn-xs pull-right">
                <i class="fa fa-file-excel-o"></i>&nbsp;Export Data
            </a>
        </caption>

        <tr>
            <th>#</th>
            <th>KPI name</th>
            <th>KPI Address</th>
            <th>Disburse Type</th>
            <th>Disburse For Month</th>
            <th>Total Salary</th>
            <th>Total AVUB Share</th>
            <th>Total Welfare</th>
            <th>Total Regimental</th>
            <th>Total Stamp</th>
            <th>Total 15%/20%</th>
            {{--<th>Action</th>--}}

        </tr>

        @if(count($histories))
            @foreach($histories as $history)
                <tr>
                    <td>{{$i++}}</td>
                    <td>{{$history->salarySheet->kpi->kpi_name}}</td>
                    <td>
                        <strong>Division:</strong>{{$history->salarySheet->kpi->division->division_name_bng}}<br>
                        <strong>Unit:</strong>{{$history->salarySheet->kpi->unit->unit_name_bng}}<br>
                        <strong>Thana:</strong>{{$history->salarySheet->kpi->thana->thana_name_bng}}<br>
                    </td>
                    <td>{{$history->salarySheet->generated_type}}</td>
                    <td>{{$history->salarySheet->generated_for_month}}</td>
                    <td>{{$history->salarySheet->salaryHistory->sum('amount')}}</td>
                    <td>{{$history->share_account_amount}}</td>
                    <td>{{$history->welfare_account_amount}}</td>
                    <td>{{$history->regimental_account_amount}}</td>
                    <td>{{$history->stampAmount()}}</td>
                    <td>{!!$history->extra_amount_include?$history->extra_amount:'<strong style="color:red">Not Include</strong>'!!}</td>

                    {{--<td>--}}
                        {{--<button class="btn btn-primary btn-xs" >--}}
                            {{--<i class="fa fa-eye"></i>&nbsp;View Details--}}
                        {{--</button>--}}
                    {{--</td>--}}
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
@if($histories->total()>$histories->perPage())
    <div style="overflow: hidden">
        <div class="pull-left">
            <select name="" id="" ng-model="param.limit" ng-change="loadData()">
                <option value="30">30</option>
                <option value="100">100</option>
                <option value="200">200</option>
                <option value="300">300</option>
                <option value="500">500</option>
            </select>
        </div>
        <div class="pull-right" style="margin: -20px 0" paginate ref="loadData(url)">
            {{$histories->render()}}
        </div>
    </div>
@endif
