<table class="table table-condensed table-bordered">
    <tr>
        <th>SL. No</th>
        <th>Name</th>
        <th>Account No</th>
        <th>Amount</th>
        <th>Month</th>
    </tr>
    <?php $i=0;?>
    @foreach($datas as $data)
        {{--{{var_dump($data)}}--}}
        <tr>
            <td>{{++$i}}</td>
            <td>{{$data['account_name']}}</td>
            <td>{{$data['account_no']}}</td>
            <td>{{$data['amount']}}</td>
            <td>{{$data['month']}}</td>
        </tr>
    @endforeach
</table>