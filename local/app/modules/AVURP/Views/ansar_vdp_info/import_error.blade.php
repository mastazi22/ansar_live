<table>
    <tr>
        @foreach($headers as $header)
            <th>{{$header}}</th>
        @endforeach
    </tr>
    @foreach($error_datas as $error_data)
        <tr>
            @foreach($error_data["dd"] as $key=>$data)
                @if(in_array($key,$error_data["err"]))
                    <td style="background-color: #ff000f !important;color:#ffffff !important;">{{$data}}</td>
                @else
                    <td>{{$data}}</td>
                @endif
            @endforeach
        </tr>
    @endforeach
</table>