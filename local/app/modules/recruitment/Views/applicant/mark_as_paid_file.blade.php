@extends('template.master')
@section('title','Applicants')
@section('breadcrumb')
    {!! Breadcrumbs::render('recruitment.applicant.index') !!}
@endsection
@section('content')
    <section class="content">
        <div class="box box-solid">
            {{--<div class="overlay" ng-if="allLoading">
                    <span class="fa">
                        <i class="fa fa-refresh fa-spin"></i> <b>Loading...</b>
                    </span>
            </div>--}}
            <div class="box-body">
                <div class="row">
                    <div class="col-sm-6 col-centered">
                        {!! Form::open(['route'=>'recruitment.applicant.update_as_paid_by_file','files'=>true]) !!}
                        <div class="form-group">
                            {!! Form::label('file','Upload File :',['class'=>'control-label']) !!}
                            {!! Form::file('file',null,['class'=>'form-control']) !!}
                            @if(isset($errors)&&$errors->first('file'))
                                <p class="text text-danger">{{$errors->first('file')}}</p>
                            @endif
                        </div>
                        <button type="submit" class="btn btn-primary pull-right">
                            <i class="fa fa-save"></i>&nbsp;Update
                        </button>
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
