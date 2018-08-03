@if($type=='salary')
    <table class="table table-condensed table-bordered">
        <tr>
            <th>SL. No</th>
            <th>ID NO</th>

            <th>Rank</th>
            <th>Name</th>
            <th>Account No</th>
            <th>Net Amount</th>
            <th>Guard Name</th>
            <th>Month</th>
        </tr>
        <?php $i=0;?>
        @foreach($datas as $data)
            {{--{{var_dump($data)}}--}}
            <tr>
                <td>{{++$i}}</td>
                <td>{{$data['ansar_id']}}</td>
                <td>{{$data['ansar_rank']}}</td>
                <td>{{$data['ansar_name']}}</td>
                <td>{{$data['account_no']}}</td>
                <td>{{$data['net_amount']}}</td>
                <td>{{$data['kpi_name']}}</td>
                <td>{{$data['month']}}</td>
            </tr>
        @endforeach
    </table>
    @else
    <table class="table table-condensed table-bordered">
        <tr>
            <th>SL. No</th>
            <th>ID NO</th>

            <th>Rank</th>
            <th>Name</th>
            <th>Account No</th>
            <th>Net Amount</th>
            <th>Guard Name</th>
            <th>Bonus</th>
        </tr>
        <?php $i=0;?>
        @foreach($datas as $data)
            {{--{{var_dump($data)}}--}}
            <tr>
                <td>{{++$i}}</td>
                <td>{{$data['ansar_id']}}</td>
                <td>{{$data['ansar_rank']}}</td>
                <td>{{$data['ansar_name']}}</td>
                <td>{{$data['account_no']}}</td>
                <td>{{$data['net_amount']}}</td>
                <td>{{$data['kpi_name']}}</td>
                <td>{{$data['bonus_for']}}</td>
            </tr>
        @endforeach
    </table>
@endif