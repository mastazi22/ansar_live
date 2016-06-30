{{--User: Shreya--}}
{{--Date: 10/14/2015--}}
{{--Time: 12:00 PM--}}

@extends('template.master')
@section('title','Session Form')
@section('breadcrumb')
    {!! Breadcrumbs::render('session_information_edit',$id,$page) !!}
@endsection
@section('content')

    <div>
        {{--<div class="breadcrumbplace">--}}
            {{--{!! Breadcrumbs::render('entryform') !!}--}}
        {{--</div>--}}
        <!-- Content Header (Page header) -->

        <!-- Main content -->
        <section class="content">
            <div class="row">
                <!-- left column -->
                <div class="col-lg-6 col-centered">
                    <div class="label-title-session-entry">
                        <h4 style="text-align:center; padding:2px"></h4>
                    </div>
                    <!-- general form elements -->

                    <!-- Request addon -->

                    <div class="box box-info">

                        <div class="box-body">

                            {!! Form::open(array('url' => 'session-update', 'class' => 'form-horizontal')) !!}
                            <div class="box-body">
                                <input type="hidden" value="{{$page}}" name="page">
                                <div class="form-group">

                                    {!! Form::label('session_year', 'Session Year:', $attributes = array('class' => 'col-sm-4 control-label')) !!}
                                    <div class="col-sm-8 @if($errors->has('session_year')) has-error @endif">
                                        <div class="input-group">
                                            <span class="input-group-addon glyphicon glyphicon-calendar"></span>
                                            <input type="hidden" name="select-year" value="selectyear">
                                            <select class="form-control" id="session_year" name="session_year">
                                                <option value="selectyear">--Select Year--</option>
                                                <?php
                                                $year=1990;

                                                while ( $year<= 2050) {
                                                ?>
                                                <option value="{{$year}}" <?php if(Request::old('session_year')){ if(Request::old('session_year')==$year) echo 'selected'; } else if($session_info->session_year==$year){ echo 'selected';} ; ?>><?php echo $year; ?></option>

                                                <?php
                                                $year++;
                                                }
                                                ?>
                                            </select>
                                        </div>
                                        @if($errors->has('session_year'))
                                            <p class="text-danger">{{$errors->first('session_year')}}</p>
                                        @endif
                                    </div>
                                </div>
                                <input type="hidden" name="id" class="form-control" id="session_year" value="{{ $session_info->id }}">

                                <div class="form-group">

                                    {!! Form::label('session_start_month', 'Starting Session Month:', $attributes = array('class' => 'col-sm-4 control-label')) !!}
                                    <div class="col-sm-8 @if($errors->has('session_start_month')) has-error @endif">
                                        <div class="input-group">
                                            <span class="input-group-addon glyphicon glyphicon-calendar"></span>
                                            <input type="hidden" name="select-start-month" value="selectstartsession">
                                            <select class="form-control" id="session_start_month" name="session_start_month">
                                                <option value="selectstartsession">--Select Starting Month--</option>
                                                <option value="January" <?php if(Request::old('session_start_month')){ if(Request::old('session_start_month')=="January") echo 'selected'; } else if($session_info->session_start_month=="January"){ echo 'selected';} ; ?>>January</option>
                                                <option value="February" <?php if(Request::old('session_start_month')){ if(Request::old('session_start_month')=="February") echo 'selected'; } else if($session_info->session_start_month=="February"){ echo 'selected';} ; ?>>February</option>
                                                <option value="March" <?php if(Request::old('session_start_month')){ if(Request::old('session_start_month')=="March") echo 'selected'; } else if($session_info->session_start_month=="March"){ echo 'selected';} ; ?>>March</option>
                                                <option value="April" <?php if(Request::old('session_start_month')){ if(Request::old('session_start_month')=="April") echo 'selected'; } else if($session_info->session_start_month=="April"){ echo 'selected';} ; ?>>April</option>
                                                <option value="May" <?php if(Request::old('session_start_month')){ if(Request::old('session_start_month')=="May") echo 'selected'; } else if($session_info->session_start_month=="May"){ echo 'selected';} ; ?>>May</option>
                                                <option value="June" <?php if(Request::old('session_start_month')){ if(Request::old('session_start_month')=="June") echo 'selected'; } else if($session_info->session_start_month=="June"){ echo 'selected';} ; ?>>June</option>
                                                <option value="July" <?php if(Request::old('session_start_month')){ if(Request::old('session_start_month')=="July") echo 'selected'; } else if($session_info->session_start_month=="July"){ echo 'selected';} ; ?>>July</option>
                                                <option value="August" <?php if(Request::old('session_start_month')){ if(Request::old('session_start_month')=="August") echo 'selected'; } else if($session_info->session_start_month=="August"){ echo 'selected';} ; ?>>August</option>
                                                <option value="September" <?php if(Request::old('session_start_month')){ if(Request::old('session_start_month')=="September") echo 'selected'; } else if($session_info->session_start_month=="September"){ echo 'selected';} ; ?>>September</option>
                                                <option value="October" <?php if(Request::old('session_start_month')){ if(Request::old('session_start_month')=="October") echo 'selected'; } else if($session_info->session_start_month=="October"){ echo 'selected';} ; ?>>October</option>
                                                <option value="November" <?php if(Request::old('session_start_month')){ if(Request::old('session_start_month')=="November") echo 'selected'; } else if($session_info->session_start_month=="November"){ echo 'selected';} ; ?>>November</option>
                                                <option value="December" <?php if(Request::old('session_start_month')){ if(Request::old('session_start_month')=="December") echo 'selected'; } else if($session_info->session_start_month=="December"){ echo 'selected';} ; ?>>December</option>
                                            </select>
                                        </div>
                                        @if($errors->has('session_start_month'))
                                            <p class="text-danger">{{$errors->first('session_start_month')}}</p>
                                        @endif
                                    </div>
                                </div>
                                <div class="form-group">
                                    {!! Form::label('session_end_month', 'Ending Session Month:', $attributes = array('class' => 'col-sm-4 control-label')) !!}
                                    <div class="col-sm-8 @if($errors->has('session_end_month')) has-error @endif">
                                        <div class="input-group">
                                            <span class="input-group-addon glyphicon glyphicon-calendar"></span>
                                            <!-- <span class="glyphicons glyphicons-calendar">î?†</span> -->
                                            <input type="hidden" name="select-end-month" value="selectendsession">
                                            <select class="form-control" id="session_end_month" name="session_end_month">
                                                <option value="selectendsession">--Select Ending Month--</option>
                                                <option value="January" <?php if(Request::old('session_end_month')){ if(Request::old('session_end_month')=="January") echo 'selected'; } else if($session_info->session_end_month=="January"){ echo 'selected';} ; ?>>January</option>
                                                <option value="February" <?php if(Request::old('session_end_month')){ if(Request::old('session_end_month')=="February") echo 'selected'; } else if($session_info->session_end_month=="February"){ echo 'selected';} ; ?>>February</option>
                                                <option value="March" <?php if(Request::old('session_end_month')){ if(Request::old('session_end_month')=="March") echo 'selected'; } else if($session_info->session_end_month=="March"){ echo 'selected';} ; ?>>March</option>
                                                <option value="April" <?php if(Request::old('session_end_month')){ if(Request::old('session_end_month')=="April") echo 'selected'; } else if($session_info->session_end_month=="April"){ echo 'selected';} ; ?>>April</option>
                                                <option value="May" <?php if(Request::old('session_end_month')){ if(Request::old('session_end_month')=="May") echo 'selected'; } else if($session_info->session_end_month=="May"){ echo 'selected';} ; ?>>May</option>
                                                <option value="June" <?php if(Request::old('session_end_month')){ if(Request::old('session_end_month')=="June") echo 'selected'; } else if($session_info->session_end_month=="June"){ echo 'selected';} ; ?>>June</option>
                                                <option value="July" <?php if(Request::old('session_end_month')){ if(Request::old('session_end_month')=="July") echo 'selected'; } else if($session_info->session_end_month=="July"){ echo 'selected';} ; ?>>July</option>
                                                <option value="August" <?php if(Request::old('session_end_month')){ if(Request::old('session_end_month')=="August") echo 'selected'; } else if($session_info->session_end_month=="August"){ echo 'selected';} ; ?>>August</option>
                                                <option value="September" <?php if(Request::old('session_end_month')){ if(Request::old('session_end_month')=="September") echo 'selected'; } else if($session_info->session_end_month=="September"){ echo 'selected';} ; ?>>September</option>
                                                <option value="October" <?php if(Request::old('session_end_month')){ if(Request::old('session_end_month')=="October") echo 'selected'; } else if($session_info->session_end_month=="October"){ echo 'selected';} ; ?>>October</option>
                                                <option value="November" <?php if(Request::old('session_end_month')){ if(Request::old('session_end_month')=="November") echo 'selected'; } else if($session_info->session_end_month=="November"){ echo 'selected';} ; ?>>November</option>
                                                <option value="December" <?php if(Request::old('session_end_month')){ if(Request::old('session_end_month')=="December") echo 'selected'; } else if($session_info->session_end_month=="December"){ echo 'selected';} ; ?>>December</option>

                                            </select>
                                        </div>
                                        @if($errors->has('session_end_month'))
                                            <p class="text-danger">{{$errors->first('session_end_month')}}</p>
                                        @endif
                                    </div>
                                </div>

                                <div class="form-group">
                                    {!! Form::label('session_name', 'Session Name:', $attributes = array('class' => 'col-sm-4 control-label')) !!}
                                    <div class="col-sm-8 @if($errors->has('session_name')) has-error @endif">
                                        <div class="input-group">
                                            <span class="input-group-addon glyphicon glyphicon-pencil"></span>
                                            {!! Form::text('session_name', $value = (Request::old('session_name')) ? Request::old('session_name') : $session_info->session_name, $attributes = array('class' => 'form-control', 'id' => 'session_name', 'placeholder' => 'Enter Session Name.e.g., 2005-2009')) !!}
                                        </div>
                                        @if($errors->has('session_name'))
                                            <p class="text-danger">{{$errors->first('session_name')}}</p>
                                        @endif
                                    </div>
                                </div>

                            </div><!-- /.box-body -->



                        </div><!-- /.box-body -->

                    </div><!-- /.box -->
                    <div >
                        <button type="submit" class="btn btn-info pull-right">Update</button>
                    </div><!-- /.box-footer -->
                    {!! Form::close() !!}

                </div><!--/.col (left) -->
                <!-- right column -->

            </div>   <!-- /.row -->
        </section><!-- /.content -->
    </div><!-- /.content-wrapper -->
@endsection