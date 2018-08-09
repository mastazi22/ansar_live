<html>
<head>
    <meta charset="utf-8">
</head>
<body>
<table class="table table-condensed table-bordered">
    <tr>
        <th>SL. No</th>
        <th>Name</th>
        <th>Account No</th>
        <th>Branch</th>
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
            <td>{{$data['branch_name']}}</td>
            <td>{{$data['amount']}}</td>
            <td>{{$data['month']}}</td>
        </tr>
    @endforeach
</table>

</body>
</html>