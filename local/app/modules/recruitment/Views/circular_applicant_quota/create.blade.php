@extends('template.master')
@section('title','Create Applicant Quota Type')
@section('breadcrumb')
    {!! Breadcrumbs::render('recruitment.applicant.list') !!}
@endsection
@section('content')
    <section class="content">
        <div class="box box-solid">
            <div class="box body">
                <div class="container" style="padding-bottom: 20px">
                    <div class="row">
                        <div class="col-sm-6">
                            {!! Form::open(['route'=>'recruitment.quota.store']) !!}
                            {!! Form::label('quota_name_eng','Quota Type Name Eng',['class'=>'control-lable']) !!}
                            {!! Form::text('quota_name_eng',null,['class'=>'form-control','placeholder'=>'Quota Type Name Eng']) !!}
                            @if(isset($errors)&&$errors->first('quota_name_eng'))
                                <p class="text-danger">
                                    {{$errors->first('quota_name_eng')}}
                                </p>
                            @endif
                            {!! Form::label('quota_name_bng','Quota Type Name Bng',['class'=>'control-lable']) !!}
                            {!! Form::text('quota_name_bng',null,['class'=>'form-control','placeholder'=>'Quota Type Name Bng']) !!}
                            @if(isset($errors)&&$errors->first('quota_name_bng'))
                                <p class="text-danger">
                                    {{$errors->first('quota_name_bng')}}
                                </p>
                            @endif
                            <div style="padding-top: 20px">
                                <button class="btn btn-success btn-sm" type="submit">
                                    <i class="fa fa-save"></i>&nbsp;Create New Quota TYpe
                                </button>
                            </div>
                            {!! Form::close() !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection