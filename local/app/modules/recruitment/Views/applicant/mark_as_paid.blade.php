@extends('template.master')
@section('title','Applicants')
@section('breadcrumb')
    {!! Breadcrumbs::render('recruitment.applicant.index') !!}
@endsection
@section('content')
    <section class="content">
        @if(Session::has('flash_error'))
            <div style="padding: 10px 20px 0 20px;">
                <div class="alert alert-danger">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                    <span class="fa fa-remove"></span> {{Session::get('flash_error')}}
                </div>
            </div>
        @endif
        <div class="box box-solid">
            {{--<div class="overlay" ng-if="allLoading">
                    <span class="fa">
                        <i class="fa fa-refresh fa-spin"></i> <b>Loading...</b>
                    </span>
            </div>--}}
            <div class="box-body">
                <div class="row">
                    <div class="col-sm-6 col-centered">
                        {!! Form::open(['route'=>['recruitment.applicant.update_as_paid',$id]]) !!}
                        {!! Form::hidden('type',$type) !!}
                        <div class="form-group">
                            {!! Form::label('bankTxID','bankTxID :',['class'=>'control-label']) !!}
                            {!! Form::text('bankTxID',null,['class'=>'form-control']) !!}
                            @if(isset($errors)&&$errors->first('bankTxID'))
                                <p class="text text-danger">{{$errors->first('bankTxID')}}</p>
                            @endif
                        </div>
                        <div class="form-group">
                            {!! Form::label('paymentOption','paymentOption :',['class'=>'control-label']) !!}
                            {!! Form::text('paymentOption',null,['class'=>'form-control']) !!}
                            @if(isset($errors)&&$errors->first('paymentOption'))
                                <p class="text text-danger">{{$errors->first('paymentOption')}}</p>
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
