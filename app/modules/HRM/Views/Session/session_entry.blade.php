{{--User: Shreya--}}
{{--Date: 10/14/2015--}}
{{--Time: 12:00 PM--}}

@extends('template/master')
@section('content')

    <div>

        <!-- Content Header (Page header) -->

        <!-- Main content -->
        <section class="content">
            <div class="row">
                <!-- left column -->
                <div class="col-lg-8 col-centered">
                    <div class="label-title-session-entry">
                        <h4 style="text-align:center; padding:2px">Session Form</h4>
                    </div>
                    <!-- general form elements -->

                    <!-- Request addon -->

                    <div class="box box-info">

                        <div class="box-body">

                            {!! Form::open(array('url' => 'HRM/save-session-entry', 'class' => 'form-horizontal')) !!}
                            <div class="box-body">

                                <div class="form-group required">

                                    {!! Form::label('session_year', 'Session Year:', $attributes = array('class' => 'col-sm-4 control-label')) !!}
                                    <div class="col-sm-8 @if($errors->has('session_year')) has-error @endif">
                                        <div class="input-group">
                                            <span class="input-group-addon glyphicon glyphicon-calendar"></span>
                                            <select class="form-control" id="session_year" name="session_year">
                                                <option value="">--Select Year--</option>
                                                <?php
                                                $year = 1990;

                                                while ( $year <= 2050) {
                                                ?>
                                                <option value="{{$year}}" @if(Request::old('session_year') == $year) selected @endif><?php echo $year; ?></option>
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

                                <div class="form-group required">

                                    {!! Form::label('session_start_month', 'Starting Session Month:', $attributes = array('class' => 'col-sm-4 control-label')) !!}
                                    <div class="col-sm-8 @if($errors->has('session_start_month')) has-error @endif">
                                        <div class="input-group">
                                            <span class="input-group-addon glyphicon glyphicon-calendar"></span>
                                            <select class="form-control" id="session_start_month"
                                                    name="session_start_month">
                                                <option value="">--Select Starting Month--</option>
                                                <option value="January" @if(Request::old('session_start_month') == "January") selected @endif>January</option>
                                                <option value="February" @if(Request::old('session_start_month') == "February") selected @endif>February</option>
                                                <option value="March" @if(Request::old('session_start_month') == "March") selected @endif>March</option>
                                                <option value="April" @if(Request::old('session_start_month') == "April") selected @endif>April</option>
                                                <option value="May" @if(Request::old('session_start_month') == "May") selected @endif>May</option>
                                                <option value="June" @if(Request::old('session_start_month') == "June") selected @endif>June</option>
                                                <option value="July" @if(Request::old('session_start_month') == "July") selected @endif>July</option>
                                                <option value="August" @if(Request::old('session_start_month') == "August") selected @endif>August</option>
                                                <option value="September" @if(Request::old('session_start_month') == "September") selected @endif>September</option>
                                                <option value="October" @if(Request::old('session_start_month') == "October") selected @endif>October</option>
                                                <option value="November" @if(Request::old('session_start_month') == "November") selected @endif>November</option>
                                                <option value="December" @if(Request::old('session_start_month') == "December") selected @endif>December</option>
                                            </select>
                                        </div>
                                        @if($errors->has('session_start_month'))
                                            <p class="text-danger">{{$errors->first('session_start_month')}}</p>
                                        @endif
                                    </div>
                                </div>
                                <div class="form-group required">
                                    {!! Form::label('session_end_month', 'Ending Session Month:', $attributes = array('class' => 'col-sm-4 control-label')) !!}
                                    <div class="col-sm-8 @if($errors->has('session_end_month')) has-error @endif">
                                        <div class="input-group">
                                            <span class="input-group-addon glyphicon glyphicon-calendar"></span>
                                            <!-- <span class="glyphicons glyphicons-calendar">�?�</span> -->
                                            <select class="form-control" id="session_end_month"
                                                    name="session_end_month">
                                                <option value="">--Select Ending Month--</option>
                                                <option value="January" @if(Request::old('session_end_month') == "January") selected @endif>January</option>
                                                <option value="February" @if(Request::old('session_end_month') == "February") selected @endif>February</option>
                                                <option value="March" @if(Request::old('session_end_month') == "March") selected @endif>March</option>
                                                <option value="April" @if(Request::old('session_end_month') == "April") selected @endif>April</option>
                                                <option value="May" @if(Request::old('session_end_month') == "May") selected @endif>May</option>
                                                <option value="June" @if(Request::old('session_end_month') == "June") selected @endif>June</option>
                                                <option value="July" @if(Request::old('session_end_month') == "July") selected @endif>July</option>
                                                <option value="August" @if(Request::old('session_end_month') == "August") selected @endif>August</option>
                                                <option value="September" @if(Request::old('session_end_month') == "September") selected @endif>September</option>
                                                <option value="October" @if(Request::old('session_end_month') == "October") selected @endif>October</option>
                                                <option value="November" @if(Request::old('session_end_month') == "November") selected @endif>November</option>
                                                <option value="December" @if(Request::old('session_end_month') == "December") selected @endif>December</option>
                                            </select>
                                        </div>
                                        @if($errors->has('session_end_month'))
                                            <p class="text-danger">{{$errors->first('session_end_month')}}</p>
                                        @endif
                                    </div>
                                </div>
                                <div class="form-group required">
                                    {!! Form::label('session_name', 'Session Name:', $attributes = array('class' => 'col-sm-4 control-label')) !!}
                                    <div class="col-sm-8 @if($errors->has('session_name')) has-error @endif">
                                        <div class="input-group">
                                            <span class="input-group-addon glyphicon glyphicon-pencil"></span>
                                            {!! Form::text('session_name', $value = Request::old('session_name'), $attributes = array('class' => 'form-control', 'id' => 'session_name', 'placeholder' => 'Enter Session Name.e.g., 2005-2009')) !!}
                                        </div>
                                        @if($errors->has('session_name'))
                                            <p class="text-danger">{{$errors->first('session_name')}}</p>
                                        @endif
                                    </div>
                                </div>

                            </div>
                            <!-- /.box-body -->


                        </div>
                        <!-- /.box-body -->

                    </div>
                    <!-- /.box -->
                    <div>
                        <button type="submit" class="btn btn-info pull-right">Submit</button>
                    </div>
                    <!-- /.box-footer -->
                    {!! Form::close() !!}

                </div>
                <!--/.col (left) -->
                <!-- right column -->

            </div>
            <!-- /.row -->
        </section>
        <!-- /.content -->
    </div><!-- /.content-wrapper -->
@endsection