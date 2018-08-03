<?php $i = (intVal($payment_history->currentPage() - 1) * $payment_history->perPage()) + 1; ?>
<div class="table-responsive">

    <table class="table table-bordered table-condensed">
        <caption><span style="font-size: 20px;">Total Payment({{$payment_history->total()}})</span>
            <a href="{{URL::route('SD.kpi_payment.create')}}" class="btn btn-primary btn-xs pull-right">
                <i class="fa fa-plus"></i>&nbsp;Add new payment
            </a>
        </caption>

        <tr>
            <th>#</th>
            <th>KPI name</th>
            <th>Demand Sheet No.</th>
            <th>KPI Division</th>
            <th>KPI District</th>
            <th>KPI thana</th>
            <th>Paid Amount</th>
            <th>Uploaded date</th>
            <th>Document</th>
            <th>Action</th>

        </tr>

        @if(count($payment_history))
            @foreach($payment_history as $info)
                <tr>
                    <td>{{$i++}}</td>
                    <td>{{$info->kpi->kpi_name}}</td>
                    <td>{{$info->demand->memorandum_no}}</td>
                    <td>{{$info->kpi->division->division_name_bng}}</td>
                    <td>{{$info->kpi->unit->unit_name_bng}}</td>
                    <td>{{$info->kpi->thana->thana_name_bng}}</td>
                    <td>{{$info->paid_amount}}</td>
                    <td>{{\Carbon\Carbon::parse($info->created_at)->format('d-M-Y')}}</td>
                    <td><a href="#" data-url="{{URL::route('SD.kpi_payment.show_doc',$info->id)}}" class="show-doc">Click here</a></td>

                    <td>
                        <a href="{{URL::route('SD.kpi_payment.edit',$info->id)}}" class="btn btn-xs btn-primary">
                            <i class="fa fa-edit"></i>&nbsp;Edit
                        </a>
                    </td>
                </tr>
            @endforeach
        @else
            <tr>
                <td colspan="10" class="bg-warning">
                    No Payment History Available
                </td>
            </tr>
        @endif

    </table>
</div>
@if($payment_history->total()>$payment_history->perPage())
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
            {{$payment_history->render()}}
        </div>
    </div>
@endif
<script>
    $(document).ready(function(){
        $(".show-doc").on('click',function (e) {
            e.preventDefault();
            e.stopPropagation();
            var s = $(this).attr("data-url")
            $(".backdrop").removeClass("hidden").find('img').attr('src',s)

        })
        $(".backdrop").on('click',function (e) {
            $(".backdrop").addClass("hidden")

        })
    })
</script>
