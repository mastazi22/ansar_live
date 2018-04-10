@extends('template.master')
@section('title','Create Sub Training')
{{--@section('small_title','DG')--}}
@section('breadcrumb')
    {!! Breadcrumbs::render('range.create') !!}
@endsection
@section('content')
    <section class="content">
        <div class="row">
            <div class="col-sm-6 col-centered">
                <div class="box box-solid">
                    <div class="box-body">
                        {!! Form::open(['route'=>'HRM.sub_training.store']) !!}
                        <div class="form-group">
                            {!! Form::label('main_training_info_id','Main Training : ') !!}
                            {!! Form::select('main_training_info_id',$main_training,null,['class'=>'form-control']) !!}
                            {!! $errors->first('main_training_info_id','<p class="text text-danger">:message</p>') !!}
                        </div>
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