@if(isset($data))
    {!! Form::model($data,['route'=>['recruitment.marks.update',$data->id],'method'=>'patch','form-submit','loading'=>'allLoading','on-reset'=>'loadApplicant()']) !!}
@else
    {!! Form::open(['route'=>['recruitment.marks.store'],'form-submit','loading'=>'allLoading','on-reset'=>'loadApplicant()']) !!}
    {!! Form::hidden('applicant_id',$applicant->applicant_id) !!}
@endif
<div class="form-group">
    @if(auth()->user()->type==11)
    {!! Form::label('physical','Physical Fitness Exam',['class'=>'control-label']) !!}<sup style="color:red;font-size: 20px;top: 0;">*</sup>
    <div class="form-control">{{isset($data)?$data->physical:$applicant->physicalPoint()}} out of <strong>{{$mark_distribution?$mark_distribution->physical:'Not Defined'}}</strong></div>
    @endif
    {!! Form::hidden('physical',isset($data)?$data->physical:$applicant->physicalPoint(),['class'=>'form-control','placeholder'=>'Enter physical exam number']) !!}
</div>
<div class="form-group">
    @if(auth()->user()->type==11)
    {!! Form::label('edu_training','Education & Training',['class'=>'control-label']) !!}<sup style="color:red;font-size: 20px;top: 0;">*</sup>
    <div class="form-control">{{isset($data)?$data->edu_training:$applicant->educationTrainingPoint()}} out of <strong>{{$mark_distribution?$mark_distribution->edu_training:'Not Defined'}}</strong></div>
    @endif
    {!! Form::hidden('edu_training',isset($data)?$data->edu_training:$applicant->educationTrainingPoint(),['class'=>'form-control','placeholder'=>'Enter education & training mark']) !!}
</div>

<div class="form-group">
    {!! Form::label('written','Written Exam',['class'=>'control-label']) !!}<sup style="color:red;font-size: 20px;top: 0;">*</sup>
    <div class="input-group">
        {!! Form::text('written',null,['class'=>'form-control','placeholder'=>'Enter written exam number','oninput'=>"validateInput(this,".($mark_distribution?$mark_distribution->written:10000).")"]) !!}
        <span class="input-group-addon">out of <strong>{{$mark_distribution?$mark_distribution->written:'Not Defined'}}</strong></span>
    </div>
</div>
<div class="form-group">
    {!! Form::label('viva','Viva Exam',['class'=>'control-label']) !!}<sup style="color:red;font-size: 20px;top: 0;">*</sup>
    <div class="input-group">
        {!! Form::text('viva',null,['class'=>'form-control','placeholder'=>'Enter viva exam number','oninput'=>"validateInput(this,".($mark_distribution?$mark_distribution->viva:10000).")"]) !!}
        <span class="input-group-addon">out of <strong>{{$mark_distribution?$mark_distribution->viva:'Not Defined'}}</strong></span>
    </div>
</div>
<div class="row">
    <div class="col-sm-12">
        <button class="btn btn-primary pull-right" onclick="$('#mark-form').modal('hide')" type="submit"><i class="fa fa-save"></i>&nbsp;Save</button>
    </div>
</div>
{!! Form::close() !!}
<script>
    function validateInput(elem,maxValue) {
        var v = elem.value;
        elem.value = v>maxValue?maxValue:v;
    }
</script>