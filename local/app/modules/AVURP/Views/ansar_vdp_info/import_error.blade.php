<table>
    <tr>
        @foreach($headers as $header)
            <th>{{$header}}</th>
        @endforeach
    </tr>
    @foreach($error_datas as $error_data)
        <tr>
            @foreach(array_values($error_data) as $data)
                <td>{{$data}}</td>
            @endforeach
        </tr>
    @endforeach
</table>