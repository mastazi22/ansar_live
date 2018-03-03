@if(method_exists($applicants,'count')&&$applicants->count()>1)
    <?php $i=0 ?>
    <div class="panel-group" id="accordion">
        @foreach($applicants as $applicant)
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <a data-toggle="collapse" data-parent="#accordion" href="#{{$applicant->applicant_id}}">{{$applicant->applicant_name_bng}}</a>
                    </h4>
                </div>
                <div id="{{$applicant->applicant_id}}" class="panel-collapse collapse @if($i++<=0) in @endif">
                    <div class="panel-body">
                        <div class="container-fluid" style="margin-top: 20px">
                            <img class="pull-right profile-image"
                                 src="{{URL::to('recruitment/profile_image').'?file='.base64_encode($applicant->profile_pic)}}"
                                 alt="">
                            <table class="entry-table" style="width: 100%">
                                <caption style="text-align: center;font-size: 1em;font-weight: bold">বাক্তিগত ও পারিবারিক তথ্য</caption>
                                <tr>
                                    <td>*Name<span class="pull-right">:</span></td>
                                    <td style="padding-left: 20px">
                                        <div style="padding:5px;font-size:14px;border:1px solid #ababab">{{$applicant->applicant_name_eng}}</div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>*নাম<span class="pull-right">:</span></td>
                                    <td style="padding-left: 20px">
                                        <div style="padding:5px;font-size:14px;border:1px solid #ababab">{{$applicant->applicant_name_bng}}</div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>*পিতার নাম <span class="pull-right">:</span></td>
                                    <td style="padding-left: 20px">
                                        <div style="padding:5px;font-size:14px;border:1px solid #ababab">{{$applicant->father_name_bng}}</div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>*মাতার নাম <span class="pull-right">:</span></td>
                                    <td style="padding-left: 20px">
                                        <div style="padding:5px;font-size:14px;border:1px solid #ababab">{{$applicant->mother_name_bng}}</div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>*Date of birth <span class="pull-right">:</span></td>
                                    <td style="padding-left: 20px">
                                        <div style="padding:5px;font-size:14px;border:1px solid #ababab">{{\Carbon\Carbon::parse($applicant->date_of_birth)->format("d-m-Y")}}</div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>*Marital status <span class="pull-right">:</span></td>
                                    <td style="padding-left: 20px">
                                        <div style="padding:5px;font-size:14px;border:1px solid #ababab">{{strcasecmp($applicant->marital_status,"married")==0?"বিবাহিত":(strcasecmp($applicant->marital_status,"unmarried")==0?"অবিবাহিত":"তালাকপ্রাপ্ত")}}</div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>*National Id no <span class="pull-right">:</span></td>
                                    <td style="padding-left: 20px">
                                        <div style="padding:5px;font-size:14px;border:1px solid #ababab">{{$applicant->national_id_no?$applicant->national_id_no:'&nbsp;'}}</div>
                                    </td>
                                </tr>
                            </table>
                            <table class="entry-table" style="width: 100%">
                                <caption style="text-align: center;font-size: 1em;font-weight: bold">স্থায়ী ঠিকানা</caption>
                                <tr>
                                    <td>গ্রাম/বাড়ি নং<span class="pull-right">:</span></td>
                                    <td style="padding-left: 20px">
                                        <div style="padding:5px;font-size:14px;border:1px solid #ababab">{{$applicant->village_name_bng}}
                                            &nbsp;
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>ডাকঘর <span class="pull-right">:</span></td>
                                    <td style="padding-left: 20px">
                                        <div style="padding:5px;font-size:14px;border:1px solid #ababab">{{$applicant->post_office_name_bng}}
                                            &nbsp;
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>ইউনিয়ন নাম/ওয়ার্ড <span class="pull-right">:</span></td>
                                    <td style="padding-left: 20px">
                                        <div style="padding:5px;font-size:14px;border:1px solid #ababab">{{$applicant->union_name_bng}}
                                            &nbsp;
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>*বিভাগ <span class="pull-right">:</span></td>
                                    <td style="padding-left: 20px">
                                        <div style="padding:5px;font-size:14px;border:1px solid #ababab">{{$applicant->division->division_name_bng}}
                                            &nbsp;
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>*জেলা <span class="pull-right">:</span></td>
                                    <td style="padding-left: 20px">
                                        <div style="padding:5px;font-size:14px;border:1px solid #ababab">{{$applicant->district->unit_name_bng}}</div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>*থানা <span class="pull-right">:</span></td>
                                    <td style="padding-left: 20px">
                                        <div style="padding:5px;font-size:14px;border:1px solid #ababab">{{$applicant->thana->thana_name_bng}}</div>
                                    </td>
                                </tr>
                            </table>
                            <table class="entry-table" style="width: 100%">
                                <caption style="text-align: center;font-size: 1em;font-weight: bold">শারীরিক যোগ্যতার তথ্য</caption>
                                <tr>
                                    <td>*Height(উচ্চতা)<span class="pull-right">:</span></td>
                                    <td style="padding-left: 20px">
                                        <div style="padding:0 5px;font-size:14px;border:1px solid #ababab">
                                            <span style="padding: 5px 20px">{{$applicant->height_feet}}</span>
                                            <span style="padding: 0 5px;border: 1px solid #ababab;border-top: none;border-bottom: none">ফিট</span>
                                            <span style="padding: 5px 20px">{{$applicant->height_inch}}</span>
                                            <span style="padding: 0 5px;border: 1px solid #ababab;border-top: none;border-bottom: none">ইঞ্চি</span>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>*ওজন<span class="pull-right">:</span></td>
                                    <td style="padding-left: 20px">
                                        <div style="padding:5px;font-size:14px;border:1px solid #ababab">{{$applicant->weight}}</div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>বুকের মাপ(স্বাভাবিক-সম্প্রসারিত) <span class="pull-right">:</span></td>
                                    <td style="padding-left: 20px">
                                        <div style="padding:5px;font-size:14px;border:1px solid #ababab">{{$applicant->chest_normal.'-'.$applicant->chest_extended}}
                                            &nbsp;
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>*Gender<span class="pull-right">:</span></td>
                                    <td style="padding-left: 20px">
                                        <div style="padding:5px;font-size:14px;border:1px solid #ababab">{{$applicant->gender}}&nbsp;</div>
                                    </td>
                                </tr>
                            </table>
                            <table class="entry-table border-table">
                                <caption>শিক্ষাগত যোগ্যতার তথ্য*</caption>
                                <tbody>
                                <tr>
                                    <td><b>শিক্ষাগত যোগ্যতা</b></td>
                                    <td><b>শিক্ষা প্রতিষ্ঠানের নাম</b></td>
                                    <td><b>পাসের সাল</b></td>
                                    <td><b>বিভাগ / শ্রেণী</b></td>
                                </tr>

                                @foreach($applicant->appliciantEducationInfo as $singleeducation)

                                    <tr>
                                        <td>{{ $singleeducation->educationInfo->education_deg_bng  }}</td>
                                        <td>{{ $singleeducation->institute_name }}</td>
                                        <td>{{ $singleeducation->passing_year}}</td>
                                        <td>{{ $singleeducation->gade_divission }}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>


                            <table class="entry-table other-table">
                                <caption>অন্যান্য তথ্য</caption>
                                {{--<tr>
                                    <td>Mobile No. (Self) নিজ* <span class="pull-right">:</span></td>
                                    <td>
                                        <div style="font-size:14px;">{{$applicants->mobile_no_self}}</div>
                                    </td>
                                </tr>--}}
                                <tr>
                                    <td>Training Info<span class="pull-right">:</span></td>
                                    <td>
                                        <div style="font-size:14px;">{{$applicant->training_info}}</div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Reference name<span class="pull-right">:</span></td>
                                    <td>
                                        <div style="font-size:14px;">{{$applicant->connection_name}}</div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Reference relation<span class="pull-right">:</span></td>
                                    <td>
                                        <div style="font-size:14px;">{{$applicant->connection_relation}}</div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Reference address<span class="pull-right">:</span></td>
                                    <td>
                                        <div style="font-size:14px;">{{$applicant->connection_address}}</div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Reference mobile no<span class="pull-right">:</span></td>
                                    <td>
                                        <div style="font-size:14px;">{{$applicant->connection_mobile_no}}</div>
                                    </td>
                                </tr>
                            </table>
                            <div class="row" style="margin-top: 20px">
                                <div class="col-sm-12 text-center">
                                    {{--<a class="btn btn-primary" target="_blank"
                                       href="{{URL::route('recruitment.applicant.detail_view',['id'=>$applicants->applicant_id])}}"><i
                                                class="fa fa-edit"></i>&nbsp;Edit info</a>--}}
                                    <a class="btn btn-primary" ng-click="editApplicant('{{URL::route('recruitment.applicant.detail_view',['id'=>$applicant->applicant_id])}}')"><i
                                                class="fa fa-edit"></i>&nbsp;Edit info</a>
                                    <button class="btn btn-primary" ng-click="addToSelection('{{$applicant->applicant_id}}')"><i
                                                class="fa fa-plus"></i>&nbsp;Add to selection</button>
                                    <button id="accept-applicant" class="btn btn-primary" ng-click="acceptedApplicants('{{$applicant->applicant_id}}')"><i
                                                class="fa fa-check"></i>&nbsp;Accept if Bn Candidate</button>
                                    <button class="btn btn-danger" ng-click="rejectApplicants('{{$applicant->applicant_id}}')"><i
                                                class="fa fa-minus"></i>&nbsp;Reject applicant</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
    </div>
@elseif($applicants)
    <div class="container-fluid" style="margin-top: 20px">
        <img class="pull-right profile-image"
             src="{{URL::to('recruitment/profile_image').'?file='.base64_encode($applicants->profile_pic)}}"
             alt="">
        <table class="entry-table" style="width: 100%">
            <caption style="text-align: center;font-size: 1em;font-weight: bold">বাক্তিগত ও পারিবারিক তথ্য</caption>
            <tr>
                <td>*Name<span class="pull-right">:</span></td>
                <td style="padding-left: 20px">
                    <div style="padding:5px;font-size:14px;border:1px solid #ababab">{{$applicants->applicant_name_eng}}</div>
                </td>
            </tr>
            <tr>
                <td>*নাম<span class="pull-right">:</span></td>
                <td style="padding-left: 20px">
                    <div style="padding:5px;font-size:14px;border:1px solid #ababab">{{$applicants->applicant_name_bng}}</div>
                </td>
            </tr>
            <tr>
                <td>*পিতার নাম <span class="pull-right">:</span></td>
                <td style="padding-left: 20px">
                    <div style="padding:5px;font-size:14px;border:1px solid #ababab">{{$applicants->father_name_bng}}</div>
                </td>
            </tr>
            <tr>
                <td>*মাতার নাম <span class="pull-right">:</span></td>
                <td style="padding-left: 20px">
                    <div style="padding:5px;font-size:14px;border:1px solid #ababab">{{$applicants->mother_name_bng}}</div>
                </td>
            </tr>
            <tr>
                <td>*Date of birth <span class="pull-right">:</span></td>
                <td style="padding-left: 20px">
                    <div style="padding:5px;font-size:14px;border:1px solid #ababab">{{\Carbon\Carbon::parse($applicants->date_of_birth)->format("d-m-Y")}}</div>
                </td>
            </tr>
            <tr>
                <td>*Marital status <span class="pull-right">:</span></td>
                <td style="padding-left: 20px">
                    <div style="padding:5px;font-size:14px;border:1px solid #ababab">{{strcasecmp($applicants->marital_status,"married")==0?"বিবাহিত":(strcasecmp($applicants->marital_status,"unmarried")==0?"অবিবাহিত":"তালাকপ্রাপ্ত")}}</div>
                </td>
            </tr>
            <tr>
                <td>*National Id no <span class="pull-right">:</span></td>
                <td style="padding-left: 20px">
                    <div style="padding:5px;font-size:14px;border:1px solid #ababab">{{$applicants->national_id_no?$applicants->national_id_no:'&nbsp;'}}</div>
                </td>
            </tr>
        </table>
        <table class="entry-table" style="width: 100%">
            <caption style="text-align: center;font-size: 1em;font-weight: bold">স্থায়ী ঠিকানা</caption>
            <tr>
                <td>গ্রাম/বাড়ি নং<span class="pull-right">:</span></td>
                <td style="padding-left: 20px">
                    <div style="padding:5px;font-size:14px;border:1px solid #ababab">{{$applicants->village_name_bng}}
                        &nbsp;
                    </div>
                </td>
            </tr>
            <tr>
                <td>ডাকঘর <span class="pull-right">:</span></td>
                <td style="padding-left: 20px">
                    <div style="padding:5px;font-size:14px;border:1px solid #ababab">{{$applicants->post_office_name_bng}}
                        &nbsp;
                    </div>
                </td>
            </tr>
            <tr>
                <td>ইউনিয়ন নাম/ওয়ার্ড <span class="pull-right">:</span></td>
                <td style="padding-left: 20px">
                    <div style="padding:5px;font-size:14px;border:1px solid #ababab">{{$applicants->union_name_bng}}
                        &nbsp;
                    </div>
                </td>
            </tr>
            <tr>
                <td>*বিভাগ <span class="pull-right">:</span></td>
                <td style="padding-left: 20px">
                    <div style="padding:5px;font-size:14px;border:1px solid #ababab">{{$applicants->division->division_name_bng}}
                        &nbsp;
                    </div>
                </td>
            </tr>
            <tr>
                <td>*জেলা <span class="pull-right">:</span></td>
                <td style="padding-left: 20px">
                    <div style="padding:5px;font-size:14px;border:1px solid #ababab">{{$applicants->district->unit_name_bng}}</div>
                </td>
            </tr>
            <tr>
                <td>*থানা <span class="pull-right">:</span></td>
                <td style="padding-left: 20px">
                    <div style="padding:5px;font-size:14px;border:1px solid #ababab">{{$applicants->thana->thana_name_bng}}</div>
                </td>
            </tr>
        </table>
        <table class="entry-table" style="width: 100%">
            <caption style="text-align: center;font-size: 1em;font-weight: bold">শারীরিক যোগ্যতার তথ্য</caption>
            <tr>
                <td>*Height(উচ্চতা)<span class="pull-right">:</span></td>
                <td style="padding-left: 20px">
                    <div style="padding:0 5px;font-size:14px;border:1px solid #ababab">
                        <span style="padding: 5px 20px">{{$applicants->height_feet}}</span>
                        <span style="padding: 0 5px;border: 1px solid #ababab;border-top: none;border-bottom: none">ফিট</span>
                        <span style="padding: 5px 20px">{{$applicants->height_inch}}</span>
                        <span style="padding: 0 5px;border: 1px solid #ababab;border-top: none;border-bottom: none">ইঞ্চি</span>
                    </div>
                </td>
            </tr>
            <tr>
                <td>*ওজন<span class="pull-right">:</span></td>
                <td style="padding-left: 20px">
                    <div style="padding:5px;font-size:14px;border:1px solid #ababab">{{$applicants->weight}}</div>
                </td>
            </tr>
            <tr>
                <td>বুকের মাপ(স্বাভাবিক-সম্প্রসারিত) <span class="pull-right">:</span></td>
                <td style="padding-left: 20px">
                    <div style="padding:5px;font-size:14px;border:1px solid #ababab">{{$applicants->chest_normal.'-'.$applicants->chest_extended}}
                        &nbsp;
                    </div>
                </td>
            </tr>
            <tr>
                <td>*Gender<span class="pull-right">:</span></td>
                <td style="padding-left: 20px">
                    <div style="padding:5px;font-size:14px;border:1px solid #ababab">{{$applicants->gender}}&nbsp;</div>
                </td>
            </tr>
        </table>
        <table class="entry-table border-table">
            <caption>শিক্ষাগত যোগ্যতার তথ্য*</caption>
            <tbody>
            <tr>
                <td><b>শিক্ষাগত যোগ্যতা</b></td>
                <td><b>শিক্ষা প্রতিষ্ঠানের নাম</b></td>
                <td><b>পাসের সাল</b></td>
                <td><b>বিভাগ / শ্রেণী</b></td>
            </tr>

            @foreach($applicants->appliciantEducationInfo as $singleeducation)

                <tr>
                    <td>{{ $singleeducation->educationInfo->education_deg_bng  }}</td>
                    <td>{{ $singleeducation->institute_name }}</td>
                    <td>{{ $singleeducation->passing_year}}</td>
                    <td>{{ $singleeducation->gade_divission }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>


        <table class="entry-table other-table">
            <caption>অন্যান্য তথ্য</caption>
            {{--<tr>
                <td>Mobile No. (Self) নিজ* <span class="pull-right">:</span></td>
                <td>
                    <div style="font-size:14px;">{{$applicants->mobile_no_self}}</div>
                </td>
            </tr>--}}
            <tr>
                <td>Training Info<span class="pull-right">:</span></td>
                <td>
                    <div style="font-size:14px;">{{$applicants->training_info}}</div>
                </td>
            </tr>
            <tr>
                <td>Reference name<span class="pull-right">:</span></td>
                <td>
                    <div style="font-size:14px;">{{$applicants->connection_name}}</div>
                </td>
            </tr>
            <tr>
                <td>Reference relation<span class="pull-right">:</span></td>
                <td>
                    <div style="font-size:14px;">{{$applicants->connection_relation}}</div>
                </td>
            </tr>
            <tr>
                <td>Reference address<span class="pull-right">:</span></td>
                <td>
                    <div style="font-size:14px;">{{$applicants->connection_address}}</div>
                </td>
            </tr>
            <tr>
                <td>Reference mobile no<span class="pull-right">:</span></td>
                <td>
                    <div style="font-size:14px;">{{$applicants->connection_mobile_no}}</div>
                </td>
            </tr>
        </table>
        <div class="row" style="margin-top: 20px">
            <div class="col-sm-12 text-center">
                {{--<a class="btn btn-primary" target="_blank"
                   href="{{URL::route('recruitment.applicant.detail_view',['id'=>$applicants->applicant_id])}}"><i
                            class="fa fa-edit"></i>&nbsp;Edit info</a>--}}
                <a class="btn btn-primary" ng-click="editApplicant('{{URL::route('recruitment.applicant.detail_view',['id'=>$applicants->applicant_id])}}')"><i
                            class="fa fa-edit"></i>&nbsp;Edit info</a>
                <button class="btn btn-primary" ng-click="addToSelection('{{$applicants->applicant_id}}')"><i
                            class="fa fa-plus"></i>&nbsp;Add to selection</button>
                <button id="accept-applicant" class="btn btn-primary" ng-click="acceptedApplicants('{{$applicants->applicant_id}}')"><i
                            class="fa fa-check"></i>&nbsp;Accept if Bn Candidate</button>
                <button class="btn btn-danger" ng-click="rejectApplicants('{{$applicants->applicant_id}}')"><i
                            class="fa fa-minus"></i>&nbsp;Reject applicant</button>
            </div>
        </div>
    </div>

@else
    <h3 class="text-center text-danger">No applicant found</h3>
@endif
<style>
    .entry-table {
        border: none !important;
        page-break-after: auto !important;
        page-break-inside: avoid;
    !important;
    }

    .entry-table td {
        border: none !important;
        padding: 5px 0 0 0 !important;
        text-align: left !important;
    }

    .entry-table tr td:first-child {
        width: 20%;
    }

    .entry-table tr td:last-child {
        width: 80%;
        padding-left: 20px !important;
    }

    .entry-table.border-table, .entry-table.other-table {
        width: 100%;
        border: 1px solid #ababab !important;

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

    .entry-table.border-table.image-table {
        margin-top: 10px;
    }

    .entry-table.border-table.image-table tr:first-child td {
        width: 50% !important;
    }

    .entry-table.border-table.image-table tr:not(:first-child) td {
        width: 50% !important;
        height: 100px !important;
        vertical-align: middle;
    }

    .entry-table.border-table.image-table td > img {
        width: auto !important;
        height: 80px !important;
        vertical-align: middle;
    }
</style>