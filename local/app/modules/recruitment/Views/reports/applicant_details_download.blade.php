<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
</head>
<body>


<div class="container-fluid">
    <img class="pull-right profile-image"
         src="{{$ansarAllDetails->profile_pic}}"
         alt="">
    <table class="entry-table other-table">
        <tr>
            <td>পদের নাম <span class="pull-right">:</span></td>
            <td><div style="font-size:14px;">{{$ansarAllDetails->circular->circular_name}}</div></td>
        </tr>
        <tr>
            <td>রেফেরেন্স আইডি <span class="pull-right">:</span></td>
            <td><div style="font-size:14px;">{{$ansarAllDetails->applicant_id}}</div></td>
        </tr>
        <tr>
            <td>রোল নং <span class="pull-right">:</span></td>
            <td><div style="font-size:14px;">{{LanguageConverter::engToBng($ansarAllDetails->roll_no)}}</div></td>
        </tr>
    </table>
    <table class="entry-table" style="width: 100%">
        <caption style="text-align: center;font-size: 1em;font-weight: bold"  class="bng-class">বাক্তিগত ও পারিবারিক তথ্য</caption>
        <tr>
            <td>*Name<span class="pull-right">:</span></td>
            <td style="padding-left: 20px">
                <div style="padding:5px;font-size:14px;border:1px solid #ababab">{{$ansarAllDetails->applicant_name_eng}}</div>
            </td>
        </tr>
        <tr>
            <td  class="bng-class">*নাম<span class="pull-right">:</span></td>
            <td style="padding-left: 20px">
                <div style="padding:5px;font-size:14px;border:1px solid #ababab">{{$ansarAllDetails->applicant_name_bng}}</div>
            </td>
        </tr>
        <tr>
            <td  class="bng-class">*পিতার নাম <span class="pull-right">:</span></td>
            <td style="padding-left: 20px">
                <div style="padding:5px;font-size:14px;border:1px solid #ababab"  class="bng-class">{{$ansarAllDetails->father_name_bng}}</div>
            </td>
        </tr>
        <tr>
            <td  class="bng-class">*মাতার নাম <span class="pull-right">:</span></td>
            <td style="padding-left: 20px">
                <div style="padding:5px;font-size:14px;border:1px solid #ababab" class="bng-class">{{$ansarAllDetails->mother_name_bng}}</div>
            </td>
        </tr>
        <tr>
            <td>জন্ম তারিখ <span class="pull-right">:</span></td>
            <td style="padding-left: 20px">
                <div style="padding:5px;font-size:14px;border:1px solid #ababab">{{LanguageConverter::engToBng(\Carbon\Carbon::parse($ansarAllDetails->date_of_birth)->format("d-m-Y"))}}</div>
            </td>
        </tr>
        <tr>
            <td>বয়স <span class="pull-right">:</span></td>
            <td style="padding-left: 20px">
                <div style="padding:5px;font-size:14px;border:1px solid #ababab">{{LanguageConverter::engToBng(\Carbon\Carbon::parse($ansarAllDetails->date_of_birth)->diff($ansarAllDetails->circular->end_date,true)->format("Y বছর m মাস d দিন"))}}</div>
            </td>
        </tr>
        <tr>
            <td>বৈবাহিক অবস্থা <span class="pull-right">:</span></td>
            <td style="padding-left: 20px">
                <div style="padding:5px;font-size:14px;border:1px solid #ababab">{{strcasecmp($ansarAllDetails->marital_status,"married")==0?"বিবাহিত":(strcasecmp($ansarAllDetails->marital_status,"unmarried")==0?"অবিবাহিত":"তালাকপ্রাপ্ত")}}</div>
            </td>
        </tr>
        <tr>
            <td>জাতীয় পরিচয় পত্র নং <span class="pull-right">:</span></td>
            <td style="padding-left: 20px">
                <div style="padding:5px;font-size:14px;border:1px solid #ababab">{{LanguageConverter::engToBng($ansarAllDetails->national_id_no?$ansarAllDetails->national_id_no:'&nbsp;')}}</div>
            </td>
        </tr>
    </table>
    <table class="entry-table" style="width: 100%">
        <caption style="text-align: center;font-size: 1em;font-weight: bold" class="bng-class">স্থায়ী ঠিকানা</caption>
        <tr>
            <td class="bng-class">গ্রাম/বাড়ি নং<span class="pull-right">:</span></td>
            <td style="padding-left: 20px">
                <div style="padding:5px;font-size:14px;border:1px solid #ababab">{{$ansarAllDetails->village_name_bng}}
                    &nbsp;</div>
            </td>
        </tr>
        <tr>
            <td class="bng-class">ডাকঘর <span class="pull-right">:</span></td>
            <td style="padding-left: 20px">
                <div style="padding:5px;font-size:14px;border:1px solid #ababab">{{$ansarAllDetails->post_office_name_bng}}&nbsp;</div>
            </td>
        </tr>
        <tr>
            <td class="bng-class">ইউনিয়ন নাম/ওয়ার্ড <span class="pull-right">:</span></td>
            <td style="padding-left: 20px">
                <div style="padding:5px;font-size:14px;border:1px solid #ababab">{{$ansarAllDetails->union_name_bng}} &nbsp;</div>
            </td>
        </tr>
        <tr>
            <td class="bng-class">*বিভাগ <span class="pull-right">:</span></td>
            <td style="padding-left: 20px">
                <div style="padding:5px;font-size:14px;border:1px solid #ababab" class="bng-class">{{$ansarAllDetails->division->division_name_bng}}
                    &nbsp;</div>
            </td>
        </tr>
        <tr>
            <td class="bng-class">*জেলা <span class="pull-right">:</span></td>
            <td style="padding-left: 20px">
                <div style="padding:5px;font-size:14px;border:1px solid #ababab" class="bng-class">{{$ansarAllDetails->district->unit_name_bng}}</div>
            </td>
        </tr>
        <tr>
            <td class="bng-class">*থানা <span class="pull-right">:</span></td>
            <td style="padding-left: 20px">
                <div style="padding:5px;font-size:14px;border:1px solid #ababab" class="bng-class">{{$ansarAllDetails->thana->thana_name_bng}}</div>
            </td>
        </tr>
    </table>
    <table class="physical-table" style="width: 100%">
        <caption style="text-align: center;font-size: 1em;font-weight: bold">শারীরিক যোগ্যতার তথ্য</caption>
        <tr>
            <td>*উচ্চতা<span class="bng-class">(উচ্চতা)</span><span class="pull-right">:</span></td>
            <td style="padding-left: 20px">
                <div style="padding:0 5px;font-size:14px;border:1px solid #ababab">
                    <span style="padding: 5px 20px">{{LanguageConverter::engToBng($ansarAllDetails->height_feet)}}</span>
                    <span style="padding: 0 5px;border: 1px solid #ababab;border-top: none;border-bottom: none">ফিট</span>
                    <span style="padding: 5px 20px">{{LanguageConverter::engToBng($ansarAllDetails->height_inch)}}</span>
                    <span style="padding: 0 5px;border: 1px solid #ababab;border-top: none;border-bottom: none">ইঞ্চি</span>
                </div>
            </td>
        </tr>
        <tr>
            <td>লিঙ্গ<span class="pull-right">:</span></td>
            <td style="padding-left: 20px">
                <div style="padding:5px;font-size:14px;border:1px solid #ababab">{{$ansarAllDetails->gender=='Male'?"পুরুষ":"মহিলা"}}&nbsp;</div>
            </td>
        </tr>
    </table>
    <table class="entry-table border-table">
        <caption class="bng-class">শিক্ষাগত যোগ্যতার তথ্য*</caption>
        <tbody>
        <tr>
            <td class="bng-class"><b>শিক্ষাগত যোগ্যতা</b></td>
            <td class="bng-class"><b>শিক্ষা প্রতিষ্ঠানের নাম</b></td>
            <td class="bng-class"><b>পাসের সাল</b></td>
            <td class="bng-class"><b>বিভাগ / শ্রেণী</b></td>
        </tr>

        @foreach($ansarAllDetails->appliciantEducationInfo as $singleeducation)

            <tr>
                <td class="bng-class">{{ $singleeducation->educationInfo->education_deg_bng  }}</td>
                <td class="bng-class">{{ $singleeducation->institute_name }}</td>
                <td class="bng-class">{{ LanguageConverter::engToBng($singleeducation->passing_year)}}</td>
                <td class="bng-class">{{ $singleeducation->gade_divission }}</td>
            </tr>
        @endforeach
        <tr>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
        </tr>
        </tbody>
    </table>


    <table class="entry-table other-table">
        <caption>অন্যান্য তথ্য</caption>
        <tr>
            <td>মোবাইল নম্বর <span class="pull-right">:</span></td>
            <td><div style="font-size:14px;">{{LanguageConverter::engToBng($ansarAllDetails->mobile_no_self)}}</div></td>
        </tr>
        <tr>
            <td>কোটা <span class="pull-right">:</span></td>
            <td><div style="font-size:14px;">{{$ansarAllDetails->govQuota?$quota[$ansarAllDetails->govQuota->quota_type]:"----"}}</div></td>
        </tr>
    </table>
</div>
<style>
    @font-face{
        font-family: syamrupali;
        src: url('{{asset('dist/fonts/Siyamrupali.ttf')}}');
    }
    /*table td{
        font-size: 12px !important;
        font-weight: normal !important;
    }*/

    .bng-class,.bng-class>*{
        font-family: syamrupali;
    }
    .entry-table {
        border: none !important;
        page-break-after: auto !important;
        page-break-inside: avoid  !important;
    }
    .physical-table tr{
        border: none !important;
        page-break-after: auto !important;
        page-break-inside: avoid  !important;
    }

    .entry-table td {
        border: none !important;
        padding: 5px 0 0 0 !important;
        text-align: left !important;
    }

    .entry-table tr td:first-child,.physical-table tr td:first-child {
        width: 20%;
    }

    .entry-table tr td:last-child,.physical-table tr td:last-child {
        width: 80%;
        padding-left: 20px !important;
    }

    .entry-table.border-table, .entry-table.other-table {
        width: 100%;
        border: 1px solid #ababab !important;
        border-collapse: collapse;

    }

    .entry-table.border-table td, .entry-table.border-table th {
        border: 1px solid #ababab !important;
        border-collapse: collapse !important;
        width: auto !important;
        text-align: center !important;
    }

    .entry-table caption {
        text-align: center !important;
        font-size: 1em !important;
        font-weight: bold !important;

    }

    .entry-table.other-table td, .entry-table.other-table th {
        border: 1px solid #ababab !important;
        border-collapse: collapse !important;
        padding: 5px 10px !important;
    }

    .entry-table.border-table.image-table{
        margin-top: 10px;
    }
    .entry-table.border-table.image-table tr:first-child td{
        width: 50% !important;
    }
    .entry-table.border-table.image-table tr:not(:first-child) td{
        width: 50% !important;
        height:100px !important;
        vertical-align: middle;
    }
    .entry-table.border-table.image-table td>img{
        width: auto !important;
        height: 80px !important;
        vertical-align: middle;
    }
    .pull-right{
        float: right !important;
    }
    table td div{
        min-height:15px;
    }
    .profile-image{
        width: 150px !important;
        min-height: 100px !important;
    }
    @media print{
        .profile-image{
            width: 150px !important;
            height: 170px !important;
        }
    }

</style>
</body>
</html>