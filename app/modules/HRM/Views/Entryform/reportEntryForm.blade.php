@extends('template/master')
@section('title','Entry Report')
@section('breadcrumb')
    {!! Breadcrumbs::render('entry_report',$ansarAllDetails->ansar_id) !!}
    @endsection
@section('content')

    <script>
        GlobalApp.controller("EntryReportController", function ($scope) {
            $scope.changeToLocal = function (v) {
                var b = moment(v);
                return b.locale('bn').format('DD-MMMM-YYYY');
            }
        })
        $(document).ready(function () {
            $("#print-report").on('click', function (e) {
                e.preventDefault();
                $('body').append('<div id="print-area" class="letter">' + $("#entry-report").html() + '</div>')
                window.print();
                $("#print-area").remove()
            })
        })
    </script>

    <div ng-controller="EntryReportController">
        <section class="content">
            <div class="row " id="entry-report">
                <div class="box box-solid" style="width:70%;margin:0 auto;">
                    <div class="box-body">
                        <div class="col-md-4">
                            <img class="img-thumbnail img-responsive"
                                 src="{{action('UserController@getImage',['file'=>$ansarAllDetails->profile_pic])}}"
                                 style="margin:0 auto;width:80%;"/>
                        </div>
                        <div class="col-md-6 col-md-offset-2">
                            <table class="table borderless">
                                <tr>
                                    <td><b>আইডি নং </b></td>
                                    <td>{{ $ansarAllDetails->ansar_id }}</td>
                                </tr>
                                <tr>
                                    <td><b>Name </b></td>
                                    <td>{{ $ansarAllDetails->ansar_name_eng }}</td>
                                </tr>
                                <tr>
                                    <td><b>নাম </b></td>
                                    <td>{{ $ansarAllDetails->ansar_name_bng}}</td>
                                </tr>
                                <tr>
                                    <td><b>জন্ম তারিখ </b></td>
                                    <td>[[changeToLocal('{{$ansarAllDetails->data_of_birth}}')]]</td>
                                </tr>
                                <tr>
                                    <td><b>বর্তমান পদবী </b></td>
                                    <td>{{ $ansarAllDetails->designation->name_bng}}</td>
                                </tr>
                                <tr>
                                    <td><b>মোবাইল নং</b></td>
                                    <td style="word-break: break-all">{{LanguageConverter::engToBng($ansarAllDetails->mobile_no_self)}}</td>
                                </tr>

                            </table>
                        </div>
                        <div class="col-md-12" style="margin-bottom: 10px;">
                            <fieldset class="fieldset">
                                <legend class="legend">পারিবারিক তথ্য:</legend>
                                <div class="col-md-6">
                                    <table class="table borderless">
                                        <tr>
                                            <td><b>পিতার নাম</b></td>
                                            <td>{{ $ansarAllDetails->father_name_bng}}</td>
                                        </tr>
                                        <tr>
                                            <td><b>বৈবাহিক অবস্থা</b></td>
                                            <td>{{ strcasecmp($ansarAllDetails->marital_status,"married")==0?"বিবাহিত":(strcasecmp($ansarAllDetails->marital_status,"unmarried")==0?"অবিবাহিত":"তালাকপ্রাপ্ত")}}</td>
                                        </tr>

                                        <tr>
                                            <td><b>জাতীয় পরিচয়পত্র নং</b></td>
                                            <td style="word-break: break-all">{{ LanguageConverter::engToBng($ansarAllDetails->national_id_no) }}</td>
                                        </tr>

                                    </table>
                                </div>

                                <div class="col-md-6">
                                    <table class="table borderless">

                                        <tr>
                                            <td><b>মাতার নাম </b></td>
                                            <td>{{ $ansarAllDetails->mother_name_bng}}</td>
                                        </tr>
                                        <tr>
                                            <td><b>স্ত্রী/স্বামীর নাম </b></td>
                                            <td>{{ $ansarAllDetails->spouse_name_bng}}</td>
                                        </tr>
                                        <tr>
                                            <td><b>জন্ম নিবন্ধন সনদ নং</b></td>
                                            <td style="word-break: break-all">{{ $ansarAllDetails->birth_certificate_no}}</td>
                                        </tr>

                                    </table>
                                </div>

                            </fieldset>
                        </div>

                        <div class="col-md-12" style="margin-bottom: 10px;">
                            <fieldset class="fieldset">
                                <legend class="legend">স্থায়ী ঠিকানা:</legend>
                                <div class="col-md-6">
                                    <table class="table borderless">
                                        <tr>
                                            <td><b>গ্রাম</b></td>
                                            <td>{{$ansarAllDetails->village_name_bng}}</td>
                                        </tr>
                                        <tr>
                                            <td><b>ইউনিয়ন</b></td>
                                            <td>{{$ansarAllDetails->union_name_eng }}</td>

                                        </tr>
                                        <tr>
                                            <td><b>জেলা</b></td>
                                            <td>{{$ansarAllDetails->district->unit_name_bng}}</td>

                                        </tr>


                                    </table>
                                </div>

                                <div class="col-md-6">
                                    <table class="table borderless">
                                        <tr>
                                            <td><b>ডাকঘর </b></td>
                                            <td>{{$ansarAllDetails->post_office_name_bng}}</td>

                                        </tr>
                                        <tr>
                                            <td><b>থানা</b></td>
                                            <td>{{$ansarAllDetails->thana->thana_name_bng}}</td>
                                        </tr>
                                        <tr>
                                            <td><b>বিভাগ</b></td>
                                            <td>{{$ansarAllDetails->division->division_name_bng}}</td>
                                        </tr>

                                    </table>
                                </div>

                            </fieldset>
                        </div>

                        <div class="col-md-12" style="margin-bottom: 10px;">
                            <fieldset class="fieldset">
                                <legend class="legend">শারীরিক যোগ্যতার তথ্য</legend>
                                <div class="col-md-6">
                                    <table class="table borderless">
                                        <tr>
                                            <td><b>উচ্চতা</b></td>
                                            <td>{{LanguageConverter::engToBng($ansarAllDetails->hight_feet)}}
                                                '{{LanguageConverter::engToBng($ansarAllDetails->hight_inch)}}"
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><b>রক্তের গ্রুপ</b></td>
                                            <td>{{ $ansarAllDetails->blood->blood_group_name_bng }}</td>
                                        </tr>
                                        <tr>
                                            <td><b>চোখের রং</b></td>
                                            <td>{{$ansarAllDetails->eye_color }}</td>
                                        </tr>


                                    </table>
                                </div>

                                <div class="col-md-6">
                                    <table class="table borderless">
                                        <tr>
                                            <td><b>গায়ের রং</b></td>
                                            <td>{{$ansarAllDetails->skin_color_bng}}</td>
                                        </tr>
                                        <tr>
                                            <td><b>লিঙ্গ</b></td>
                                            <td>{{strcasecmp($ansarAllDetails->sex,"Male")==0?"পুরুষ":(strcasecmp($ansarAllDetails->sex,"Female")==0?"মহিলা":"অন্যান্য")}}</td>
                                        </tr>
                                        <tr>
                                            <td><b>সনাক্তকরন চিহ্ন</b></td>
                                            <td>{{$ansarAllDetails->identification_mark}}</td>
                                        </tr>

                                    </table>
                                </div>

                            </fieldset>
                        </div>

                        <div class="col-md-12" style="margin-bottom: 10px;">
                            <fieldset class="fieldset">
                                <legend class="legend">শিক্ষাগত যোগ্যতার তথ্য</legend>
                                <table class="table borderless">
                                    <tr>
                                        <td><b>শিক্ষাগত যোগ্যতা</b></td>
                                        <td><b>শিক্ষা প্রতিষ্ঠানের নাম</b></td>
                                        <td><b>পাসের সাল</b></td>
                                        <td><b>বিভাগ / শ্রেণী</b></td>
                                    </tr>
                                    @foreach($ansarAllDetails->education as $singleeducation)

                                        <tr>
                                            <td>{{ $singleeducation->educationName->education_deg_bng }}</td>
                                            <td>{{ $singleeducation->institute_name }}</td>
                                            <td>{{ LanguageConverter::engToBng($singleeducation->passing_year) }}</td>
                                            <td>{{ $singleeducation->gade_divission }}</td>
                                        </tr>
                                    @endforeach
                                </table>
                            </fieldset>
                        </div>

                        <div class="col-md-12" style="margin-bottom: 10px;">
                            <fieldset class="fieldset">
                                <legend class="legend">প্রশিক্ষন সংক্রান্ত তথ্য্</legend>
                                <table class="table borderless">
                                    <tr>
                                        <td><b>পদবী</b></td>
                                        <td><b>প্রতিষ্ঠান</b></td>
                                        <td><b>প্রশিক্ষন শুরুর তারিখ</b></td>
                                        <td><b>প্রশিক্ষন শেষের তারিখ</b></td>
                                        <td><b>সনদ নং</b></td>
                                        </td>
                                    </tr>
                                    @foreach ($ansarAllDetails->training as $singletraining)
                                        <tr>
                                            <td>{{ $singletraining->training_designation}}</td>
                                            <td>{{$singletraining->training_institute_name}}</td>
                                            <td>[[changeToLocal('{{ $singletraining->training_start_date}}')]]
                                            <td>[[changeToLocal('{{ $singletraining->training_end_date }}')]]</td>
                                            <td>{{ $singletraining->trining_certificate_no }}</td>
                                        </tr>
                                    @endforeach
                                </table>
                            </fieldset>
                        </div>

                        <div class="col-md-12" style="margin-bottom: 10px;">
                            <fieldset class="fieldset">
                                <legend class="legend">উত্তরাধিকারীর তথ্য</legend>
                                <table class="table borderless">
                                    <tr>
                                        <td><b>নাম</b></td>
                                        <td><b>সম্পর্ক</b></td>
                                        <td><b>অংশ(%)</b></td>
                                        <td><b>মোবাইল নং</b></td>
                                    </tr>
                                    @foreach ($ansarAllDetails->nominee as $singlenominee)
                                        <tr>
                                            <td>{{$singlenominee->name_of_nominee}}</td>
                                            <td>{{$singlenominee->relation_with_nominee}}</td>
                                            <td>{{$singlenominee->nominee_parcentage}}</td>
                                            <td>{{LanguageConverter::engToBng($singlenominee->nominee_contact_no)}}</td>
                                        </tr>
                                    @endforeach
                                </table>
                            </fieldset>
                        </div>

                        <div class="col-md-12" style="margin-bottom: 10px;">
                            <fieldset class="fieldset">
                                <legend class="legend">অন্যান্য তথ্য</legend>
                                <div class="col-md-6">
                                    <table class="table borderless">
                                        <tr>
                                            <td><b>Mobile no(Self)</b></td>
                                            <td style="word-break: break-all">{{LanguageConverter::engToBng($ansarAllDetails->mobile_no_self)}}</td>
                                        </tr>
                                        <tr>
                                            <td><b>Land phone no(self)</b></td>
                                            <td>{{LanguageConverter::engToBng($ansarAllDetails->land_phone_self) }}</td>
                                        </tr>
                                        <tr>
                                            <td><b>Email(Self)</b></td>
                                            <td>{{$ansarAllDetails->email_self}}</td>
                                        </tr>


                                    </table>
                                </div>

                                <div class="col-md-6">
                                    <table class="table borderless">
                                        <tr>
                                            <td><b>Mobile no(Request)</b></td>
                                            <td>{{LanguageConverter::engToBng($ansarAllDetails->mobile_no_request)}}</td>
                                        </tr>
                                        <tr>
                                            <td><b>Land phone no(request)</b></td>
                                            <td>{{LanguageConverter::engToBng($ansarAllDetails->land_phone_request)}}</td>
                                        </tr>

                                        <tr>
                                            <td><b>Email(Request)</b></td>
                                            <td>{{$ansarAllDetails->email_request}}</td>
                                        </tr>

                                    </table>
                                </div>

                            </fieldset>
                        </div>

                        <div class="col-md-6" style="margin-bottom: 10px;">
                            <fieldset class="fieldset">
                                <legend class="legend">Sign image</legend>
                                <img class="img-thumbnail"
                                     src="{{action('UserController@getSingImage',['file'=>$ansarAllDetails->sign_pic])}}"
                                     style="height:80px;width:100%;"/>
                            </fieldset>
                        </div>
                        <div class="col-md-6" style="margin-bottom: 10px;">
                            <fieldset class="fieldset">
                                <legend class="legend">Thumb image</legend>
                                <img class="img-thumbnail"
                                     src="{{action('UserController@getThumbImage',['file'=>$ansarAllDetails->thumb_pic])}}"
                                     style="height:80px;width:100%;"/>
                            </fieldset>
                        </div>
                    </div>
                </div>
            </div>
            {{--<div style="width: 70%;margin: 12px auto;position: relative;left: -10px">--}}
            {{--<button id="print-report" class="btn btn-primary" style="display: block;">--}}
            {{--<i class="fa fa-print"></i> Print Report--}}
            {{--</button>--}}
            {{--</div>--}}
        </section>
    </div>


@stop