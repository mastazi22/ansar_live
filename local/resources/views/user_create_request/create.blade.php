@extends('template.master')
@section('title','Create New Request')
@section('breadcrumb')
    {!! Breadcrumbs::render('entry.list.entry') !!}
@endsection
@section('content')
    <style>
        .required{
            position: relative;
        }
        .required:after{
            content: '∗';
            position: absolute;
            right: -7px;
            top: -6px;
            color: red;
            font-size: 14px;
        }
    </style>
    <section class="content">
        <div class="box box-solid">
            <div class="box-body">
                <div class="row">
                    <div class="col-sm-8 col-sm-offset-2">
                        {!! Form::open(['route'=>'user_create_request.store','method'=>'post']) !!}
                            <div class="form-group">
                                {!! Form::label('first_name','First Name',['class'=>'control-label required']) !!}
                                {!! Form::text('first_name',null,['class'=>'form-control','placeholder'=>'First name']) !!}
                                @if(isset($errors)&&$errors->first('first_name'))
                                    <p class="text text-danger">{{$errors->first('first_name')}}</p>
                                @endif
                            </div>
                            <div class="form-group">
                                {!! Form::label('last_name','Last Name',['class'=>'control-label required']) !!}
                                {!! Form::text('last_name',null,['class'=>'form-control','placeholder'=>'Last name']) !!}
                                @if(isset($errors)&&$errors->first('last_name'))
                                    <p class="text text-danger">{{$errors->first('last_name')}}</p>
                                @endif
                            </div>
                            <div class="form-group">
                                {!! Form::label('email','Email',['class'=>'control-label required']) !!}
                                {!! Form::text('email',null,['class'=>'form-control','placeholder'=>'Email']) !!}
                                @if(isset($errors)&&$errors->first('email'))
                                    <p class="text text-danger">{{$errors->first('email')}}</p>
                                @endif
                            </div>
                            <div class="form-group">
                                {!! Form::label('mobile_no','Mobile No',['class'=>'control-label required']) !!}
                                <small class="text text-danger">(Please enter valid mobile no. you will receive your user name and password in this number)</small>
                                {!! Form::text('mobile_no',null,['class'=>'form-control','placeholder'=>'Mobile no']) !!}
                                @if(isset($errors)&&$errors->first('mobile_no'))
                                    <p class="text text-danger">{{$errors->first('mobile_no')}}</p>
                                @endif
                            </div>
                            <div class="form-group">
                                {!! Form::label('user_type','User Type',['class'=>'control-label required']) !!}
                                {!! Form::select('user_type',[''=>'--Select a user type--','dataentry'=>'Date Entry','verifier'=>'Verifier','accountant'=>'Accountant','office_assistance'=>'Office Assistance'],null,['class'=>'form-control']) !!}
                                @if(isset($errors)&&$errors->first('user_type'))
                                    <p class="text text-danger">{{$errors->first('user_type')}}</p>
                                @endif
                            </div>
                            <button class="btn btn-primary pull-right">Submit</button>
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection