<style>
    table th, table td {
        white-space: nowrap;
        word-break: normal;
        line-break: normal;
        vertical-align: top;
        font-size: 14px;
    }

    table {
        table-layout: auto;
    }

    .serial {
        width: 30px !important;
        vertical-align: top;
        text-align: center;
    }

    .date {
        width: 35px;
        text-align: center;
    }

    .country-id-type {
        width: 30px;
        text-align: center;
    }
    table{
        page-break-inside: avoid;
    }
</style>
<div style="width: 70%;margin: auto;padding: 20px 0px;">
    <div id="print_box">
        <div style="width: 100%;margin: auto;">
            <?php $pic =$applicant->profile_pic ?>
            <div style="display: flex;justify-content: space-between;margin-bottom: 10px">
                <img src="{{public_path('dist/img/ansar-vdp.png')}}" style="width: 100px;height: 100px;align-self: flex-start">
                <div style="align-self: center;text-align: center;font-size: 14px">
                    <strong>গণপ্রজাতন্ত্রী বাংলাদেশ সরকার</strong><br>
                    <strong>বাংলাদেশ আনসার ও গ্রাম প্রতিরক্ষা বাহিনী</strong><br>
                    <strong>সদর দপ্তর, খিলগাঁও, ঢাকা-১২১৯</strong><br>
                    <h2 style="text-decoration: underline; color:blue;margin-top: 5px !important;">www.ansarvdp.gov.bd</h2>
                    <h4 style="text-align: center;margin: 5px"><strong>তৃতীয় ও চতুর্থ শ্রেণী কর্মচারী নিয়োগ-{{\Carbon\Carbon::now()->year}}</strong></h4>
                </div>
                <img src="@if($pic&&file_exists($pic)&&getimagesize($pic)){{$pic}}@endif" style="width: 100px;height: 150px;align-self: end">
            </div>

            <table style="width: 100%" border="1" cellpadding="0" cellspacing="0">
                <tr>
                    <th class="serial">১.</th>
                    <th style="width: 200px">পদের নাম&nbsp;&nbsp;&nbsp;&nbsp;:</th>
                    <th colspan="3">{{$applicant->circular->circular_name}}</th>
                    <th colspan="8">
                        ২. প্রার্থীর নিজ জেলা &nbsp;&nbsp;:{{$applicant->district->unit_name_bng}}
                    </th>
                </tr>
                <tr>
                    <th class="serial">৩.</th>
                    <th style="width: 200px">রেফারেন্স আইডি &nbsp;&nbsp;:</th>
                    <th colspan="12">{{$applicant->applicant_id}}</th>
                </tr>
                <tr>
                    <th rowspan="2" class="serial">৪.</th>
                    <th style="width: 200px" rowspan="2">প্রকাশিত বিজ্ঞপ্তি নম্বর&nbsp;&nbsp;:</th>
                    <th rowspan="2" style="width: 320px">{{$applicant->circular->memorandum_no}}</th>
                    <th rowspan="2" colspan="2" style="text-align: center;vertical-align: middle !important;">তারিখ&nbsp;:</th>
                    <th class="date">দি</th>
                    <th class="date">ন</th>
                    <th class="date">মা</th>
                    <th class="date">স</th>
                    <th class="date">ব</th>
                    <th class="date">ৎ</th>
                    <th class="date">স</th>
                    <th class="date">র</th>
                </tr>
                <tr>
                    <?php
                    $bang = ['0'=>'০','1'=>'১','2'=>'২','3'=>'৩','4'=>'৪','5'=>'৫','6'=>'৬','7'=>'৭','8'=>'৮','9'=>'৯'];
                        $date = str_split(\Carbon\Carbon::now()->format("dmY"));
                        $nids = count(str_split($applicant->national_id_no))==0?array_fill(0,17,''):str_split($applicant->national_id_no);
                        $bcs = count(str_split($applicant->birth_certificate_no))==0?array_fill(0,17,''):str_split($applicant->birth_certificate_no);
                        $dob = \Carbon\Carbon::parse($applicant->date_of_birth)->diff(\Carbon\Carbon::now());
                    ?>
                    @foreach($date as $d)
                            @if(isset($bang[$d]))
                                <th class="date" >{{$bang[$d]}}</th>
                                @else
                                <th class="date" >{{$d}}</th>
                            @endif
                    @endforeach


                </tr>
            </table>
            <table style="width: 100%;margin-top: 5px" border="1" cellpadding="0" cellspacing="0">
                <tr>
                    <th class="serial" rowspan="2">৫.</th>
                    <th rowspan="2">প্রার্থীর নাম</th>
                    <td>বাংলা&nbsp;:{{$applicant->applicant_name_bng}}</td>
                </tr>
                <tr>
                    <td>ইংরেজিতে (বড় অক্ষরে)&nbsp;:{{strtoupper($applicant->applicant_name_bng)}}</td>
                </tr>
                <tr>
                    <th class="serial">৬.</th>
                    <th colspan="2">পিতার নাম&nbsp;&nbsp;&nbsp;:{{$applicant->father_name_bng}}</th>
                </tr>
                <tr>
                    <th class="serial">৭.</th>
                    <th colspan="2">মাতার নাম&nbsp;&nbsp;&nbsp;:{{$applicant->mother_name_bng}}</th>
                </tr>
                <tr>
                    <th class="serial">৮.</th>
                    <th colspan="2">জন্ম তারিখ&nbsp;&nbsp;&nbsp;:{{$applicant->date_of_birth}}<br><span style="font-size: 10px">(শিক্ষা সনদ অনুযায়ী)</span></th>
                </tr>
            </table><br>
            <table style="width: 100%;margin-top: 5px" border="1" cellpadding="0" cellspacing="0">
                <tr>
                    <th class="serial" rowspan="2">৯.</th>
                    <th style="width:250px">জাতীয় পরিচয়পত্র নম্বর&nbsp;&nbsp;&nbsp;:</th>
                    @foreach($nids as $d)
                        @if(isset($bang[$d]))
                            <th class="country-id-type" >{{$bang[$d]}}</th>
                        @else
                            <th class="country-id-type" >{{$d}}</th>
                        @endif
                    @endforeach
                    <th rowspan="2">
                        (যে কোন<br>একটি)
                    </th>
                </tr>
                <tr>
                    <th style="width:250px">জন্ম নিবন্ধন নম্বর&nbsp;&nbsp;&nbsp;:</th>
                    @foreach($bcs as $d)
                        @if(isset($bang[$d]))
                            <th class="country-id-type" >{{$bang[$d]}}</th>
                        @else
                            <th class="country-id-type" >{{$d}}</th>
                        @endif
                    @endforeach
                </tr>
            </table><br>
            <table style="width: 100%;margin-top: 5px" border="1" cellpadding="0" cellspacing="0">
                <colgroup>
                    <col class="serial">
                    <col width="20%">
                    <col width="20%">
                    <col width="20%">
                    <col width="20%">
                    <col width="20%">
                </colgroup>
                <tr>
                    <th class="serial">১০.</th>
                    <th colspan="3">বিজ্ঞপ্তিতে উল্লেখিত তারিখে প্রার্থীর বয়স&nbsp;:</th>
                    <th style="text-align: right">&nbsp;{{$dob->y}}&nbsp;বছর</th>
                    <th style="text-align: right">&nbsp;{{$dob->m}}&nbsp;মাস</th>
                    <th style="text-align: right">&nbsp;{{$dob->d}}&nbsp;দিন</th>
                </tr>
                <tr>
                    <th class="serial">১১.</th>
                    <th colspan="3">বৈবাহিক অবস্থা&nbsp;&nbsp;&nbsp;:{{$applicant->marital_status=='Other'?$applicant->other_marital_status:$applicant->marital_status}}</th>
                    <th>১২.ধর্ম&nbsp;&nbsp;&nbsp;:{{$applicant->religion}}</th>
                    <th colspan="2">১৩.জাতীয়তা&nbsp;&nbsp;&nbsp;:{{$applicant->nationality}}</th>
                </tr>
                <tr>
                    <th class="serial">১৪.</th>
                    <th colspan="3">পুরুষ/মহিলা&nbsp;&nbsp;&nbsp;:{{$applicant->gender=='Male'?'পুরুষ':'মহিলা'}}</th>
                    <th>১৫. উচ্চতা&nbsp;&nbsp;&nbsp;:{{$applicant->height_feet}}'{{$applicant->height_inch}}''</th>
                    <th colspan="2">১৬. ওজন&nbsp;&nbsp;&nbsp;:{{$applicant->weight}}</th>
                </tr>
                <tr>
                    <th class="serial">১৭.</th>
                    <th>বুকের মাপ</th>
                    <th colspan="2">সংকুচিত&nbsp;&nbsp;&nbsp;:{{$applicant->chest_normal}}</th>
                    <th colspan="3">প্রসারিত&nbsp;&nbsp;&nbsp;:{{$applicant->chest_extended}}</th>
                </tr>
            </table><br>


            <table style="width: 100%;margin-top: 5px" border="1" cellpadding="0" cellspacing="0">
                <tr>
                    <th rowspan="{{$applicant->education()->count()+2}}" class="serial">১৮.</th>
                    <th colspan="6" style="text-align: center !important;">শিক্ষাগত যোগ্যতা</th>
                </tr>
                <tr>
                    <th style="text-align: center !important;">পরীক্ষার নাম</th>
                    <th style="text-align: center !important;">বিষয়</th>
                    <th style="text-align: center !important;">শিক্ষা প্রতিষ্ঠান</th>
                    <th style="text-align: center !important;">পাশের সন</th>
                    <th style="text-align: center !important;">বোর্ড/বিশ্ববিদ্যালয়</th>
                    <th style="text-align: center !important;">গ্রেড/শ্রেণি/বিভাগ</th>
                </tr>
                @foreach($applicant->education as $edu)
                <tr>
                    <th>{{$edu->education_deg_bng}}</th>
                    <th>{{$edu->pivot->subject}}</th>
                    <th>{{$edu->pivot->institute_name}}</th>
                    <th>{{$edu->pivot->passing_year}}</th>
                    <th>{{$edu->pivot->board_university}}</th>
                    <th>{{$edu->pivot->gade_divission}}</th>
                </tr>
                    @endforeach
            </table><br>
            <table style="width: 100%;margin-top: 5px" border="1" cellpadding="0" cellspacing="0">
                <tr>
                    <th class="serial">১৯.</th>
                    <th colspan="3">কম্পিউটার জ্ঞান (যদি থাকে)&nbsp;:{{$applicant->computer_knowledge}}</th>
                </tr>
                <tr>
                    <th rowspan="2">২০.</th>
                    <th rowspan="2" style="width: 300px;">যোগাযোগের মাধ্যম&nbsp;:</th>
                    <th style="text-align: center !important;">মোবাইল নম্বর</th>
                    <th style="text-align: center !important;">ই-মেইল (যদি থাকে)</th>
                </tr>
                <tr>
                    <td>{{$applicant->mobile_no_self}}</td>
                    <td>{{$applicant->email_id}}</td>
                </tr>
            </table><br>
            <table style="width: 100%;margin-top: 5px" border="1" cellpadding="0" cellspacing="0">
                <tr>
                    <th class="serial">২১.</th>
                    <th>ঠিকানা&nbsp;:</th>
                    <th style="text-align: center">বর্তমান</th>
                    <th style="text-align: center">স্থায়ী</th>
                </tr>
                <tr>
                    <th class="serial" rowspan="7"></th>
                    <th>বাসা ও সড়ক (নাম/নম্বর)&nbsp;&nbsp;&nbsp;&nbsp;:</th>
                    <td>{{$applicant->present_house_road_number}}</td>
                    <td>{{$applicant->house_road_number}}</td>
                </tr>
                <tr>
                    <th>গ্রাম/পাড়া/মহল্লা&nbsp;&nbsp;&nbsp;&nbsp;:</th>
                    <td>{{$applicant->present_village_name_bng}}</td>
                    <td>{{$applicant->village_name_bng}}</td>
                </tr>
                <tr>

                    <th>ইউনিয়ন/ওয়ার্ড&nbsp;&nbsp;&nbsp;&nbsp;:</th>
                    <td>{{$applicant->present_union_name_bng}}</td>
                    <td>{{$applicant->union_name_bng}}</td>
                </tr>
                <tr>
                    <th>ডাকঘর&nbsp;&nbsp;&nbsp;&nbsp;:</th>
                    <td>{{$applicant->present_post_office_name_bng}}</td>
                    <td>{{$applicant->post_office_name_bng}}</td>
                </tr>
                <tr>
                    <th>পোষ্ট কোড নম্বর&nbsp;&nbsp;&nbsp;&nbsp;:</th>
                    <td>{{$applicant->present_post_code_number}}</td>
                    <td>{{$applicant->post_code_number}}</td>
                </tr>
                <tr>
                    <th>উপজেলা&nbsp;&nbsp;&nbsp;&nbsp;:</th>
                    <td>{{$applicant->present_thana->thana_name_bng}}</td>
                    <td>{{$applicant->thana->thana_name_bng}}</td>
                </tr>
                <tr>
                    <th>জেলা&nbsp;&nbsp;&nbsp;&nbsp;:</th>
                    <td>{{$applicant->present_district->unit_name_bng}}</td>
                    <td>{{$applicant->district->unit_name_bng}}</td>
                </tr>
            </table><br>
            <table style="width: 100%;margin-top: 5px" border="1" cellpadding="0" cellspacing="0">
                <tr>
                    <th class="serial">২২.</th>
                    <th>অভিজ্ঞতার বিবরণ (যদি থাকে)&nbsp;:{{$applicant->experience}}</th>
                </tr>
            </table><br>
            <table style="width: 100%;margin-top: 5px" border="1" cellpadding="0" cellspacing="0">
                <tr>
                    <th class="serial">২৩.</th>
                    <th style="width: 150px">কোটা</th>
                    <th>{{$applicant->quotaType?$applicant->quotaType->quota_name_bng:''}}
                        <?php $quota_data = json_decode($applicant->quota_details,true); ?>
                        @if(count($quota_data)>0)
                            <div >
                                <strong>Additional Informtion:</strong><br>
                                @foreach($quota_detail as $gd)
                                <p style="margin: 0" *ngFor="let qd of quotaDetails">
                                    <strong>{{qd.key}} :</strong>{{qd.value}}
                                </p>
                            </div>
                        @endif
                    </th>
                </tr>
            </table><br>
            <table style="width: 100%;margin-top: 5px" border="1" cellpadding="0" cellspacing="0">
                <tr>
                    <th class="serial">২৪.</th>
                    <th style="width: 300px">বিভাগীয় প্রার্থী কি না</th>
                    <th>{{$applicant->divisional_candidate=="empty"?"প্রযোজ্য নয়":($applicant->divisional_candidate=="yes"?"হ্যাঁ":"না")}}</th>
                </tr>
            </table><br>
            <p style="margin-top: 5px;text-align: justify;font-size: 14px">
                <strong>
                    আমি এই মর্মে অঙ্গিকার করছি যে, উপরে বর্ণিত তথ্যাবলি সম্পূর্ণ সত্য। মৌখিক পরিক্ষার সময় উল্লিখিত তথ্য প্রমাণের
                    জন্য সকল মূল সার্টিফিকেট, জাতীয় পরিচয় এবং রেকর্ডপত্র উপস্থাপন করব। কোন তথ্য অসত্য প্রমাণিত হলে আইনানুগ শাস্তি
                    ভোগ করতে বাধ্য থাকব।
                </strong>
            </p>
            <div style="margin-top: 5px;overflow: hidden">
                <table style="table-layout: auto;float: left" border="1" cellpadding="0" cellspacing="0">
                    <tr>
                        <th rowspan="2" style="text-align: center;vertical-align: middle !important;width: 100px">তারিখ&nbsp;:</th>
                        <th class="date">দি</th>
                        <th class="date">ন</th>
                        <th class="date">মা</th>
                        <th class="date">স</th>
                        <th class="date">ব</th>
                        <th class="date">ৎ</th>
                        <th class="date">স</th>
                        <th class="date">র</th>
                    </tr>
                    <tr>
                        <th class="date" *ngFor="let d of currentDate;let i = index">{{d}}</th>
                    </tr>
                </table>
                <div style="float: right">
                    <img src="{{signatureImage}}" style="width: 150px;height: 50px;display: block">
                    <span style="font-size: 14px;font-weight: bold">প্রার্থীর স্বাক্ষর</span>
                </div>
            </div>
        </div>
    </div><br>
    <button class="btn btn-primary btn-block text-center" (click)="printInvoice()">Print</button>
</div>
