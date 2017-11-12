@if(isset($data))
    {!! Form::model($data,['route'=>['recruitment.marks.update',$data->id],'method'=>'patch','form-submit','loading'=>'allLoading','on-reset'=>'loadApplicant()']) !!}
@else
    {!! Form::open(['route'=>['recruitment.marks.store'],'form-submit','loading'=>'allLoading','on-reset'=>'loadApplicant()']) !!}
    {!! Form::hidden('applicant_id',$applicant_id) !!}
@endif
<div class="form-group">
    {!! Form::label('written','Written Exam',['class'=>'control-label']) !!}
    {!! Form::text('written',null,['class'=>'form-control','placeholder'=>'Enter written exam number']) !!}
</div>
<div class="form-group">
    {!! Form::label('medical','Medical Exam',['class'=>'control-label']) !!}
    {!! Form::text('medical',null,['class'=>'form-control','placeholder'=>'Enter medical exam number']) !!}
</div>
<div class="form-group">
    {!! Form::label('physical','Physical Exam',['class'=>'control-label']) !!}
    {!! Form::text('physical',null,['class'=>'form-control','placeholder'=>'Enter physical exam number']) !!}
</div>
<div class="form-group">
    {!! Form::label('viva','Viva Exam',['class'=>'control-label']) !!}
    {!! Form::text('viva',null,['class'=>'form-control','placeholder'=>'Enter viva exam number']) !!}
</div>
<div class="row">
    <div class="col-sm-12">
        <button class="btn btn-primary pull-right" onclick="$('#mark-form').modal('hide')" type="submit"><i class="fa fa-save"></i>&nbsp;Save</button>
    </div>
</div>
{!! Form::close() !!}