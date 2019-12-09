<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Form</title>
    <style>
        @font-face {
            font-family: 'Siyamrupali';
            src: url('http://bdansarerp.gov.bd/dist/fonts/Siyamrupali.ttf');
        }

        table {
            table-layout: auto;
            page-break-inside: avoid;
        }

        table#r1 {
            white-space: nowrap;
        }

        table#r2 {
            word-break: break-all;
        }

        table th,
        table td {
            line-break: normal;
            vertical-align: middle;
            font-size: 14px;
            padding: 2px 5px;
        }
    </style>
</head>
<body style="font-family: 'Siyamrupali';color: #000000;font-size: 12px;">
<div style="width: 100%;margin: auto;">
    <div style="display: inline-block;width:100%;margin-bottom: 10px;position:relative;">
        <img style="width: 100px;height: 100px;display:inline-block;float:left;"
             src="http://bdansarerp.gov.bd/dist/img/ansar-vdp.png">
        <div style="display:inline-block;text-align: center;font-size: 14px;position: absolute;left: 31%;">
            <strong>গণপ্রজাতন্ত্রী বাংলাদেশ সরকার</strong><br>
            <strong>বাংলাদেশ আনসার ও গ্রাম প্রতিরক্ষা বাহিনী</strong><br>
            <strong>সদর দপ্তর, খিলগাঁও, ঢাকা-১২১৯</strong><br>
            <h2 style="text-decoration: underline; color:blue;margin: 0px">www.ansarvdp.gov.bd</h2>
            <h4 style="text-align: center;margin: 0px">
                <strong>{{$applicant->circular->category->category_name_bng}}</strong>
            </h4>
        </div>
        <img style="width: 100px;height: 120px;display:inline-block;float:right;"
             src="{{str_replace('/var/www/html/ansar_recruitment/server/upload/','http://103.48.16.225:8080/media/',$applicant->getOriginal('profile_pic'))}}">
    </div>
    <table class="r1" border="1" cellpadding="0" cellspacing="0" style="width: 100%">
        <tbody>
        <tr>
            <th style="width: 10px;">১.</th>
            <th style="width: 200px;text-align: left;">পদের নাম&nbsp;&nbsp;:</th>
            <th style="text-align: left;" colspan="3">
                <span style="font-weight: normal;">{{$applicant->circular->circular_name}}</span>
            </th>
            <th style="text-align: left;" colspan="8">২.&nbsp;প্রার্থীর নিজ জেলা&nbsp;&nbsp;:&nbsp;<span
                        style="font-weight: normal;">{{$applicant->district->unit_name_bng}}</span></th>
        </tr>
        <tr>
            <th style="width: 10px;">৩.</th>
            <th style="width: 200px;text-align: left;">রেফারেন্স আইডি&nbsp;&nbsp;:</th>
            <th style="text-align: left;" colspan="12">
                <span style="font-weight: normal;">{{$applicant->applicant_id}}</span>
            </th>
        </tr>
        <tr>
            <th rowspan="2" style="width: 10px;">৪</th>
            <th rowspan="2" style="width: 200px;text-align: left;">প্রকাশিত বিজ্ঞপ্তি নম্বর&nbsp;&nbsp;:</th>
            <th rowspan="2" style="width: 320px;text-align: left;">
                <span style="font-weight: normal;">{{(isset($applicant->circular->memorandum_no))?$applicant->circular->memorandum_no:""}}</span>
            </th>
            <th colspan="2" rowspan="2" style="text-align: center;vertical-align: middle !important;">তারিখ&nbsp;:</th>
            <th>দি</th>
            <th>ন</th>
            <th>মা</th>
            <th>স</th>
            <th>ব</th>
            <th>ৎ</th>
            <th>স</th>
            <th>র</th>
        </tr>
        <tr>
            @php
                $dateDate = strtotime($applicant->circular->circular_publish_date);
                $dateDate = date('dmY',$dateDate);
            if(empty($dateDate)) $dateDate = [];
            @endphp
            @for ($i = 0; $i < 8; $i++)
                <th><span style="font-weight: normal;">{{isset($dateDate[$i])?$dateDate[$i]:'&nbsp;'}}</span></th>
            @endfor
        </tr>
        </tbody>
    </table>
    <table class="r1" border="1" cellpadding="0" cellspacing="0" style="width: 100%;margin-top: 5px">
        <tbody>
        <tr>
            <th rowspan="2" style="width: 10px;">৫.</th>
            <th rowspan="2" style="text-align: left;">প্রার্থীর নাম</th>
            <td><b>বাংলা</b>&nbsp;:&nbsp;<span style="font-weight: normal;">{{$applicant->applicant_name_bng}}</span>
            </td>
        </tr>
        <tr>
            <td><b>ইংরেজিতে (বড় অক্ষরে)</b>&nbsp;:&nbsp;<span
                        style="font-weight: normal;">{{strtoupper($applicant->applicant_name_eng)}}</span>
            </td>
        </tr>
        <tr>
            <th style="width: 10px;">৬.</th>
            <th colspan="2" style="text-align: left;">পিতার নাম&nbsp;&nbsp;:&nbsp;<span
                        style="font-weight: normal;">{{$applicant->father_name_bng}}</span></th>
        </tr>
        <tr>
            <th style="width: 10px;">৭.</th>
            <th colspan="2" style="text-align: left;">মাতার নাম&nbsp;&nbsp;:&nbsp;<span
                        style="font-weight: normal;">{{$applicant->mother_name_bng}}</span></th>
        </tr>
        <tr>
            <th style="width: 10px;">৮.</th>
            <th colspan="2" style="text-align: left;">জন্ম তারিখ&nbsp;&nbsp;:&nbsp;<span
                        style="font-weight: normal;">{{$applicant->date_of_birth}}</span><br><span
                        style="font-size: 10px">&nbsp;(শিক্ষা সনদ অনুযায়ী)</span>
            </th>
        </tr>
        </tbody>
    </table>
    <table class="r1" border="1" cellpadding="0" cellspacing="0" style="width: 100%;margin-top: 5px">
        <tbody>
        <tr>
            <th rowspan="2" style="width: 10px;">৯.</th>
            <th style="width:250px;text-align: left;">জাতীয় পরিচয়পত্র নম্বর&nbsp;:</th>
            @php
                if(empty($applicant->national_id_no)){
                $nid = [];
                }else{
                $nid=$applicant->national_id_no;
                }
            @endphp
            @for ($i = 0; $i < 22; $i++)
                <td style="text-align: center;">
                    <span style="font-weight: normal;">{{isset($nid[$i])?$nid[$i]:'&nbsp;'}}</span>
                </td>
            @endfor
            <th rowspan="2">
                (যে কোন<br>একটি)
            </th>
        </tr>
        <tr>
            <th style="width:250px;text-align: left;">জন্ম নিবন্ধন নম্বর&nbsp;:</th>
            @php
                if(empty($applicant->birth_certificate_no)){
                $bcn = [];
                }else{
                $bcn=$applicant->birth_certificate_no;
                }
            @endphp
            @for ($i = 0; $i < 22; $i++)
                <td style="text-align: center;">
                    <span style="font-weight: normal;">{{isset($bcn[$i])?$bcn[$i]:'&nbsp;'}}</span>
                </td>
            @endfor
        </tr>
        </tbody>
    </table>
    <table class="r1" border="1" cellpadding="0" cellspacing="0" style="width: 100%;margin-top: 5px">
        <colgroup>
            <col>
            <col width="20%">
            <col width="20%">
            <col width="20%">
            <col width="20%">
            <col width="20%">
        </colgroup>
        <tbody>
        <tr>
            @php
                $jdata = json_decode($applicant->circular->constraint);
                $jdata = json_decode($jdata->constraint);
                $dddate = new DateTime($applicant->date_of_birth);
                $noww = new DateTime($jdata->{'0'}->age->maxDate);
                $interval = $noww->diff($dddate);
            @endphp
            <th style="width: 10px;">১০.</th>
            <th style="text-align: left;" colspan="3">বিজ্ঞপ্তিতে উল্লেখিত তারিখে প্রার্থীর বয়স&nbsp;&nbsp;:</th>
            <th style="text-align: right"><span style="font-weight: normal;">{{$interval->y}}</span>&nbsp;বছর</th>
            <th style="text-align: right"><span style="font-weight: normal;">{{$interval->m}}</span>&nbsp;মাস</th>
            <th style="text-align: right"><span style="font-weight: normal;">{{$interval->d}}</span>&nbsp;দিন</th>
        </tr>
        <tr>
            <th style="width: 10px;">১১.</th>
            <th style="text-align: left;" colspan="3">বৈবাহিক অবস্থা&nbsp;&nbsp;:&nbsp;<span
                        style="font-weight: normal;">{{$applicant->marital_status}}</span></th>
            <th style="text-align: left;">১২.&nbsp;ধর্ম&nbsp;&nbsp;:&nbsp;<span
                        style="font-weight: normal;">{{$applicant->religion}}</span></th>
            <th style="text-align: left;" colspan="2">১৩.&nbsp;জাতীয়তা&nbsp;&nbsp;:&nbsp;<span
                        style="font-weight: normal;">{{$applicant->nationality}}</span></th>
        </tr>
        <tr>
            <th style="width: 10px;">১৪.</th>
            <th style="text-align: left;" colspan="3">পুরুষ/মহিলা&nbsp;&nbsp;:&nbsp;<span
                        style="font-weight: normal;">{{$applicant->gender}}</span></th>
            <th style="text-align: left;">১৫.&nbsp;উচ্চতা&nbsp;&nbsp;:&nbsp;<span style="font-weight: normal;">{{$applicant->height_feet}}'{{$applicant->height_inch}}''</span>
            </th>
            <th style="text-align: left;" colspan="2">১৬.&nbsp;ওজন&nbsp;&nbsp;:&nbsp;<span
                        style="font-weight: normal;">{{$applicant->weight}}</span></th>
        </tr>
        <tr>
            <th style="width: 10px;">১৭.</th>
            <th style="text-align: left;">বুকের মাপ&nbsp;</th>
            <th style="text-align: left;" colspan="2">সংকুচিত&nbsp;&nbsp;:&nbsp;<span
                        style="font-weight: normal;">{{$applicant->chest_normal}}</span></th>
            <th style="text-align: left;" colspan="3">প্রসারিত&nbsp;&nbsp;:&nbsp;<span
                        style="font-weight: normal;">{{$applicant->chest_extended}}</span></th>
        </tr>
        </tbody>
    </table>
    <table class="r2" border="1" cellpadding="0" cellspacing="0" style="width: 100%;margin-top: 5px">
        <tbody>
        <tr>
            <th rowspan="{{count($applicant->education)+2}}" style="width: 10px;">১৮.</th>
            <th colspan="6" style="text-align: center !important;">শিক্ষাগত যোগ্যতা</th>
        </tr>
        <tr>
            <th style="width:20%;text-align: center !important;">পরীক্ষার নাম</th>
            <th style="width:20%;text-align: center !important;">বিষয়</th>
            <th style="width:20%;text-align: center !important;">শিক্ষা প্রতিষ্ঠান</th>
            <th style="width:10%;text-align: center !important;">পাশের সন</th>
            <th style="width:20%;text-align: center !important;">বোর্ড/বিশ্ববিদ্যালয়</th>
            <th style="width:10%;text-align: center !important;">গ্রেড/শ্রেণি/বিভাগ</th>
        </tr>
        @foreach($applicant->education as $edu)
            <tr>
                <th style="text-align: center !important;">
                    <span style="font-weight: normal;">{{$edu->education_deg_bng}}</span>
                </th>
                <th style="text-align: center !important;">
                    <span style="font-weight: normal;">{{$edu->pivot->subject}}</span>
                </th>
                <th style="text-align: center !important;">
                    <span style="font-weight: normal;">{{$edu->pivot->institute_name}}</span>
                </th>
                <th style="text-align: center !important;">
                    <span style="font-weight: normal;">{{$edu->pivot->passing_year}}</span>
                </th>
                <th style="text-align: center !important;">
                    <span style="font-weight: normal;">{{$edu->pivot->board_university}}</span>
                </th>
                <th style="text-align: center !important;">
                    <span style="font-weight: normal;">{{$edu->pivot->gade_divission}}</span>
                </th>
            </tr>

        @endforeach
        </tbody>
    </table>
    <table class="r1" border="1" cellpadding="0" cellspacing="0" style="width: 100%;margin-top: 5px">
        <tbody>
        <tr>
            <th style="width: 10px;">১৯.</th>
            <th colspan="3" style="text-align: left;">কম্পিউটার জ্ঞান (যদি থাকে)&nbsp;:<span
                        style="font-weight: normal;">{{$applicant->computer_knowledge}}</span></th>
        </tr>
        <tr>
            <th rowspan="2" style="width: 10px;">২০.</th>
            <th rowspan="2" style="width: 300px;">যোগাযোগের মাধ্যম&nbsp;:</th>
            <th style="text-align: center !important;">মোবাইল নম্বর</th>
            <th style="text-align: center !important;">ই-মেইল (যদি থাকে)</th>
        </tr>
        <tr>
            <td><span style="font-weight: normal;">{{$applicant->mobile_no_self}}</span></td>
            <td><span style="font-weight: normal;">{{$applicant->email_id}}</span></td>
        </tr>
        </tbody>
    </table>
    <table class="r1" border="1" cellpadding="0" cellspacing="0" style="width: 100%;margin-top: 5px">
        <tbody>
        <tr>
            <th style="width: 10px;">২১.</th>
            <th style="text-align: left;">ঠিকানা&nbsp;:</th>
            <th style="text-align: left;">বর্তমান</th>
            <th style="text-align: left;">স্থায়ী</th>
        </tr>
        <tr>
            <th rowspan="7"></th>
            <th style="text-align: left;">বাসা ও সড়ক (নাম/নম্বর)&nbsp;&nbsp;:</th>
            <td><span style="font-weight: normal;">{{$applicant->present_house_road_number}}</span></td>
            <td><span style="font-weight: normal;">{{$applicant->house_road_number}}</span></td>
        </tr>
        <tr>
            <th style="text-align: left;">গ্রাম/পাড়া/মহল্লা&nbsp;&nbsp;:</th>
            <td><span style="font-weight: normal;">{{$applicant->present_village_name_bng}}</span></td>
            <td><span style="font-weight: normal;">{{$applicant->village_name_bng}}</span></td>
        </tr>
        <tr>

            <th style="text-align: left;">ইউনিয়ন/ওয়ার্ড&nbsp;&nbsp;:</th>
            <td><span style="font-weight: normal;">{{$applicant->present_union_name_bng}}</span></td>
            <td><span style="font-weight: normal;">{{$applicant->union_name_bng}}</span></td>
        </tr>
        <tr>
            <th style="text-align: left;">ডাকঘর&nbsp;&nbsp;:</th>
            <td><span style="font-weight: normal;">{{$applicant->present_post_office_name_bng}}</span></td>
            <td><span style="font-weight: normal;">{{$applicant->post_office_name_bng}}</span></td>
        </tr>
        <tr>
            <th style="text-align: left;">পোষ্ট কোড নম্বর&nbsp;&nbsp;:</th>
            <td><span style="font-weight: normal;">{{$applicant->present_post_code_number}}</span></td>
            <td><span style="font-weight: normal;">{{$applicant->post_code_number}}</span></td>
        </tr>
        <tr>
            <th style="text-align: left;">উপজেলা&nbsp;&nbsp;:</th>
            <td><span style="font-weight: normal;">{{$applicant->present_thana->thana_name_bng}}</span></td>
            <td><span style="font-weight: normal;">{{$applicant->thana->thana_name_bng}}</span></td>
        </tr>
        <tr>
            <th style="text-align: left;">জেলা&nbsp;&nbsp;:</th>
            <td><span style="font-weight: normal;">{{$applicant->present_district->unit_name_bng}}</span></td>
            <td><span style="font-weight: normal;">{{$applicant->district->unit_name_bng}}</span></td>
        </tr>
        </tbody>
    </table>
    <table class="r1" border="1" cellpadding="0" cellspacing="0" style="width: 100%;margin-top: 5px">
        <tbody>
        <tr>
            <th style="width: 10px;">২২.</th>
            <th style="text-align: left;">অভিজ্ঞতার বিবরণ (যদি থাকে)&nbsp;:<span
                        style="font-weight: normal;">{{$applicant->experience}}</span>
            </th>
        </tr>
        </tbody>
    </table>
    <table class="r1" border="1" cellpadding="0" cellspacing="0" style="width: 100%;margin-top: 5px">
        <tbody>
        <tr>
            <th style="width: 10px;">২৩.</th>
            <th style="text-align: left;">কোটা&nbsp;:<span
                        style="font-weight: normal;">{{ (isset($applicant->quotaType) && !empty($applicant->quotaType))?$applicant->quotaType->quota_name_bng:""}}</span>
            </th>
        </tr>
        </tbody>
    </table>
    <table class="r1" border="1" cellpadding="0" cellspacing="0" style="width: 100%;margin-top: 5px">
        <tbody>
        <tr>
            <th style="width: 10px;">২৪.&nbsp;</th>
            <th style="text-align: left;">বিভাগীয় প্রার্থী কি না&nbsp;:<span
                        style="font-weight: normal;">{{$applicant->divisional_candidate}}</span></th>
        </tr>
        </tbody>
    </table>
    <p style="margin-top: 5px;text-align: justify;font-size: 14px">
        <strong>
            আমি এই মর্মে অঙ্গিকার করছি যে, উপরে বর্ণিত তথ্যাবলি সম্পূর্ণ সত্য। মৌখিক পরিক্ষার সময় উল্লিখিত তথ্য প্রমাণের
            জন্য সকল মূল সার্টিফিকেট, জাতীয় পরিচয় এবং রেকর্ডপত্র উপস্থাপন করব। কোন তথ্য অসত্য প্রমাণিত হলে আইনানুগ
            শাস্তি ভোগ করতে বাধ্য থাকব।
        </strong>
    </p>
    <div style="margin-top: 5px;overflow: hidden">
        <table border="1" cellpadding="0" cellspacing="0" style="table-layout: auto;float: left">
            <tbody>
            <tr>
                <th rowspan="2" style="text-align: center;vertical-align: middle !important;width: 100px">তারিখ&nbsp;:
                </th>
                <th>দি</th>
                <th>ন</th>
                <th>মা</th>
                <th>স</th>
                <th>ব</th>
                <th>ৎ</th>
                <th>স</th>
                <th>র</th>
            </tr>
            <tr>
                @php
                    $dateDate = strtotime($applicant->created_at);
                    $dateDate = date('dmY',$dateDate);
                if(empty($dateDate)) $dateDate = [];
                @endphp
                @for ($i = 0; $i < 8; $i++)
                    <th><span style="font-weight: normal;">{{isset($dateDate[$i])?$dateDate[$i]:'&nbsp;'}}</span></th>
                @endfor
            </tr>
            </tbody>
        </table>
        <div style="float: right">
            <img style="width: 150px;height: 50px;display: block"
                 src="{{str_replace('/var/www/html/ansar_recruitment/server/upload/','http://103.48.16.225:8080/media/',$applicant->getOriginal('signature_pic'))}}"/>
            <span style="font-size: 14px;font-weight: bold">প্রার্থীর স্বাক্ষর</span>
        </div>
    </div>
</div>
</body>
</html>