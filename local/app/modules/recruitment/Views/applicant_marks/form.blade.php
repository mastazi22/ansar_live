@if(isset($data))
    {!! Form::model($data,['route'=>['recruitment.marks.update',$data->id],'method'=>'patch','form-submit','loading'=>'allLoading','on-reset'=>'loadApplicant()']) !!}
@else
    {!! Form::open(['route'=>['recruitment.marks.store'],'form-submit','loading'=>'allLoading','on-reset'=>'loadApplicant()']) !!}
    {!! Form::hidden('applicant_id',$applicant->applicant_id) !!}
@endif
<div class="form-group">
    {!! Form::label('physical','Physical Fitness Exam',['class'=>'control-label']) !!}<sup style="color:red;font-size: 20px;top: 0;">*</sup>
    {!! Form::text('physical',isset($data)?$data->physical:$applicant->physicalPoint(),['class'=>'form-control','placeholder'=>'Enter physical exam number']) !!}
</div>
<div class="form-group">
    {!! Form::label('edu_training','Education & Training',['class'=>'control-label']) !!}<sup style="color:red;font-size: 20px;top: 0;">*</sup>
    {!! Form::text('edu_training',isset($data)?$data->edu_training:$applicant->educationTrainingPoint(),['class'=>'form-control','placeholder'=>'Enter education & training mark']) !!}
</div>

<div class="form-group">
    {!! Form::label('written','Written Exam',['class'=>'control-label']) !!}<sup style="color:red;font-size: 20px;top: 0;">*</sup>
    {!! Form::text('written',null,['class'=>'form-control','placeholder'=>'Enter written exam number']) !!}
</div>
<div class="form-group">
    {!! Form::label('viva','Viva Exam',['class'=>'control-label']) !!}<sup style="color:red;font-size: 20px;top: 0;">*</sup>
    {!! Form::text('viva',null,['class'=>'form-control','placeholder'=>'Enter viva exam number']) !!}
</div>
<div class="row">
    <div class="col-sm-12">
        <button class="btn btn-primary pull-right" onclick="$('#mark-form').modal('hide')" type="submit"><i class="fa fa-save"></i>&nbsp;Save</button>
    </div>
</div>
{!! Form::close() !!}