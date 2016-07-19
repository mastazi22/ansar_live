@extends('template.master')
@section('title','Change user password')
@section('breadcrumb')
    {{--{!! Breadcrumbs::render('edit_user',$id) !!}--}}
@endsection
@section('content')
    <script>
    </script>
    <section class="content">
        <div class="box box-solid">
            <div class="box-body">
                <div class="row" >
                    <div class="col-sm-4 col-sm-offset-4" style="margin-bottom: 20px">
                        @if(Session::has('success'))
                            <div class="alert alert-success" style="padding: 5px 10px">
                                <i class="fa fa-check"></i>&nbsp;&nbsp;{{Session::get('success')}}
                            </div>
                            @endif
                            @if(Session::has('error'))
                                <div class="alert alert-danger" style="padding: 5px 10px">
                                    <i class="fa fa-warning"></i>&nbsp;&nbsp;{{Session::get('error')}}
                                </div>
                            @endif
                        <h4>Change user password for : {{$user}}</h4>
                        <form id="user-password-form" action="{{URL::route('handle_change_password')}}" method="post">
                            {!! csrf_field() !!}
                            <input type="hidden" name="user" value="{{$user}}">
                            <div class="form-group has-feedback">
                                <input type="password" name="password" value="" class="form-control" placeholder="Enter password"/>
                                <span class="glyphicon glyphicon-lock form-control-feedback"></span>
                                @if($errors->has('password'))
                                <p class="text text-danger"><i class="fa fa-warning"></i>&nbsp;{{$errors->first('password')}}</p>
                                    @endif
                            </div>
                            <div class="form-group has-feedback">
                                <input type="password" name="c_password" value="" class="form-control" placeholder="Type password again"/>
                                <span class="glyphicon glyphicon-lock form-control-feedback"></span>
                                @if($errors->has('c_password'))
                                    <p class="text text-danger"><i class="fa fa-warning"></i>&nbsp;{{$errors->first('c_password')}}</p>
                                @endif
                            </div>
                            <div>
                                <button type="submit" class="btn btn-primary">
                                    Change password
                                </button>
                                <div class="clearfix"></div>
                            </div>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </section>
@stop