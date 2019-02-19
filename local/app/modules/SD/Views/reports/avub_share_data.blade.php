
<?php $i = (intVal($shares->currentPage() - 1) * $shares->perPage()) + 1; ?>
<div class="table-responsive">

    <table class="table table-bordered table-condensed">
        <caption><span style="font-size: 20px;">Total({{$shares->total()}})</span>
        </caption>

        <tr>
            <th>#</th>
            <th>Ansar ID</th>
            <th>Name</th>
            <th>AVUB Share ID</th>
            <th>Month</th>

        </tr>
        @forelse($shares as $info)
        <tr>
            <td>{{$i++}}</td>
            <td>{{$info->ansar->ansar_id}}<br><span style="color:red;font-size: 12px;font-weight: bold;">Type: </span><span class="label label-info">{{$info->generated_type}}</span></td>
            <td>{{$info->ansar->ansar_name_bng}}</td>
            <td>{{$info->ansar->avub_share_id}}</td>
            <td>{{$info->salarySheet->generated_for_month}}</td>
        </tr>

        @empty
        <tr>
            <td colspan="5" class="bg-warning">
                No share info available
            </td>
        </tr>
            @endforelse

    </table>
</div>
@if($shares->total()>$shares->perPage())
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
    <div class="pull-right" style="margin: -20px 0" paginate ref="loadData(url)">
        {{$shares->render()}}
    </div>
</div>
@endif
