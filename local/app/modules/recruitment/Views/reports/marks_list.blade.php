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
            <td>{{$unit->unit_name_bng}}</td>
            <td>
                {{$a->marks->physical}}
            </td>


            <td>{{$a->marks->edu_training}}</td>
            <td>{{$a->marks->written}}</td>
            <td>{{$a->marks->viva}}</td>
            <td>{{$a->marks->total_mark}}</td>
        </tr>
    @empty
        <tr>
            <td colspan="7" style="background: yellow">কোন তথ্য পাওয়া যাই নি</td>
        </tr>
    @endforelse
</table>
</body>
</html>