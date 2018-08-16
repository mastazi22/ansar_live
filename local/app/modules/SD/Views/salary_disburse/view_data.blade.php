<style>
    table td{
        vertical-align: middle !important;
        /*white-space: nowrap !important;*/
    }
</style>

<?php $i = (intVal($sheets->currentPage() - 1) * $sheets->perPage()) + 1; ?>
<div class="table-responsive">

    <table class="table table-bordered table-condensed">
        <caption><span style="font-size: 20px;">Total({{$sheets->total()}})</span>
        </caption>

        <tr>
            <th>#</th>
            <th>KPI name</th>
            <th>KPI Division</th>
            <th>KPI District</th>
            <th>KPI thana</th>
            <th>Generate For Month</th>
            <th>Total Ansar</th>
            <th>Total Amount</th>
            <th>Total Amount<br><span style="font-size: 12px;white-space: nowrap;color: red">(without 15-20%)</span></th>
            <th>Deposit status</th>
            <th>Action</th>

        </tr>

        @if(count($sheets))
            @foreach($sheets as $info)
                <tr>
                    <td>{{$i++}}</td>
                    <td>{{$info->kpi->kpi_name}}<br><span style="color:red;font-size: 12px;font-weight: bold;">Type: </span><span class="label label-info">{{$info->generated_type}}</span></td>
                    <td>{{$info->kpi->division->division_name_bng}}</td>
                    <td>{{$info->kpi->unit->unit_name_bng}}</td>
                    <td>{{$info->kpi->thana->thana_name_bng}}</td>
                    <td>{{$info->generated_for_month}}</td>
                    <td>{{$info->salaryHistory->count()}}</td>
                    <td>{{$info->generated_type=="salary"?$info->summery["total_max_amount"]:$info->salaryHistory->sum("amount")}}</td>
                    <td>{{$info->generated_type=="salary"?$info->summery["total_min_amount"]."(-".($info->kpi->details->is_special?$info->kpi->details->special_amount:($info->kpi->details->with_weapon?20:15)).")%":"Not Applicable"}}</td>
                    <td>
                        @if(!$info->deposit)
                            <span class="label label-danger">Not Available</span>
                        @elseif($info->generated_type=="salary"&&$info->deposit->paid_amount<$info->summery["total_max_amount"])
                            <span class="label label-warning">Partial</span>
                        @elseif($info->generated_type=="bonus"&&$info->deposit->paid_amount<$info->salaryHistory->sum('amount'))
                            <span class="label label-warning">Partial</span>
                        @else
                            <span class="label label-success">Fully Deposit</span>
                        @endif
                    </td>
                    <td>
                        <button class="btn btn-primary btn-xs" ng-click="viewDetails('{{URL::route("SD.salary_disburse.show",\Illuminate\Support\Facades\Crypt::encrypt($info->id))}}')">
                            <i class="fa fa-eye"></i>&nbsp;View Details
                        </button>
                    </td>
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
@if($sheets->total()>$sheets->perPage())
    <div style="overflow: hidden">
        <div class="pull-left">
            <select name="" id="" ng-model="param.limit" ng-change="loadPage()">
                <option value="30">30</option>
                <option value="100">100</option>
                <option value="200">200</option>
                <option value="300">300</option>
                <option value="500">500</option>
            </select>
        </div>
        <div class="pull-right" style="margin: -20px 0" paginate ref="loadPage(url)">
            {{$sheets->render()}}
        </div>
    </div>
@endif
