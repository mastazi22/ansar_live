<div class="row">
    <div class="col-md-4">
        <img class="img-thumbnail img-responsive profile-image"
             src="{{action('UserController@getImage',['file'=>$ansarAllDetails->profile_pic])}}"
             style="margin:0 auto;width:80%;"/>
    </div>
    <div class="col-md-6 col-md-offset-2">
        <table class="table borderless">
            <tr>
                <td><b>{{$label->id[$type]}}</b></td>
                <td>{{ $ansarAllDetails->ansar_id }}</td>
            </tr>
            <tr>
                <td><b>{{$label->name[$type]}} </b></td>
                <td>{{ $ansarAllDetails->{"ansar_name_".$type} }}</td>
            </tr>
            <tr>
                <td><b>{{$label->bd[$type]}}</b></td>
                <td>@if($type=='bng')[[changeToLocal('{{$ansarAllDetails->data_of_birth}}')]] @else {{\Carbon\Carbon::parse($ansarAllDetails->data_of_birth)->format('d-M-Y')}} @endif</td>
            </tr>
            <tr>
                <td><b>{{$label->rank[$type]}}</b></td>
                <td>{{ $ansarAllDetails->designation->{"name_".$type} }}</td>
            </tr>
            <tr>
                <td><b>{{$label->mn[$type]}}</b></td>
                <td style="word-break: break-all">{{$type=='bng'?LanguageConverter::engToBng($ansarAllDetails->mobile_no_self):$ansarAllDetails->mobile_no_self}}</td>
            </tr>

        </table>
    </div>
    <div class="col-md-12" style="margin-bottom: 10px;">
        <fieldset class="fieldset">
            <legend class="legend">{{$title->personal_info[$type] }}:</legend>
            <div class="col-md-6">
                <table class="table borderless">
                    <tr>
                        <td><b>{{$label->fn[$type]}}</b></td>
                        <td>{{ $ansarAllDetails->{"father_name_".$type} }}</td>
                    </tr>
                    <tr>
                        <td><b>{{$label->ms[$type]}}</b></td>
                        <td>{{ $type=='bng'?(strcasecmp($ansarAllDetails->marital_status,"married")==0?"বিবাহিত":(strcasecmp($ansarAllDetails->marital_status,"unmarried")==0?"অবিবাহিত":"তালাকপ্রাপ্ত")):$ansarAllDetails->marital_status}}</td>
                    </tr>

                    <tr>
                        <td><b>{{$label->nin[$type]}}</b></td>
                        <td style="word-break: break-all">{{ $type=='bng'?LanguageConverter::engToBng($ansarAllDetails->national_id_no):$ansarAllDetails->national_id_no }}</td>
                    </tr>

                </table>
            </div>

            <div class="col-md-6">
                <table class="table borderless">

                    <tr>
                        <td><b>{{$label->mtn[$type]}}</b></td>
                        <td>{{ $ansarAllDetails->{"mother_name_".$type} }}</td>
                    </tr>
                    <tr>
                        <td><b>{{$label->hwn[$type]}} </b></td>
                        <td>{{ $ansarAllDetails->{"spouse_name_".$type} }}</td>
                    </tr>
                    <tr>
                        <td><b>{{$label->bc[$type]}}</b></td>
                        <td style="word-break: break-all">{{ $ansarAllDetails->birth_certificate_no}}</td>
                    </tr>

                </table>
            </div>

        </fieldset>
    </div>

    <div class="col-md-12" style="margin-bottom: 10px;">
        <fieldset class="fieldset">
            <legend class="legend">{{$title->permanent_address[$type] }}:</legend>
            <div class="col-md-6">
                <table class="table borderless">
                    <tr>
                        <td><b>{{$label->vv[$type]}}</b></td>
                        <td>{{$ansarAllDetails->{"village_name_".$type} }}</td>
                    </tr>
                    <tr>
                        <td><b>{{$label->un[$type]}}</b></td>
                        <td>{{$ansarAllDetails->{"union_name_".$type}  }}</td>

                    </tr>
                    <tr>
                        <td><b>{{$label->ds[$type]}}</b></td>
                        <td>{{$ansarAllDetails->district->{"unit_name_".$type} }}</td>

                    </tr>


                </table>
            </div>

            <div class="col-md-6">
                <table class="table borderless">
                    <tr>
                        <td><b>{{$label->po[$type]}} </b></td>
                        <td>{{$ansarAllDetails->{"post_office_name_".$type} }}</td>

                    </tr>
                    <tr>
                        <td><b>{{$label->th[$type]}}</b></td>
                        <td>{{$ansarAllDetails->thana->{"thana_name_".$type} }}</td>
                    </tr>
                    <tr>
                        <td><b>{{$label->dv[$type]}}</b></td>
                        <td>{{$ansarAllDetails->division->{"division_name_".$type} }}</td>
                    </tr>

                </table>
            </div>

        </fieldset>
    </div>

    <div class="col-md-12" style="margin-bottom: 10px;">
        <fieldset class="fieldset">
            <legend class="legend">{{$title->physical_info[$type] }}</legend>
            <div class="col-md-6">
                <table class="table borderless">
                    <tr>
                        <td><b>{{$label->hh[$type]}}</b></td>
                        <td>
                            @if($type=='bng')
                                {{LanguageConverter::engToBng($ansarAllDetails->hight_feet)}}
                                '{{LanguageConverter::engToBng($ansarAllDetails->hight_inch)}}"
                            @else
                                {{$ansarAllDetails->hight_feet}}
                                '{{$ansarAllDetails->hight_inch}}"
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td><b>{{$label->bg[$type]}}</b></td>
                        <td>{{ $ansarAllDetails->blood->{"blood_group_name_".$type} }}</td>
                    </tr>
                    <tr>
                        <td><b>{{$label->ec[$type]}}</b></td>
                        <td>{{$ansarAllDetails->eye_color }}</td>
                    </tr>


                </table>
            </div>

            <div class="col-md-6">
                <table class="table borderless">
                    <tr>
                        <td><b>{{$label->bc[$type]}}</b></td>
                        <td>{{$ansarAllDetails->{"skin_color_".$type} }}</td>
                    </tr>
                    <tr>
                        <td><b>{{$label->gen[$type]}}</b></td>
                        <td>{{strcasecmp($ansarAllDetails->sex,"Male")==0?"পুরুষ":(strcasecmp($ansarAllDetails->sex,"Female")==0?"মহিলা":"অন্যান্য")}}</td>
                    </tr>
                    <tr>
                        <td><b>{{$label->im[$type]}}</b></td>
                        <td>{{$ansarAllDetails->identification_mark}}</td>
                    </tr>

                </table>
            </div>

        </fieldset>
    </div>

    <div class="col-md-12" style="margin-bottom: 10px;">
        <fieldset class="fieldset">
            <legend class="legend">{{$title->edu_info[$type] }}</legend>
            <table class="table borderless">
                <tr>
                    <td><b>{{$label->eq[$type]}}</b></td>
                    <td><b>{{$label->in[$type]}}</b></td>
                    <td><b>{{$label->py[$type]}}</b></td>
                    <td><b>{{$label->dc[$type]}}</b></td>
                </tr>
                @foreach($ansarAllDetails->education as $singleeducation)

                    <tr>
                        <td>{{ $singleeducation->educationName->{"education_deg_".$type}  }}</td>
                        <td>{{ $singleeducation->institute_name }}</td>
                        <td>{{ $type=='bng'?LanguageConverter::engToBng($singleeducation->passing_year):$singleeducation->passing_year }}</td>
                        <td>{{ $singleeducation->gade_divission }}</td>
                    </tr>
                @endforeach
            </table>
        </fieldset>
    </div>

    <div class="col-md-12" style="margin-bottom: 10px;">
        <fieldset class="fieldset">
            <legend class="legend">{{$title->train_info[$type] }}</legend>
            <table class="table borderless">
                <tr>
                    <td><b>{{$label->rank[$type]}}</b></td>
                    <td><b>{{$label->tin[$type]}}</b></td>
                    <td><b>{{$label->tsd[$type]}}</b></td>
                    <td><b>{{$label->ted[$type]}}</b></td>
                    <td><b>{{$label->cn[$type]}}</b></td>
                </tr>
                @foreach ($ansarAllDetails->training as $singletraining)
                    <tr>
                        <td>{{ $singletraining->rank->name_bng }}</td>
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
            <legend class="legend">{{$title->nominee_info[$type] }}</legend>
            <table class="table borderless">
                <tr>
                    <td><b>{{$label->name[$type]}}</b></td>
                    <td><b>{{$label->rel[$type]}}</b></td>
                    <td><b>{{$label->per[$type]}}</b></td>
                    <td><b>{{$label->mn[$type]}}</b></td>
                </tr>
                @foreach ($ansarAllDetails->nominee as $singlenominee)
                    <tr>
                        <td>{{$type=='bng'?$singlenominee->name_of_nominee:$singlenominee->name_of_nominee_eng}}</td>
                        <td>{{$singlenominee->relation_with_nominee}}</td>
                        <td>{{$singlenominee->nominee_parcentage}}</td>
                        <td>{{$type=='bng'?LanguageConverter::engToBng($singlenominee->nominee_contact_no):$singlenominee->nominee_contact_no}}</td>
                    </tr>
                @endforeach
            </table>
        </fieldset>
    </div>

    <div class="col-md-12" style="margin-bottom: 10px;">
        <fieldset class="fieldset">
            <legend class="legend">{{$title->other_info[$type] }}</legend>
            <div class="col-md-6">
                <table class="table borderless">
                    <tr>
                        <td><b>Mobile no(Self)</b></td>
                        <td style="word-break: break-all">{{$type=='bng'?LanguageConverter::engToBng($ansarAllDetails->mobile_no_self):$ansarAllDetails->mobile_no_self}}</td>
                    </tr>
                    <tr>
                        <td><b>Land phone no(self)</b></td>
                        <td>{{$type=='bng'?LanguageConverter::engToBng($ansarAllDetails->land_phone_self):$ansarAllDetails->land_phone_self }}</td>
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
                        <td>{{$type=='bng'?LanguageConverter::engToBng($ansarAllDetails->mobile_no_request):$ansarAllDetails->mobile_no_request}}</td>
                    </tr>
                    <tr>
                        <td><b>Land phone no(request)</b></td>
                        <td>{{$type=='bng'?LanguageConverter::engToBng($ansarAllDetails->land_phone_request):$ansarAllDetails->land_phone_request}}</td>
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
            <legend class="legend">Signature image</legend>
            <img class="img-thumbnail"
                 src="{{URL::route('sign_image',['id'=>$ansarAllDetails->ansar_id])}}"
                 style="height:80px;width:100%;"/>
        </fieldset>
    </div>
    <div class="col-md-6" style="margin-bottom: 10px;">
        <fieldset class="fieldset">
            <legend class="legend">Thumb image</legend>
            <img class="img-thumbnail" src="{{URL::route('thumb_image',['id'=>$ansarAllDetails->ansar_id])}}" style="height:80px;width:100%;"/>
        </fieldset>
    </div>
</div>