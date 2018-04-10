@extends('template.master')
@section('title','Edit Training')
{{--@section('small_title','DG')--}}
@section('breadcrumb')
    {!! Breadcrumbs::render('range.edit',$data->id) !!}
@endsection
@section('content')
    <section class="content">
        <div class="row">
            <div class="col-sm-6 col-centered">
                <div class="box box-solid">
                    <div class="box-body">
                        {!! Form::model($data,['route'=>['HRM.main_training.update',$data->id],'method'=>'patch']) !!}
                        <div class="form-group">
                            {!! Form::label('training_name_eng','Training name eng : ') !!}
                            {!! Form::text('training_name_eng',null,['class'=>'form-control','placeholder'=>'Enter Training Name']) !!}
                            {!! $errors->first('training_name_eng','<p class="text text-danger">:message</p>') !!}
                        </div>
                        <div class="form-group">
                            {!! Form::label('training_name_bng','Training name bng : ') !!}
                            {!! Form::text('training_name_bng',null,['class'=>'form-control','placeholder'=>'প্রশিক্ষণের নাম লিখুন']) !!}
                            {!! $errors->first('training_name_bng','<p class="text text-danger">:message</p>') !!}
                        </div>
                        <div class="form-group">
                            {!! Form::submit('Submit',['class'=>'btn btn-info pull-right']) !!}
                        </div>
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection