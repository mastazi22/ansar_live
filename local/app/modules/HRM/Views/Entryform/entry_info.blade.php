{{--<div class="row">--}}
{{--<div class="col-md-4">--}}
{{--<img class="img-thumbnail img-responsive profile-image"--}}
{{--src="{{action('UserController@getImage',['file'=>$ansarAllDetails->profile_pic])}}"--}}
{{--style="margin:0 auto;width:80%;"/>--}}
{{--</div>--}}
{{--<div class="col-md-6 col-md-offset-2">--}}
{{--<table class="table borderless">--}}
{{--<tr>--}}
{{--<td><b>{{$label->id[$type]}}</b></td>--}}
{{--<td>{{ $ansarAllDetails->ansar_id }}</td>--}}
{{--</tr>--}}
{{--<tr>--}}
{{--<td><b>{{$label->name[$type]}} </b></td>--}}
{{--<td>{{ $ansarAllDetails->{"ansar_name_".$type} }}</td>--}}
{{--</tr>--}}
{{--<tr>--}}
{{--<td><b>{{$label->bd[$type]}}</b></td>--}}
{{--<td>@if($type=='bng')[[changeToLocal('{{$ansarAllDetails->data_of_birth}}')]] @else {{\Carbon\Carbon::parse($ansarAllDetails->data_of_birth)->format('d-M-Y')}} @endif</td>--}}
{{--</tr>--}}
{{--<tr>--}}
{{--<td><b>{{$label->rank[$type]}}</b></td>--}}
{{--<td>{{ $ansarAllDetails->designation->{"name_".$type} }}</td>--}}
{{--</tr>--}}
{{--<tr>--}}
{{--<td><b>{{$label->mn[$type]}}</b></td>--}}
{{--<td style="word-break: break-all">{{$type=='bng'?LanguageConverter::engToBng($ansarAllDetails->mobile_no_self):$ansarAllDetails->mobile_no_self}}</td>--}}
{{--</tr>--}}

{{--</table>--}}
{{--</div>--}}
{{--<div class="col-md-12" style="margin-bottom: 10px;">--}}
{{--<fieldset class="fieldset">--}}
{{--<legend class="legend">{{$title->personal_info[$type] }}:</legend>--}}
{{--<div class="col-md-6">--}}
{{--<table class="table borderless">--}}
{{--<tr>--}}
{{--<td><b>{{$label->fn[$type]}}</b></td>--}}
{{--<td>{{ $ansarAllDetails->{"father_name_".$type} }}</td>--}}
{{--</tr>--}}
{{--<tr>--}}
{{--<td><b>{{$label->ms[$type]}}</b></td>--}}
{{--<td>{{ $type=='bng'?(strcasecmp($ansarAllDetails->marital_status,"married")==0?"বিবাহিত":(strcasecmp($ansarAllDetails->marital_status,"unmarried")==0?"অবিবাহিত":"তালাকপ্রাপ্ত")):$ansarAllDetails->marital_status}}</td>--}}
{{--</tr>--}}

{{--<tr>--}}
{{--<td><b>{{$label->nin[$type]}}</b></td>--}}
{{--<td style="word-break: break-all">{{ $type=='bng'?LanguageConverter::engToBng($ansarAllDetails->national_id_no):$ansarAllDetails->national_id_no }}</td>--}}
{{--</tr>--}}

{{--</table>--}}
{{--</div>--}}

{{--<div class="col-md-6">--}}
{{--<table class="table borderless">--}}

{{--<tr>--}}
{{--<td><b>{{$label->mtn[$type]}}</b></td>--}}
{{--<td>{{ $ansarAllDetails->{"mother_name_".$type} }}</td>--}}
{{--</tr>--}}
{{--<tr>--}}
{{--<td><b>{{$label->hwn[$type]}} </b></td>--}}
{{--<td>{{ $ansarAllDetails->{"spouse_name_".$type} }}</td>--}}
{{--</tr>--}}
{{--<tr>--}}
{{--<td><b>{{$label->bc[$type]}}</b></td>--}}
{{--<td style="word-break: break-all">{{ $ansarAllDetails->birth_certificate_no}}</td>--}}
{{--</tr>--}}

{{--</table>--}}
{{--</div>--}}

{{--</fieldset>--}}
{{--</div>--}}

{{--<div class="col-md-12" style="margin-bottom: 10px;">--}}
{{--<fieldset class="fieldset">--}}
{{--<legend class="legend">{{$title->permanent_address[$type] }}:</legend>--}}
{{--<div class="col-md-6">--}}
{{--<table class="table borderless">--}}
{{--<tr>--}}
{{--<td><b>{{$label->vv[$type]}}</b></td>--}}
{{--<td>{{$ansarAllDetails->{"village_name_".$type} }}</td>--}}
{{--</tr>--}}
{{--<tr>--}}
{{--<td><b>{{$label->un[$type]}}</b></td>--}}
{{--<td>{{$ansarAllDetails->{"union_name_".$type}  }}</td>--}}

{{--</tr>--}}
{{--<tr>--}}
{{--<td><b>{{$label->ds[$type]}}</b></td>--}}
{{--<td>{{$ansarAllDetails->district->{"unit_name_".$type} }}</td>--}}

{{--</tr>--}}


{{--</table>--}}
{{--</div>--}}

{{--<div class="col-md-6">--}}
{{--<table class="table borderless">--}}
{{--<tr>--}}
{{--<td><b>{{$label->po[$type]}} </b></td>--}}
{{--<td>{{$ansarAllDetails->{"post_office_name_".$type} }}</td>--}}

{{--</tr>--}}
{{--<tr>--}}
{{--<td><b>{{$label->th[$type]}}</b></td>--}}
{{--<td>{{$ansarAllDetails->thana->{"thana_name_".$type} }}</td>--}}
{{--</tr>--}}
{{--<tr>--}}
{{--<td><b>{{$label->dv[$type]}}</b></td>--}}
{{--<td>{{$ansarAllDetails->division->{"division_name_".$type} }}</td>--}}
{{--</tr>--}}

{{--</table>--}}
{{--</div>--}}

{{--</fieldset>--}}
{{--</div>--}}

{{--<div class="col-md-12" style="margin-bottom: 10px;">--}}
{{--<fieldset class="fieldset">--}}
{{--<legend class="legend">{{$title->physical_info[$type] }}</legend>--}}
{{--<div class="col-md-6">--}}
{{--<table class="table borderless">--}}
{{--<tr>--}}
{{--<td><b>{{$label->hh[$type]}}</b></td>--}}
{{--<td>--}}
{{--@if($type=='bng')--}}
{{--{{LanguageConverter::engToBng($ansarAllDetails->hight_feet)}}--}}
{{--'{{LanguageConverter::engToBng($ansarAllDetails->hight_inch)}}"--}}
{{--@else--}}
{{--{{$ansarAllDetails->hight_feet}}--}}
{{--'{{$ansarAllDetails->hight_inch}}"--}}
{{--@endif--}}
{{--</td>--}}
{{--</tr>--}}
{{--<tr>--}}
{{--<td><b>{{$label->bg[$type]}}</b></td>--}}
{{--<td>{{ $ansarAllDetails->blood->{"blood_group_name_".$type} }}</td>--}}
{{--</tr>--}}
{{--<tr>--}}
{{--<td><b>{{$label->ec[$type]}}</b></td>--}}
{{--<td>{{$ansarAllDetails->eye_color }}</td>--}}
{{--</tr>--}}


{{--</table>--}}
{{--</div>--}}

{{--<div class="col-md-6">--}}
{{--<table class="table borderless">--}}
{{--<tr>--}}
{{--<td><b>{{$label->bc[$type]}}</b></td>--}}
{{--<td>{{$ansarAllDetails->{"skin_color_".$type} }}</td>--}}
{{--</tr>--}}
{{--<tr>--}}
{{--<td><b>{{$label->gen[$type]}}</b></td>--}}
{{--<td>{{strcasecmp($ansarAllDetails->sex,"Male")==0?"পুরুষ":(strcasecmp($ansarAllDetails->sex,"Female")==0?"মহিলা":"অন্যান্য")}}</td>--}}
{{--</tr>--}}
{{--<tr>--}}
{{--<td><b>{{$label->im[$type]}}</b></td>--}}
{{--<td>{{$ansarAllDetails->identification_mark}}</td>--}}
{{--</tr>--}}

{{--</table>--}}
{{--</div>--}}

{{--</fieldset>--}}
{{--</div>--}}

{{--<div class="col-md-12" style="margin-bottom: 10px;">--}}
{{--<fieldset class="fieldset">--}}
{{--<legend class="legend">{{$title->edu_info[$type] }}</legend>--}}
{{--<table class="table borderless">--}}
{{--<tr>--}}
{{--<td><b>{{$label->eq[$type]}}</b></td>--}}
{{--<td><b>{{$label->in[$type]}}</b></td>--}}
{{--<td><b>{{$label->py[$type]}}</b></td>--}}
{{--<td><b>{{$label->dc[$type]}}</b></td>--}}
{{--</tr>--}}
{{--@foreach($ansarAllDetails->education as $singleeducation)--}}

{{--<tr>--}}
{{--<td>{{ $singleeducation->educationName->{"education_deg_".$type}  }}</td>--}}
{{--<td>{{ $singleeducation->institute_name }}</td>--}}
{{--<td>{{ $type=='bng'?LanguageConverter::engToBng($singleeducation->passing_year):$singleeducation->passing_year }}</td>--}}
{{--<td>{{ $singleeducation->gade_divission }}</td>--}}
{{--</tr>--}}
{{--@endforeach--}}
{{--</table>--}}
{{--</fieldset>--}}
{{--</div>--}}

{{--<div class="col-md-12" style="margin-bottom: 10px;">--}}
{{--<fieldset class="fieldset">--}}
{{--<legend class="legend">{{$title->train_info[$type] }}</legend>--}}
{{--<table class="table borderless">--}}
{{--<tr>--}}
{{--<td><b>{{$label->rank[$type]}}</b></td>--}}
{{--<td><b>{{$label->tin[$type]}}</b></td>--}}
{{--<td><b>{{$label->tsd[$type]}}</b></td>--}}
{{--<td><b>{{$label->ted[$type]}}</b></td>--}}
{{--<td><b>{{$label->cn[$type]}}</b></td>--}}
{{--</tr>--}}
{{--@foreach ($ansarAllDetails->training as $singletraining)--}}
{{--<tr>--}}
{{--<td>{{ $singletraining->rank->name_bng }}</td>--}}
{{--<td>{{$singletraining->training_institute_name}}</td>--}}
{{--<td>[[changeToLocal('{{ $singletraining->training_start_date}}')]]--}}
{{--<td>[[changeToLocal('{{ $singletraining->training_end_date }}')]]</td>--}}
{{--<td>{{ $singletraining->trining_certificate_no }}</td>--}}
{{--</tr>--}}
{{--@endforeach--}}
{{--</table>--}}
{{--</fieldset>--}}
{{--</div>--}}

{{--<div class="col-md-12" style="margin-bottom: 10px;">--}}
{{--<fieldset class="fieldset">--}}
{{--<legend class="legend">{{$title->nominee_info[$type] }}</legend>--}}
{{--<table class="table borderless">--}}
{{--<tr>--}}
{{--<td><b>{{$label->name[$type]}}</b></td>--}}
{{--<td><b>{{$label->rel[$type]}}</b></td>--}}
{{--<td><b>{{$label->per[$type]}}</b></td>--}}
{{--<td><b>{{$label->mn[$type]}}</b></td>--}}
{{--</tr>--}}
{{--@foreach ($ansarAllDetails->nominee as $singlenominee)--}}
{{--<tr>--}}
{{--<td>{{$type=='bng'?$singlenominee->name_of_nominee:$singlenominee->name_of_nominee_eng}}</td>--}}
{{--<td>{{$singlenominee->relation_with_nominee}}</td>--}}
{{--<td>{{$singlenominee->nominee_parcentage}}</td>--}}
{{--<td>{{$type=='bng'?LanguageConverter::engToBng($singlenominee->nominee_contact_no):$singlenominee->nominee_contact_no}}</td>--}}
{{--</tr>--}}
{{--@endforeach--}}
{{--</table>--}}
{{--</fieldset>--}}
{{--</div>--}}

{{--<div class="col-md-12" style="margin-bottom: 10px;">--}}
{{--<fieldset class="fieldset">--}}
{{--<legend class="legend">{{$title->other_info[$type] }}</legend>--}}
{{--<div class="col-md-6">--}}
{{--<table class="table borderless">--}}
{{--<tr>--}}
{{--<td><b>Mobile no(Self)</b></td>--}}
{{--<td style="word-break: break-all">{{$type=='bng'?LanguageConverter::engToBng($ansarAllDetails->mobile_no_self):$ansarAllDetails->mobile_no_self}}</td>--}}
{{--</tr>--}}
{{--<tr>--}}
{{--<td><b>Land phone no(self)</b></td>--}}
{{--<td>{{$type=='bng'?LanguageConverter::engToBng($ansarAllDetails->land_phone_self):$ansarAllDetails->land_phone_self }}</td>--}}
{{--</tr>--}}
{{--<tr>--}}
{{--<td><b>Email(Self)</b></td>--}}
{{--<td>{{$ansarAllDetails->email_self}}</td>--}}
{{--</tr>--}}


{{--</table>--}}
{{--</div>--}}

{{--<div class="col-md-6">--}}
{{--<table class="table borderless">--}}
{{--<tr>--}}
{{--<td><b>Mobile no(Request)</b></td>--}}
{{--<td>{{$type=='bng'?LanguageConverter::engToBng($ansarAllDetails->mobile_no_request):$ansarAllDetails->mobile_no_request}}</td>--}}
{{--</tr>--}}
{{--<tr>--}}
{{--<td><b>Land phone no(request)</b></td>--}}
{{--<td>{{$type=='bng'?LanguageConverter::engToBng($ansarAllDetails->land_phone_request):$ansarAllDetails->land_phone_request}}</td>--}}
{{--</tr>--}}

{{--<tr>--}}
{{--<td><b>Email(Request)</b></td>--}}
{{--<td>{{$ansarAllDetails->email_request}}</td>--}}
{{--</tr>--}}

{{--</table>--}}
{{--</div>--}}

{{--</fieldset>--}}
{{--</div>--}}

{{--<div class="col-md-6" style="margin-bottom: 10px;">--}}
{{--<fieldset class="fieldset">--}}
{{--<legend class="legend">Signature image</legend>--}}
{{--<img class="img-thumbnail"--}}
{{--src="{{URL::route('sign_image',['id'=>$ansarAllDetails->ansar_id])}}"--}}
{{--style="height:80px;width:100%;"/>--}}
{{--</fieldset>--}}
{{--</div>--}}
{{--<div class="col-md-6" style="margin-bottom: 10px;">--}}
{{--<fieldset class="fieldset">--}}
{{--<legend class="legend">Thumb image</legend>--}}
{{--<img class="img-thumbnail" src="{{URL::route('thumb_image',['id'=>$ansarAllDetails->ansar_id])}}" style="height:80px;width:100%;"/>--}}
{{--</fieldset>--}}
{{--</div>--}}
{{--</div>--}}

<div class="container-fluid">
    <img class="pull-right profile-image"
         src="{{action('UserController@getImage',['file'=>$ansarAllDetails->profile_pic])}}"
         alt="">
    <table class="entry-table" style="width: 100%">
        <tr>
            <td>আইডি কার্ড নম্বর<span class="pull-right">:</span></td>
            <td style="padding-left: 20px">
                <div style="padding:5px;font-size:14px;border:1px solid #ababab">{{$ansarAllDetails->ansar_id}}</div>
            </td>
        </tr>
    </table>
    <table class="entry-table" style="width: 100%">
        <caption style="text-align: center;font-size: 1em;font-weight: bold">বাক্তিগত ও পারিবারিক তথ্য</caption>
        <tr>
            <td>*Name<span class="pull-right">:</span></td>
            <td style="padding-left: 20px">
                <div style="padding:5px;font-size:14px;border:1px solid #ababab">{{$ansarAllDetails->ansar_name_eng}}</div>
            </td>
        </tr>
        <tr>
            <td>*নাম<span class="pull-right">:</span></td>
            <td style="padding-left: 20px">
                <div style="padding:5px;font-size:14px;border:1px solid #ababab">{{$ansarAllDetails->ansar_name_bng}}</div>
            </td>
        </tr>
        <tr>
            <td>*বর্তমান পদবী <span class="pull-right">:</span></td>
            <td style="padding-left: 20px">
                <div style="padding:5px;font-size:14px;border:1px solid #ababab">{{$ansarAllDetails->designation->name_bng}}</div>
            </td>
        </tr>
        <tr>
            <td>*Father's name <span class="pull-right">:</span></td>
            <td style="padding-left: 20px">
                <div style="padding:5px;font-size:14px;border:1px solid #ababab">{{$ansarAllDetails->father_name_eng}}</div>
            </td>
        </tr>
        <tr>
            <td>*পিতার নাম <span class="pull-right">:</span></td>
            <td style="padding-left: 20px">
                <div style="padding:5px;font-size:14px;border:1px solid #ababab">{{$ansarAllDetails->father_name_bng}}</div>
            </td>
        </tr>
        <tr>
            <td>*Mother's Name <span class="pull-right">:</span></td>
            <td style="padding-left: 20px">
                <div style="padding:5px;font-size:14px;border:1px solid #ababab">{{$ansarAllDetails->mother_name_eng}}</div>
            </td>
        </tr>
        <tr>
            <td>*মাতার নাম <span class="pull-right">:</span></td>
            <td style="padding-left: 20px">
                <div style="padding:5px;font-size:14px;border:1px solid #ababab">{{$ansarAllDetails->mother_name_bng}}</div>
            </td>
        </tr>
        <tr>
            <td>*Date of birth <span class="pull-right">:</span></td>
            <td style="padding-left: 20px">
                <div style="padding:5px;font-size:14px;border:1px solid #ababab">{{\Carbon\Carbon::parse($ansarAllDetails->data_of_birth)->format("d-m-Y")}}</div>
            </td>
        </tr>
        <tr>
            <td>*Marital status <span class="pull-right">:</span></td>
            <td style="padding-left: 20px">
                <div style="padding:5px;font-size:14px;border:1px solid #ababab">{{strcasecmp($ansarAllDetails->marital_status,"married")==0?"বিবাহিত":(strcasecmp($ansarAllDetails->marital_status,"unmarried")==0?"অবিবাহিত":"তালাকপ্রাপ্ত")}}</div>
            </td>
        </tr>
        <tr>
            <td>*Spouse Name <span class="pull-right">:</span></td>
            <td style="padding-left: 20px">
                <div style="padding:5px;font-size:14px;border:1px solid #ababab">{{$ansarAllDetails->spouse_name_eng?$ansarAllDetails->spouse_name_eng: '&nbsp;'}}</div>
            </td>
        </tr>
        <tr>
            <td>*স্ত্রী/স্বামীর নাম <span class="pull-right">:</span></td>
            <td style="padding-left: 20px">
                <div style="padding:5px;font-size:14px;border:1px solid #ababab">{{$ansarAllDetails->spouse_name_bng or ' '}}</div>
            </td>
        </tr>
        <tr>
            <td>*National Id no <span class="pull-right">:</span></td>
            <td style="padding-left: 20px">
                <div style="padding:5px;font-size:14px;border:1px solid #ababab">{{$ansarAllDetails->national_id_no?$ansarAllDetails->national_id_no:'&nbsp;'}}</div>
            </td>
        </tr>
        <tr>
            <td>*Birth Certificate no <span class="pull-right">:</span></td>
            <td style="padding-left: 20px">
                <div style="padding:5px;font-size:14px;border:1px solid #ababab">{{ $ansarAllDetails->birth_certificate_no?$ansarAllDetails->birth_certificate_no:' '}}</div>
            </td>
        </tr>
        <tr>
            <td>দীর্ঘ মেয়াদি অসুখ <span class="pull-right">:</span></td>
            <td style="padding-left: 20px">
                <div style="padding:5px;font-size:14px;border:1px solid #ababab">{{$ansarAllDetails->alldisease->disease_name_bng or $ansarAllDetails->own_disease?$ansarAllDetails->own_disease:'&nbsp;'}}</div>
            </td>
        </tr>
        <tr>
            <td>নির্দিষ্ট দক্ষতা <span class="pull-right">:</span></td>
            <td style="padding-left: 20px">
                <div style="padding:5px;font-size:14px;border:1px solid #ababab">{{$ansarAllDetails->allskill->skill_name_bng}}
                    &nbsp;</div>
            </td>
        </tr>
        <tr>
            <td>Criminal Case<span class="pull-right">:</span></td>
            <td style="padding-left: 20px">
                <div style="padding:5px;font-size:14px;border:1px solid #ababab">{{$ansarAllDetails->criminal_case?$ansarAllDetails->criminal_case:"&nbsp;"}}</div>
            </td>
        </tr>
        <tr>
            <td>ফৌজদারি মামলা<span class="pull-right">:</span></td>
            <td style="padding-left: 20px">
                <div style="padding:5px;font-size:14px;border:1px solid #ababab">{{$ansarAllDetails->criminal_case_bng?$ansarAllDetails->criminal_case_bng:"&nbsp;"}}</div>
            </td>
        </tr>
    </table>
    <table class="entry-table" style="width: 100%">
        <caption style="text-align: center;font-size: 1em;font-weight: bold">স্থায়ী ঠিকানা</caption>
        <tr>
            <td>Village/House No<span class="pull-right">:</span></td>
            <td style="padding-left: 20px">
                <div style="padding:5px;font-size:14px;border:1px solid #ababab">{{$ansarAllDetails->village_name}}
                    &nbsp;</div>
            </td>
        </tr>
        <tr>
            <td>গ্রাম/বাড়ি নং<span class="pull-right">:</span></td>
            <td style="padding-left: 20px">
                <div style="padding:5px;font-size:14px;border:1px solid #ababab">{{$ansarAllDetails->village_name_bng}}
                    &nbsp;</div>
            </td>
        </tr>
        <tr>
            <td>Road No <span class="pull-right">:</span></td>
            <td style="padding-left: 20px">
                <div style="padding:5px;font-size:14px;border:1px solid #ababab">{{$ansarAllDetails->road_no or '&nbsp;'}}
                    &nbsp;</div>
            </td>
        </tr>
        <tr>
            <td>Post office <span class="pull-right">:</span></td>
            <td style="padding-left: 20px">
                <div style="padding:5px;font-size:14px;border:1px solid #ababab">{{$ansarAllDetails->post_office_name}}
                    &nbsp;</div>
            </td>
        </tr>
        <tr>
            <td>ডাকঘর <span class="pull-right">:</span></td>
            <td style="padding-left: 20px">
                <div style="padding:5px;font-size:14px;border:1px solid #ababab">{{$ansarAllDetails->post_office_name_bng}}
                    &nbsp;</div>
            </td>
        </tr>
        <tr>
            <td>Union/Word <span class="pull-right">:</span></td>
            <td style="padding-left: 20px">
                <div style="padding:5px;font-size:14px;border:1px solid #ababab">{{$ansarAllDetails->union_name_eng}}
                    &nbsp;</div>
            </td>
        </tr>
        <tr>
            <td>ইউনিয়ন নাম/ওয়ার্ড <span class="pull-right">:</span></td>
            <td style="padding-left: 20px">
                <div style="padding:5px;font-size:14px;border:1px solid #ababab">{{$ansarAllDetails->union_name_bng}}
                    &nbsp;</div>
            </td>
        </tr>
        <tr>
            <td>*বিভাগ <span class="pull-right">:</span></td>
            <td style="padding-left: 20px">
                <div style="padding:5px;font-size:14px;border:1px solid #ababab">{{$ansarAllDetails->division->division_name_bng}}
                    &nbsp;</div>
            </td>
        </tr>
        <tr>
            <td>*জেলা <span class="pull-right">:</span></td>
            <td style="padding-left: 20px">
                <div style="padding:5px;font-size:14px;border:1px solid #ababab">{{$ansarAllDetails->district->unit_name_bng}}</div>
            </td>
        </tr>
        <tr>
            <td>*থানা <span class="pull-right">:</span></td>
            <td style="padding-left: 20px">
                <div style="padding:5px;font-size:14px;border:1px solid #ababab">{{$ansarAllDetails->thana->thana_name_bng}}</div>
            </td>
        </tr>
    </table>
    <table class="entry-table" style="width: 100%">
        <caption style="text-align: center;font-size: 1em;font-weight: bold">শারীরিক যোগ্যতার তথ্য</caption>
        <tr>
            <td>*Height(উচ্চতা)<span class="pull-right">:</span></td>
            <td style="padding-left: 20px">
                <div style="padding:0 5px;font-size:14px;border:1px solid #ababab">
                    <span style="padding: 5px 20px">{{$ansarAllDetails->hight_feet}}</span>
                    <span style="padding: 0 5px;border: 1px solid #ababab;border-top: none;border-bottom: none">ফিট</span>
                    <span style="padding: 5px 20px">{{$ansarAllDetails->hight_inch}}</span>
                    <span style="padding: 0 5px;border: 1px solid #ababab;border-top: none;border-bottom: none">ইঞ্চি</span>
                </div>
            </td>
        </tr>
        <tr>
            <td>*রক্তের গ্রুপ<span class="pull-right">:</span></td>
            <td style="padding-left: 20px">
                <div style="padding:5px;font-size:14px;border:1px solid #ababab">{{$ansarAllDetails->blood->blood_group_name_bng}}</div>
            </td>
        </tr>
        <tr>
            <td>Eye color<span class="pull-right">:</span></td>
            <td style="padding-left: 20px">
                <div style="padding:5px;font-size:14px;border:1px solid #ababab">{{$ansarAllDetails->eye_color}}
                    &nbsp;</div>
            </td>
        </tr>
        <tr>
            <td>চোখের রং <span class="pull-right">:</span></td>
            <td style="padding-left: 20px">
                <div style="padding:5px;font-size:14px;border:1px solid #ababab">{{$ansarAllDetails->eye_color_bng}}
                    &nbsp;</div>
            </td>
        </tr>
        <tr>
            <td>Skin color<span class="pull-right">:</span></td>
            <td style="padding-left: 20px">
                <div style="padding:5px;font-size:14px;border:1px solid #ababab">{{$ansarAllDetails->skin_color}}
                    &nbsp;</div>
            </td>
        </tr>
        <tr>
            <td>গায়ের রং<span class="pull-right">:</span></td>
            <td style="padding-left: 20px">
                <div style="padding:5px;font-size:14px;border:1px solid #ababab">{{$ansarAllDetails->skin_color_bng}}
                    &nbsp;</div>
            </td>
        </tr>
        <tr>
            <td>*Gender<span class="pull-right">:</span></td>
            <td style="padding-left: 20px">
                <div style="padding:5px;font-size:14px;border:1px solid #ababab">{{$ansarAllDetails->sex}}&nbsp;</div>
            </td>
        </tr>
        <tr>
            <td>Identification mark <span class="pull-right">:</span></td>
            <td style="padding-left: 20px">
                <div style="padding:5px;font-size:14px;border:1px solid #ababab">{{$ansarAllDetails->identification_mark}}
                    &nbsp;</div>
            </td>
        </tr>
        <tr>
            <td>সনাক্তকরন চিহ্ন<span class="pull-right">:</span></td>
            <td style="padding-left: 20px">
                <div style="padding:5px;font-size:14px;border:1px solid #ababab">{{$ansarAllDetails->identification_mark_bng}}
                    &nbsp;</div>
            </td>
        </tr>
    </table>
    <table class="entry-table border-table">
        <caption>Educational Information*</caption>
        <tbody>
        <tr>
            <td><b>Education Qualification</b></td>
            <td><b>Institute Name</b></td>
            <td><b>Passing Year</b></td>
            <td><b>Division/Grade</b></td>
        </tr>

        @foreach($ansarAllDetails->education as $singleeducation)

            <tr>
                <td>{{ $singleeducation->educationName->education_deg_eng  }}</td>
                <td>{{ $singleeducation->institute_name_eng }}</td>
                <td>{{ $singleeducation->passing_year or LanguageConverter::engToBng($singleeducation->passing_year)}}</td>
                <td>{{ $singleeducation->gade_divission }}</td>
            </tr>
        @endforeach
        </tbody>
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

        @foreach($ansarAllDetails->education as $singleeducation)

            <tr>
                <td>{{ $singleeducation->educationName->education_deg_bng  }}</td>
                <td>{{ $singleeducation->institute_name }}</td>
                <td>{{ LanguageConverter::engToBng($singleeducation->passing_year)}}</td>
                <td>{{ $singleeducation->gade_divission }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
    <table class="entry-table border-table">
        <caption>Training Information</caption>
        <tbody>
        <tr>
            <td><b>Rank</b></td>
            <td><b>Institute Name</b></td>
            <td><b>Training Starting Date</b></td>
            <td><b>Training Ending Date</b></td>
            <td><b>Certificate No.</b></td>
        </tr>
        @foreach ($ansarAllDetails->training as $singletraining)
            <tr>
                <td>{{ $singletraining->rank->name_eng }}</td>
                <td>{{$singletraining->training_institute_name_eng}}</td>
                <td>[['{{ $singletraining->training_start_date}}'|dateformat:"DD-MMM-YYYY"]]
                <td>[['{{ $singletraining->training_end_date }}'|dateformat:"DD-MMM-YYYY"]]</td>
                <td>{{ $singletraining->trining_certificate_no }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
    <table class="entry-table border-table">
        <caption>প্রশিক্ষন সংক্রান্ত তথ্য্</caption>
        <tbody>
        <tr>
            <td><b>পদবী</b></td>
            <td><b>প্রতিষ্ঠান </b></td>
            <td><b>প্রশিক্ষন শুরুর তারিখ </b></td>
            <td><b>প্রশিক্ষন শেষের তারিখ </b></td>
            <td><b>সনদ নং </b></td>
        </tr>
        @foreach ($ansarAllDetails->training as $singletraining)
            <tr>
                <td>{{ $singletraining->rank->name_bng }}</td>
                <td>{{$singletraining->training_institute_name}}</td>
                <td>[['{{ $singletraining->training_start_date}}'|dateformat:"DD-MMMM-YYYY":"bn"]]
                <td>[['{{ $singletraining->training_end_date }}'|dateformat:"DD-MMMM-YYYY":"bn"]]</td>
                <td>{{ $singletraining->trining_certificate_no }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
    <table class="entry-table border-table">
        <caption>Nominee Information</caption>
        <tbody>
        <tr>
            <td><b>Name</b></td>
            <td><b>Relation</b></td>
            <td><b>Percentage</b></td>
            <td><b>Mobile No.</b></td>
        </tr>
        @foreach ($ansarAllDetails->nominee as $singlenominee)
            <tr>
                <td>{{$singlenominee->name_of_nominee_eng}}</td>
                <td>{{$singlenominee->relation_with_nominee_eng}}</td>
                <td>{{$singlenominee->nominee_parcentage_eng}}</td>
                <td>{{$singlenominee->nominee_contact_no}}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
    <table class="entry-table border-table">
        <caption>
            উত্তরাধিকারীর তথ্য
        </caption>
        <tbody>
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
        </tbody>
    </table>

    <table class="entry-table border-table">
        <caption>Nominee Information</caption>
        <tbody>
        <tr>
            <td><b>Name</b></td>
            <td><b>Relation</b></td>
            <td><b>Percentage</b></td>
            <td><b>Mobile No.</b></td>
        </tr>
        @foreach ($ansarAllDetails->nominee as $singlenominee)
            <tr>
                <td>{{$singlenominee->name_of_nominee_eng}}</td>
                <td>{{$singlenominee->relation_with_nominee_eng}}</td>
                <td>{{$singlenominee->nominee_parcentage_eng}}</td>
                <td>{{$singlenominee->nominee_contact_no}}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
    <table class="entry-table other-table">
        <caption>অন্যান্য তথ্য</caption>
        <tr>
            <td>Mobile No. (Self) নিজ* <span class="pull-right">:</span></td>
            <td><div style="font-size:14px;">{{$ansarAllDetails->mobile_no_self}}</div></td>
        </tr>
        <tr>
            <td>Mobile No. (Request) <span class="pull-right">:</span></td>
            <td><div style="font-size:14px;">{{$ansarAllDetails->mobile_no_request}}</div></td>
        </tr>
        <tr>
            <td>Email (Self) <span class="pull-right">:</span></td>
            <td><div style="padding:5px;font-size:14px;">{{$ansarAllDetails->email_self}}</div></td>
        </tr>
    </table>
    <table class="entry-table border-table">
        <tr>
            <td>তথ্য প্রদানকারীরস্বাক্ষর</td>
            <td>বাম হাতের বৃদ্ধা আঙ্গুলের ছাপ</td>
        </tr>
        <tr>
            <td><img class="img-thumbnail" src="{{URL::route('sign_image',['id'=>$ansarAllDetails->ansar_id])}}"
                     style="height:80px;width:100%;"/></td>
            <td><img class="img-thumbnail" src="{{URL::route('thumb_image',['id'=>$ansarAllDetails->ansar_id])}}"
                     style="height:80px;width:100%;"/></td>
        </tr>
    </table>
</div>
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

</style>