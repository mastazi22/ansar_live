<html>
<head>
    <meta charset="UTF-8">
</head>
<body>
<?php $i = 1 ?>
<table>
    <tr>
        <th>ক্রমিক নং</th>
        <th>নাম</th>
        <th>জেলা</th>
        <th>থানা</th>
        <th>শারীরিক যোগ্যতা</th>
        <th>শিক্ষা ও প্রশিক্ষন</th>
        <th>লিখিত পরীক্ষা</th>
        <th>মৌখিক পরীক্ষা</th>
        <th>প্রাপ্ত নম্বর</th>
    </tr>
    @forelse($applicants as $a)
        <tr>
            <td>{{($i++).''}}</td>
            <td>{{$a->applicant_name_bng}}</td>
            <td>{{$a->district->unit_name_bng}}</td>
            <td>{{$a->thana->thana_name_bng}}</td>
            @if($a->marks->is_bn_candidate)
                <td colspan="5" style="text-align: center;font-weight: bold">Bn Candidate</td>
            @else
                <td>
                    {{$a->marks->physical}}
                </td>


                <td>{{$a->marks->edu_training}}</td>
                <td>{{round($a->marks->convertedWrittenMark(),2)}}(out of {{$a->circular->markDistribution->convert_written_mark}}) and {{round($a->marks->written,2)}}(out of {{$a->circular->markDistribution->written}})
                </td>
                <td>{{$a->marks->viva}}</td>
                <td>{{$a->marks->total_mark}}</td>
            @endif
        </tr>
    @empty
        <tr>
            <td colspan="8" style="background: yellow">কোন তথ্য পাওয়া যাই নি</td>
        </tr>
    @endforelse
</table>
</body>
</html>