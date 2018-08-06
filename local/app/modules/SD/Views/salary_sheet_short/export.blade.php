<table class="table table-condensed table-bordered">
        <tr>
            <th>SL. No</th>
            <th>Rank</th>
            <th>Name</th>
            <th>KPI Name</th>

            <th>Account No</th>
            <th>Net Amount</th>
        </tr>
        <?php $i=0;?>
        @foreach($datas as $data)
            {{--{{var_dump($data)}}--}}
            <tr>
                <td>{{++$i}}</td>
                <td>{{$data['ansar_rank']}}</td>
                <td>{{$data['ansar_name']}}</td>
                <td>{{$data['kpi_name']}}</td>
                <td>{{$data['account_no']}}</td>
                <td>{{$data['net_amount']}}</td>
            </tr>
        @endforeach
    </table>