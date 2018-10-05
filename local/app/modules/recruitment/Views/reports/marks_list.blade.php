<html>
<head>
    <meta charset="UTF-8">
</head>
<body>
<?php $i = 1 ?>
<style>
    .fail td {
        background: #ff000b;
        color: #FFFFFF;
    }
</style>
<table>
    <tr>
        <th>ক্রমিক নং</th>
        <th>আবেদনকারীর আইডি</th>
        <th>নাম</th>
        <th>জেলা</th>
        <th>থানা</th>
        <th>শারীরিক যোগ্যতা</th>
        <th>শিক্ষা ও প্রশিক্ষন</th>
        <th>শিক্ষা ও অভিজ্ঞতা</th>
        <th>শারীরিক যোগ্যতা ও বয়স</th>
        <th>লিখিত পরীক্ষা</th>
        <th>মৌখিক পরীক্ষা</th>
        <th>প্রাপ্ত নম্বর</th>
    </tr>
    @forelse($applicants as $a)
        @if($a->marks->fail())
            <tr class="fail">
                <td>{{($i++).''}}</td>
                <td>{{$a->applicant_id}}</td>
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
                    <td>{{$a->marks->edu_experience}}</td>
                    <td>{{$a->marks->physical_age}}</td>
                    <td>{{round($a->marks->convertedWrittenMark(),2)}}(out
                        of {{$a->circular->markDistribution->convert_written_mark}}) and {{round($a->marks->written,2)}}
                        (out of {{$a->circular->markDistribution->written}})
                    </td>
                    <td>{{$a->marks->viva}}</td>
                    <td>{{$a->marks->totalMarks()}}</td>
                @endif
            </tr>
        @else
            <tr>
                <td>{{($i++).''}}</td>
                <td>{{$a->applicant_id}}</td>
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
                    <td>{{$a->marks->edu_experience}}</td>
                    <td>{{$a->marks->physical_age}}</td>
                    <td>{{round($a->marks->convertedWrittenMark(),2)}}(out
                        of {{$a->circular->markDistribution->convert_written_mark}}) and {{round($a->marks->written,2)}}
                        (out of {{$a->circular->markDistribution->written}})
                    </td>
                    <td>{{$a->marks->viva}}</td>
                    <td>{{$a->marks->totalMarks()}}</td>
                @endif
            </tr>
        @endif
    @empty
        <tr>
            <td colspan="8" style="background: yellow">কোন তথ্য পাওয়া যাই নি</td>
        </tr>
    @endforelse
</table>
</body>
</html>