<table class="table table-condensed table-bordered">
    <caption style="padding: 0 10px">
        <h4 style="    box-shadow: 1px 1px 1px #c5bfbf;padding: 10px 0;line-height: 25px;" class="text-bold text-center">
            Bonus of<br>{{$kpi_name}}<br>Based on <span class="text text-danger">{{\Carbon\Carbon::parse($for_month)->format("F, Y")}}</span> Attendance
        </h4>
    </caption>
    <tr>
        <th>SL. No</th>
        <th>KPI Name</th>
        <th>Generated Date</th>
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