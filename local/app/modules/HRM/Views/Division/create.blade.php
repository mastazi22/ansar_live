@extends('template.master')
@section('title','Create Range')
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
                        {!! Form::model(Request::old(),['route'=>'HRM.range.store']) !!}
                        <div class="form-group">
                            {!! Form::label('division_name_eng','Range name eng : ') !!}
                            {!! Form::text('division_name_eng',null,['class'=>'form-control','placeholder'=>'Enter Range Name']) !!}
                            {!! $errors->first('division_name_eng','<p class="text text-danger">:message</p>') !!}
                        </div>
                        <div class="form-group">
                            {!! Form::label('division_name_bng','Range name bng : ') !!}
                            {!! Form::text('division_name_bng',null,['class'=>'form-control','placeholder'=>'রেঞ্জের নাম লিখুন']) !!}
                            {!! $errors->first('division_name_bng','<p class="text text-danger">:message</p>') !!}
                        </div>
                        <div class="form-group">
                            {!! Form::label('division_code','Range code : ') !!}
                            {!! Form::text('division_code',null,['class'=>'form-control','placeholder'=>'Enter Range code']) !!}
                            {!! $errors->first('division_code','<p class="text text-danger">:message</p>') !!}
                        </div>
                        <div class="form-group">
                            {!! Form::submit('Create Range',['class'=>'btn btn-info pull-right']) !!}
                        </div>
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection