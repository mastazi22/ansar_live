@extends('template.master')

@section('content')



    <div class="content-wrapper">
        <section class="content">
            <div class="row">
                <div style="width:80%;margin:0 auto;">
                    <div id="entryform">
                        <div class="profile_pic">
                            <img src="{{$ansarAllDetails->profile_pic}}"/>
                        </div>
                        <form class="form-horizontal" method="post" action="{{action('FormSubmitHandler@handleregistration')}}" >
                            {!! csrf_field() !!}

                            <fieldset>

                                <div class="level-title-session-entry">
                                    <h5 style="text-align: center;">পারিবারিক তথ্য </h5>
                                </div>

                                <div class="box-info">
                                    <div class="box-body">
                                        <div class="form group col-md-12">
                                            <label class="control-label col-sm-2" for="email">Name:</label>
                                            <div class="col-sm-10">
                                                <input type="text" class="form-control" value="{{ $ansarAllDetails->ansar_name_eng }}" disabled  disabled  placeholder="Enter your name" />
                                            </div>
                                        </div>


                                        <div class="form-horizontal col-md-12">
                                            <label class="control-label col-sm-2" for="email">নাম:</label>
                                            <div class="col-sm-10">
                                                <input type="text" class="form-control" id="ansar_name_bng" value="{{ $ansarAllDetails->ansar_name_bng}}" disabled  placeholder="আপনার নাম লিখুন"/>
                                            </div>
                                        </div>

                                        <div class="form-horizontal col-md-12">
                                            <label class="control-label col-sm-2" for="email">বর্তমান পদবী :</label>
                                            <div class="col-sm-10">
                                                <input type="text" class="form-control"id="recent_Status_bl" value="{{ $ansarAllDetails->designation->name_bng}}"  disabled  placeholder="বর্তমান পদবী "/>
                                            </div>
                                        </div>
                                        <div class="form group col-md-12">
                                            <label class="control-label col-sm-2" for="email">Father's name:</label>
                                            <div class="col-sm-10">
                                                <input class="form-control" id="father_name_eng" value="{{ $ansarAllDetails->father_name_eng}}" type="text" disabled  placeholder="Father's name">
                                            </div>
                                        </div>
                                        <div class="form group col-md-12">
                                            <label class="control-label col-sm-2" for="email">বাবার নাম:</label>
                                            <div class="col-sm-10">
                                                <input class="form-control " id="father_name_bng" value="{{ $ansarAllDetails->father_name_bng}}" type="text" disabled  placeholder="বাবার নাম">
                                            </div>
                                        </div>
                                        <div class="form group col-md-12">
                                            <label class="control-label col-sm-2" for="email">Mother's Name:</label>
                                            <div class="col-sm-10">
                                                <input class="form-control  " id="mother_name_eng" value="{{ $ansarAllDetails->mother_name_eng}}" type="text" disabled  placeholder=" Mother's Name ">
                                            </div>
                                        </div>
                                        <div class="form group col-md-12">
                                            <label class="control-label col-sm-2" for="email">মাতার নাম:</label>
                                            <div class="col-sm-10">
                                                <input class="form-control  " id="mother_name_bng" value="{{ $ansarAllDetails->mother_name_bng}}" type="text" disabled  placeholder="মাতার নাম ">
                                            </div>

                                        </div>
                                        <div class="form group col-md-12">
                                            <label class="control-label col-sm-2" for="email">Date of birth:</label>
                                            <div class="col-sm-10">
                                                <input class="form-control  " id="data_of_birth" value="{{ $ansarAllDetails->data_of_birth}}" type="text" disabled  placeholder="Date of birth">
                                            </div>


                                        </div>
                                        <div class="form group col-md-12">
                                            <label class="control-label col-sm-2" for="email">Marital Status:</label>
                                            <div class="col-sm-10">
                                                <input class="form-control  " id="marital_status" value="{{ $ansarAllDetails->marital_status}}" type="text" disabled  placeholder="Marital Status">
                                            </div>

                                        </div>
                                        <div class="form group col-md-12">
                                            <label class="control-label col-sm-2" for="email">Spouse Name:</label>
                                            <div class="col-sm-10">
                                                <input class="form-control  " id="spouse_name_eng" value="{{ $ansarAllDetails->spouse_name_eng}}" type="text" disabled  placeholder="Spouse Name">
                                            </div>

                                        </div>
                                        <div class="form group col-md-12">
                                            <label class="control-label col-sm-2" for="email">স্ত্রী/স্বামীর নাম:</label>
                                            <div class="col-sm-10">
                                                <input class="form-control  " id="spouse_name_bng" value="{{ $ansarAllDetails->spouse_name_bng}}" type="text" disabled  placeholder=" স্ত্রী/স্বামীর নাম ">
                                            </div>

                                        </div>
                                        <div class="form group col-md-12">
                                            <label class="control-label col-sm-2" for="email">National Id no:</label>
                                            <div class="col-sm-10">
                                                <input class="form-control  " id="national_id_no" value="{{ $ansarAllDetails->national_id_no }}" type="text" disabled  placeholder="National Id no">
                                            </div>

                                        </div>

                                        <div class="form group col-md-12">
                                            <label class="control-label col-sm-2" for="email">Birth Certificate no:</label>
                                            <div class="col-sm-10">
                                                <input class="form-control  " id="birth_certificate_no" value="{{ $ansarAllDetails->birth_certificate_no}}" type="text" disabled  placeholder="Birth Certificate no">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </fieldset>
                            <fieldset>


                                <div class="level-title-session-entry">
                                    <h5 style="text-align: center;">স্থায়ী ঠিকানা</h5>
                                </div>
                                <div class="box-info">
                                    <div class="box-body">
                                        <div class="form-horizontal col-md-12">
                                            <label class="control-label col-sm-2" for="email">গ্রাম:</label>
                                            <div class="col-sm-10">
                                                <input class="form-control  " id="village_name" value="{{$ansarAllDetails->village_name}}" type="text" disabled  placeholder=" গ্রাম">
                                            </div>

                                        </div>
                                        <div class="form-horizontal col-md-12">
                                            <label class="control-label col-sm-2" for="email">ডাকঘর:</label>
                                            <div class="col-sm-10">
                                                <input class="form-control  " id="post_office_name" value="{{$ansarAllDetails->post_office_name}}" type="text" disabled  placeholder=" ডাকঘর ">
                                            </div>

                                        </div>

                                        <div class="form-horizontal col-md-12">
                                            <label class="control-label col-sm-2" for="email">Division:</label>
                                            <div class="col-sm-10">
                                                <input class="form-control  " id="post_office_name" value="{{$ansarAllDetails->division->division_name_bng}}" type="text" disabled  placeholder=" ডাকঘর ">
                                            </div>

                                        </div>

                                        <div class="form-horizontal col-md-12">
                                            <label class="control-label col-sm-2" for="email">District:</label>
                                            <div class="col-sm-10">
                                                <input class="form-control  " id="post_office_name" value="{{$ansarAllDetails->district->unit_name_bng}}" type="text" disabled  placeholder=" ডাকঘর ">
                                            </div>

                                        </div>
                                        <div class="form-horizontal col-md-12">
                                            <label class="control-label col-sm-2" for="email">Thana:</label>
                                            <div class="col-sm-10">
                                                <input class="form-control  " id="post_office_name" value="{{$ansarAllDetails->thana->thana_name_bng}}" type="text" disabled  placeholder=" ডাকঘর ">
                                            </div>

                                        </div>







                                        <div class="form group col-md-12">
                                            <label class="control-label col-sm-2" for="email">Union:</label>
                                            <div class="col-sm-10">
                                                <input class="form-control  " id="union_name_eng" value="{{$ansarAllDetails->union_name_eng }}" type="text" disabled  placeholder=" Union Name ">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </fieldset>
                            <fieldset>


                                <div class="level-title-session-entry">
                                    <h5 style="text-align: center;">শারীরিক যোগ্যতার তথ্য</h5>
                                </div>
                                <div class="box-info">
                                    <div class="box-body">
                                        <div class="form-horizontal col-md-12">
                                            <label class="control-label col-sm-2" for="email">Height:</label>
                                            <div class="col-sm-10">
                                                <input class="form-control  " id="hight_inch" value="{{$ansarAllDetails->hight_inch}}" type="text" disabled  placeholder=" Height ">
                                            </div>

                                        </div>
                                        <div class="form-horizontal col-md-12">
                                            <label class="control-label col-sm-2" for="email">রক্তের গ্রুপ:</label>
                                            <div class="col-sm-10">
                                                <input class="form-control  " id="hight_inch" value="{{ $ansarAllDetails->blood->blood_group_name_bng }}" type="text" disabled  placeholder=" Height ">
                                            </div>
                                        </div>

                                        <div class="form-horizontal col-md-12">
                                            <label class="control-label col-sm-2" for="email">চোখের রং:</label>
                                            <div class="col-sm-10">
                                                <input class="form-control  " id="eye_color" value="{{$ansarAllDetails->eye_color }}" type="text" disabled  placeholder="চোখের রং">
                                            </div>

                                        </div>
                                        <div class="form group col-md-12">
                                            <label class="control-label col-sm-2" for="email">গায়ের রং:</label>
                                            <div class="col-sm-10">
                                                <input class="form-control  " id="skin_color" value="{{$ansarAllDetails->skin_color}}" type="text" disabled  placeholder="গায়ের রং">
                                            </div>
                                        </div>

                                        <div class="form group col-md-12">
                                            <label class="control-label col-sm-2" for="email">Sex:</label>
                                            <div class="col-sm-10">
                                                <input class="form-control  " id="skin_color" value="{{$ansarAllDetails->sex}}" type="text" disabled  placeholder="গায়ের রং">
                                            </div>
                                        </div>

                                        <div class="form group col-md-12">
                                            <label class="control-label col-sm-2" for="email">সনাক্তকরন চিহ্ন:</label>
                                            <div class="col-sm-10">
                                                <input class="form-control  " id="identification_mark" value="{{$ansarAllDetails->identification_mark}}" type="text" disabled  placeholder="সনাক্তকরন চিহ্ন">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </fieldset>


                            <fieldset>


                                <div class="level-title-session-entry">
                                    <h5 style="text-align: center;">শিক্ষাগত যোগ্যতার তথ্য</h5>
                                </div>
                                <div class="box-info">
                                    <div class="box-body">
                                        <div class="table-responsive">
                                            <table class="table">
                                                <tr >
                                                    <th>শিক্ষাগত যোগ্যতা</th>
                                                    <th>শিক্ষা প্রতিষ্ঠানের নাম</th>
                                                    <th>Passing year</th>
                                                    <th>বিভাগ / শ্রেণী</th>

                                                </tr>
                                                @foreach ($ansarAllDetails->education as $singleeducation)
                                                    <tr >
                                                        <td><input type="text" value="{{ $singleeducation->name_of_degree }}" disabled  placeholder="শিক্ষাগত যোগ্যতা"></td>
                                                        <td><input type="text" value="{{ $singleeducation->institute_name }}" disabled  placeholder="শিক্ষা প্রতিষ্ঠানের নাম"></td>
                                                        <td><input type="text" value="{{ $singleeducation->passing_year }}" disabled  placeholder="Passing year"></td>
                                                        <td><input type="text" value="{{ $singleeducation->gade_divission }}" disabled  placeholder="বিভাগ / শ্রেণী"></td>

                                                    </tr>
                                                @endforeach

                                            </table>


                                        </div>
                                    </div>
                                </div>
                            </fieldset>


                            <fieldset>

                                <div class="level-title-session-entry">
                                    <h5 style="text-align: center;">প্রশিক্ষন সংক্রান্ত তথ্য্য</h5>
                                </div>
                                <div class="box-info" >
                                    <div class="box-body">
                                        <div class=" table-responsive">
                                            <table class="table">
                                                <tr>
                                                    <th>পদবী</th>
                                                    <th>প্রতিষ্ঠান</th>
                                                    <th>Training start date</th>
                                                    <th>Training end date</th>
                                                    <th>সনদ নং</th>

                                                </tr>
                                                @foreach ($ansarAllDetails->training as $singletraining)
                                                    <tr >
                                                        <td><input type="text" value="{{ $singletraining->designation}}" disabled  placeholder="পদবী"></td>
                                                        <td><input type="text" value="{{$singletraining->training_institute_name}}" disabled  placeholder="প্রতিষ্ঠান"></td>
                                                        <td><input type="text" value="{{ $singletraining->training_start_date}}" disabled  placeholder="Training start date"></td>
                                                        <td><input type="text" value="{{ $singletraining->training_end_date }}" disabled  placeholder="Training end date"></td>
                                                        <td><input type="text" value="{{ $singletraining->trining_certificate_no }}" disabled  placeholder="সনদ নং"></td>

                                                    </tr>
                                                @endforeach
                                            </table>


                                        </div>
                                    </div>
                                </div>
                            </fieldset>


                            <fieldset>

                                <div class="level-title-session-entry">
                                    <h5 style="text-align: center;">উত্তরাধিকারীর তথ্য</h5>
                                </div>
                                <div class="box-info">
                                    <div class="box-body">
                                        <div class="table-responsive">
                                            <table class="table">
                                                <tr>
                                                    <th>নাম</th>
                                                    <th>সম্পর্ক</th>
                                                    <th>Percentage(%)</th>
                                                    <th>মোবাইল নং</th>

                                                </tr>
                                                @foreach ($ansarAllDetails->nominee as $singlenominee)
                                                    <tr >
                                                        <td><input type="text" value="{{$singlenominee->name_of_nominee}}" disabled  placeholder="নাম"></td>
                                                        <td><input type="text" value="{{$singlenominee->relation_with_nominee}}" disabled  placeholder="সম্পর্ক"></td>
                                                        <td><input type="text" value="{{$singlenominee->nominee_parcentage}}" disabled  placeholder="Percentage"></td>
                                                        <td><input type="text" value="{{$singlenominee->nominee_contact_no}}" disabled  placeholder="মোবাইল নং"></td>

                                                    </tr>
                                                @endforeach
                                            </table>


                                        </div>
                                    </div>
                                </div>
                            </fieldset>


                            <fieldset>
                                <div class="level-title-session-entry">
                                    <h5 style="text-align: center;">অন্যান্য তথ্য</h5>
                                </div>
                                <div class="box-info">
                                    <div class="box-body">
                                        <div class="form-horizontal col-md-12">
                                            <label class="control-label col-sm-2" for="email">Land phone no(self):</label>
                                            <div class="col-sm-10">
                                                <input class="form-control  " id="land_phone_self" value="{{$ansarAllDetails->land_phone_self }}" type="text" disabled  placeholder="Land phone no(self)">
                                            </div>
                                        </div>
                                        <div class="form group col-md-12">
                                            <label class="control-label col-sm-2" for="email">Land phone no(request):</label>
                                            <div class="col-sm-10">
                                                <input class="form-control  " id="land_phone_request" value="{{$ansarAllDetails->land_phone_request}}" type="text" disabled  placeholder="Land phone no(request)">
                                            </div>


                                        </div>

                                        <div class="form-horizontal col-md-12">
                                            <label class="control-label col-sm-2" for="email">Mobile no(Self):</label>
                                            <div class="col-sm-10">
                                                <input class="form-control  " id="mobile_no_self" value="{{$ansarAllDetails->mobile_no_self}}" type="text" disabled  placeholder="Mobile no(Self)">
                                            </div>


                                        </div>
                                        <div class="form-horizontal col-md-12">
                                            <label class="control-label col-sm-2" for="email">Mobile no(Request):</label>
                                            <div class="col-sm-10">
                                                <input class="form-control  " id="mobile_no_request" value="{{$ansarAllDetails->mobile_no_request}}" type="text" disabled  placeholder="Mobile no(Request)">
                                            </div>

                                        </div>
                                        <div class="form-horizontal col-md-12">
                                            <label class="control-label col-sm-2" for="email">Email(Self):</label>
                                            <div class="col-sm-10">
                                                <input class="form-control  " id="email_self" value="{{$ansarAllDetails->email_self}}" type="text" disabled  placeholder="Email(Self)">
                                            </div>


                                        </div>
                                        <div class="form-horizontal col-md-12">
                                            <label class="control-label col-sm-2" for="email">Email(Request):</label>
                                            <div class="col-sm-10">
                                                <input class="form-control  " id="email_request" value="{{$ansarAllDetails->email_request}}" type="text" disabled  placeholder="Email(Request)">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </fieldset>


                        </form>


                    </div>

                </div>
            </div>
        </section>
    </div>


@stop
