@extends('template.master')
@section('title','Application Instruction')
@section('breadcrumb')
    {!! Breadcrumbs::render('recruitment') !!}
@endsection
@section('content')
    <section class="content" ng-controller="applicantSearch">
        @if(Session::has('success'))
            <div class="alert alert-success">
                {!! Session::get('success') !!}
            </div>
        @elseif(Session::has('error'))
            <div class="alert alert-danger">
                {!! Session::get('error') !!}
            </div>
        @endif
        <div class="box box-solid">

            <div class="box-body">
                <div class="row">
                    <div class="col-sm-8 col-centered">
                        {!! Form::model($data,['route'=>'recruitment.instruction']) !!}
                        {!! Form::label('instruction','Application instruction') !!}
                        {!! Form::textarea('instruction',null,['id'=>'instruction','class'=>'form-control']) !!}
                        <button class="btn btn-primary" type="submit" style="margin-top: 20px">Save Instrcution</button>
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
    </section>
    <script src="//cdn.ckeditor.com/4.8.0/standard/ckeditor.js"></script>
    <script>
        CKEDITOR.replace('instruction');
    </script>
@endsection