<html>
<head>
    <meta charset="utf-8">
    <style>
        @font-face {
            font-family: syamrupali;
            src: url('{{asset('dist/fonts/vrindab.ttf')}}');
        }

        .kpi_name {
            font-family: syamrupali !important;
        }
    </style>
</head>
<body>
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
            <td>{{$data['rank']}}</td>
            <td>{{$data['ansar_name']}}</td>
            <td>{{$data['account_no']}}</td>
            <td>{{$data['amount']}}</td>
            <td class="kpi_name">{{$data['kpi_name']}}</td>
            <td>{{$data['month']}}</td>
        </tr>
    @endforeach
</table>
</body>
</html>