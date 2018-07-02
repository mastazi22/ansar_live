<html>
<head>
    <meta charset="UTF-8">
</head>
<body>
<style>
    @font-face {
        font-family: syamrupali;
        src: url('{{asset('dist/fonts/vrindab.ttf')}}');
    }

    * {
        font-family: syamrupali !important;
    }

    table {
        border-collapse: collapse;
    }

    table td, table th {
        border: 1px solid #000000;
        text-align: center;
        font-size: 11px !important;
    }

    /*.inner-table tr:first-child th,.inner-table tr:first-child td{
        border-top: none !important;
    }
    .inner-table tr:last-child th,.inner-table tr:last-child td{
        border-bottom: none !important;
    }*/
    .inner-table tr th:first-child, .inner-table tr td:first-child {
        border-left: none !important;
    }

    .inner-table tr th:last-child, .inner-table tr td:last-child {
        border-right: none !important;
    }

    tr, td {
        page-break-inside: avoid !important;
    }
</style>
<?php $i = 1 ?>
<table style="width: 100%">
    <caption style="text-align: center;font-size: 20px;font-weight: bold;color:#000000;">
        আনসার ও গ্রাম প্রতিরক্ষা বাহিনী,@if(isset($unit)) {{$unit->unit_name_bng}} @elseif(isset($range)) {{$range->division_name_bng}} @endif<br>
        মৌলিক প্রশিক্ষণ -সাধারণ আনসার (পুরুষ)<br>
        চুড়ান্তভাবে নির্বাচিত প্রশিক্ষণার্থির তালিকা
    </caption>
    <tr>
        <th>ক্রমিক নং</th>
        <th>নাম</th>
        <th>পিতার নাম</th>
        <th>ঠিকানা</th>
        <th>জাতীয় পরিচয় পত্র নং</th>
        <th>জন্ম তারিখ</th>
        <th>উচ্চতাযুক্ত</th>
        <th>শিক্ষাগত যোগ্যতা</th>
        <th>প্রাপ্ত নম্বর</th>
    </tr>
    @forelse($applicants as $a)
        <tr>
            <td>{{$i++}}</td>
            <td>{{$a->applicant->applicant_name_bng}}</td>
            <td>{{$a->applicant->father_name_bng}}</td>
            <td>
                <table class="inner-table" width="100%">
                    <tr>
                        <th>গ্রাম</th>
                        <th>ডাকঘর</th>
                        <th>উপজেলা</th>
                        <th>জেলা</th>
                    </tr>
                    <tr>
                        <td>{{$a->applicant->village_name_bng}}</td>
                        <td>{{$a->applicant->post_office_name_bng}}</td>
                        {{--<td>{{$a->applicant->union_name_bng}}</td>--}}
                        <td>{{$a->applicant->thana->thana_name_bng}}</td>
                        <td>{{$a->applicant->district->unit_name_bng}}</td>
                    </tr>
                </table>
            </td>


            <td>{{$a->applicant->national_id_no}}</td>
            <td>{{$a->applicant->date_of_birth}}</td>
            <td>{{$a->applicant->height_feet.' feet '.$a->applicant->height_inch.' inch' }}</td>
            <td>
                <table class="inner-table" width="100%">
                    <tr>
                        <th>শিক্ষাগত যোগ্যতা</th>
                        <th>শিক্ষা প্রতিষ্ঠানের নাম</th>
                        <th>পাসের সাল</th>
                        <th>বিভাগ / শ্রেণী</th>
                    </tr>
                    @forelse($a->applicant->appliciantEducationInfo as $e)
                        <tr>
                            <td>{{$e->educationInfo->education_deg_bng}}</td>
                            <td>{{$e->institute_name}}</td>
                            <td>{{$e->passing_year}}</td>
                            <td>{{$e->gade_divission}}</td>
                        </tr>

                    @empty
                        <tr>
                            <td colspan="4" style="background: yellow">কোন তথ্য পাওয়া যাই নি</td>
                        </tr>
                    @endforelse
                </table>
            </td>
            @if($a->is_bn_candidate)
                <td><strong>Bn Candidate</strong></td>
            @elseif($a->specialized)
                <td><strong>Special Candidate</strong></td>
            @else
                <td>{{round($a->total_mark,2)}}</td>
            @endif
        </tr>
    @empty
        <tr>
            <td colspan="9" style="background: yellow">কোন তথ্য পাওয়া যাই নি</td>
        </tr>
    @endforelse
</table>
</body>
</html>