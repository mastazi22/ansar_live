<html>
<head>
    <meta charset="utf-8">
</head>
<body>
<table border="1" cellpadding="0" cellspacing="0">
    <tr>
        <th>SL. No</th>
        <th>Name</th>
        <th>Account No</th>
        <th>Bank Name</th>
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
            <td>{{$data['bank_name']}}</td>
            <td>{{$data['branch_name']}}</td>
            <td>{{$data['amount']}}</td>
            <td>{{$data['month']}}</td>
        </tr>
    @endforeach
</table>

</body>
</html>