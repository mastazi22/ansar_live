@if(isset($data))
    {!! Form::model($data,['route'=>['recruitment.marks.update',$data->id],'method'=>'patch','form-submit','loading'=>'allLoading','on-reset'=>'loadApplicant()']) !!}
@else
    {!! Form::open(['route'=>['recruitment.marks.store'],'form-submit','loading'=>'allLoading','on-reset'=>'loadApplicant()']) !!}
    {!! Form::hidden('applicant_id',$applicant->applicant_id) !!}
@endif

@if(isset($mark_distribution) && $mark_distribution->physical!=null)
    <div class="form-group">
        @if(auth()->user()->type==11)
            {!! Form::label('physical','Physical Fitness Exam',['class'=>'control-label']) !!}<sup
                    style="color:red;font-size: 20px;top: 0;">*</sup>
            <div class="form-control">{{isset($data)?$data->physical:$applicant->physicalPoint()}} out of
                <strong>{{$mark_distribution?$mark_distribution->physical:'Not Defined'}}</strong></div>
        @endif
        {!! Form::hidden('physical',isset($data)?$data->physical:$applicant->physicalPoint(),['class'=>'form-control','placeholder'=>'Enter physical exam number']) !!}
    </div>
@endif

@if(isset($mark_distribution) && $mark_distribution->edu_training!=null)
    <div class="form-group">
        @if(auth()->user()->type==11)
            {!! Form::label('edu_training','Education & Training',['class'=>'control-label']) !!}<sup
                    style="color:red;font-size: 20px;top: 0;">*</sup>
            <div class="form-control">{{isset($data)?$data->edu_training:$applicant->educationTrainingPoint()}} out of
                <strong>{{$mark_distribution?$mark_distribution->edu_training:'Not Defined'}}</strong></div>
        @endif
        {!! Form::hidden('edu_training',isset($data)?$data->edu_training:$applicant->educationTrainingPoint(),['class'=>'form-control','placeholder'=>'Enter education & training mark']) !!}
    </div>
@endif

@if(isset($mark_distribution) && $mark_distribution->edu_experience!=null)
    <div class="form-group">
        @if(auth()->user()->type==11)
            {!! Form::label('edu_experience','Education & Experience',['class'=>'control-label']) !!}<sup
                    style="color:red;font-size: 20px;top: 0;">*</sup>
            <div class="form-control">{{isset($data)?$data->edu_experience:$applicant->educationExperiencePoint()}} out
                of
                <strong>{{$mark_distribution?$mark_distribution->edu_experience:'Not Defined'}}</strong></div>
        @endif
        {!! Form::hidden('edu_experience',isset($data)?$data->edu_experience:$applicant->educationExperiencePoint(),['class'=>'form-control','placeholder'=>'Enter education & experience mark']) !!}
    </div>
@endif
@if(isset($mark_distribution) && $mark_distribution->physical_age!=null)
    <div class="form-group">
        @if(auth()->user()->type==11)
            {!! Form::label('physical_age','Physical & Age',['class'=>'control-label']) !!}<sup
                    style="color:red;font-size: 20px;top: 0;">*</sup>
            <div class="form-control">{{isset($data)?$data->physical_age:$applicant->physicalAgePoint()}} out
                of
                <strong>{{$mark_distribution?$mark_distribution->physical_age:'Not Defined'}}</strong></div>
        @endif
        {!! Form::hidden('physical_age',isset($data)?$data->physical_age:$applicant->physicalAgePoint(),['class'=>'form-control','placeholder'=>'Enter education & experience mark']) !!}
    </div>
@endif

@if(isset($mark_distribution) && $mark_distribution->written!=null)
    <div class="form-group">
        {!! Form::label('written','Written Exam',['class'=>'control-label']) !!}<sup
                style="color:red;font-size: 20px;top: 0;">*</sup>
        <div class="input-group">
            {!! Form::text('written',null,['class'=>'form-control','placeholder'=>'Enter written exam number','oninput'=>"validateInput(this,".($mark_distribution?$mark_distribution->written:10000).")"]) !!}
            <span class="input-group-addon">out of <strong>{{$mark_distribution?$mark_distribution->written:'Not Defined'}}</strong></span>
        </div>
    </div>
@endif

@if(isset($mark_distribution) && $mark_distribution->viva!=null)
    <div class="form-group">
        {!! Form::label('viva','Viva Exam',['class'=>'control-label']) !!}<sup
                style="color:red;font-size: 20px;top: 0;">*</sup>
        <div class="input-group">
            {!! Form::text('viva',null,['class'=>'form-control','placeholder'=>'Enter viva exam number','oninput'=>"validateInput(this,".($mark_distribution?$mark_distribution->viva:10000).")"]) !!}
            <span class="input-group-addon">out of <strong>{{$mark_distribution?$mark_distribution->viva:'Not Defined'}}</strong></span>
        </div>
    </div>
@endif
@if(isset($mark_distribution) && is_array($mark_distribution->additional_marks))
    @php($i=0)
    @foreach($mark_distribution->additional_marks as $key=>$am)
    <div class="form-group">
        {!! Form::label(implode('_',explode(' ',$am['label'])),$am['label'],['class'=>'control-label']) !!}<sup
                style="color:red;font-size: 20px;top: 0;">*</sup>
        <div class="form-group">
            <?php
            $name = implode('_',explode(' ',$am['label']));
            if(isset($data)){
                $selectedValue = $data->additional_marks[$i][$name];
            }else{
                $selectedValue = 0;
            }

            ?>
            {{--{!! Form::select("additional_marks[$i][".implode('_',explode(' ',$am['label']))."]",null,['class'=>'form-control','placeholder'=>'Enter '.$am['label'].' number','oninput'=>"validateInput(this,".(implode('_',explode(' ',$am['label'])).")"]) !!}--}}
            {!! Form::select("additional_marks[$i][".$name."]",['0'=>0,'5'=>5],$selectedValue,['class'=>'form-control','placeholder'=>'Enter '.$am['label'].' number']) !!}
            {{--{!! Form::text("additional_marks[$i][".implode('_',explode(' ',$am['label']))."]",null,['class'=>'form-control','placeholder'=>'Enter '.$am['label'].' number','oninput'=>"validateInput(this,".($am['value']).")"]) !!}--}}

            <small>pass=5,fail=0</small>
            {{--<span class="input-group-addon">out of <strong>{{$am['value']}}</strong></span>--}}
        </div>
    </div>
    @php($i++)
    @endforeach
@endif
<div class="row">
    <div class="col-sm-12">
        <button class="btn btn-primary pull-right" onclick="$('#mark-form').modal('hide')" type="submit"><i
                    class="fa fa-save"></i>&nbsp;Save
        </button>
    </div>
</div>
{!! Form::close() !!}
<script>
    function validateInput(elem, maxValue) {
        var v = elem.value;
        elem.value = v > maxValue ? maxValue : v;
    }
</script>