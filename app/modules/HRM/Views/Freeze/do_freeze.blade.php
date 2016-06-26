@extends('template.master')

@section('content')

<script>
        $(document).ready(function () {
            $('#freeze_date').datePicker(true);
            //$('.picker').datePicker();
        })
        </script>
        
<div class="content-wrapper">
    <section class="content">
        <div class="row" style="height: 100%">
            <div style="width:50%;margin:0 auto;">
                <h2 style="text-align: center;">Freeze for disciplinary action</h2>
                <div class="box">
                    <div class="box-body">
                        @if(Session::has('success_message'))
                        <div style="padding: 10px 20px 0 20px;">
                            <div class="alert alert-success">
                                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                                <span class="glyphicon glyphicon-ok"></span> {{Session::get('success_message')}}
                            </div>
                        </div>
                        @endif
                        @if(Session::has('error_message'))
                        <div style="padding: 10px 20px 0 20px;">
                            <div class="alert alert-warning">
                                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                                <span class="glyphicon glyphicon-alert"></span> {{Session::get('error_message')}}
                            </div>
                        </div>
                        @endif
                        <form class="form-horizontal" role="form" action="{{URL::to('freezesubmit')}}" method="post">
                            {!! csrf_field() !!}
                            <div class="form-group @if($errors->has('ansar_id')) has-error @endif">
                                @if($errors->has('ansar_id'))<span style="color:red">{{$errors->first('ansar_id')}}</span>@endif
                                @if(session('status')) <span style="color:red">{{ session('status') }}</span>@endif
                                <label class="control-label col-sm-3" for="id">ID:</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="ansar_id" placeholder="Enter id" name="ansar_id" value="{{Input::old('ansar_id')}}">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-sm-3" for="freeze_date">Freeze date:</label>
                                <div class="col-sm-9">          
                                    <input  class="form-control" id="freeze_date" placeholder="Enter the date" name="freeze_date" value="{{Input::old('freeze_date')}}">
                                </div>
                            </div>
                            <div class="form-group @if($errors->has('memorandum_id')) has-error @endif" >
                                @if($errors->has('memorandum_id'))<span style="color:red">{{$errors->first('memorandum_id')}}</span>@endif
                                <label class="control-label col-sm-3" for="freeze_date">স্বারক নংঃ</label>
                                <div class="col-sm-9">          
                                    <input type="text" class="form-control" id="memorandum_id" placeholder="স্বারক নং" name="memorandum_id" value="{{Input::old('memorandum_id')}}">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-sm-3" for="reason">Comment:</label>
                                <div class="col-sm-9">          
                                    <input type="text" class="form-control" id="reason" placeholder="Enter the comment" name="reason" value="{{Input::old('memorandum_id')}}">
                                </div>
                            </div>
                            <div class="form-group">        
                                <div class="col-sm-offset-3 col-sm-10">
                                    <button type="submit" class="btn btn-default">Submit</button>
                                </div>
                            </div>
                        </form>


                    </div>

                </div>
            </div>
        </div>
    </section>
</div>

@stop