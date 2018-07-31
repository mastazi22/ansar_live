<div ng-controller="jobCircularConstraintController">

    @if(isset($data))
        {!! Form::model($data,['route'=>['recruitment.training.session.update',$data],'method'=>'patch']) !!}
    @else
        {!! Form::open(['route'=>'recruitment.training.session.store']) !!}
    @endif
    <div class="form-group">
        {!! Form::label('training_course_id','Select Training Course :',['class'=>'control-label']) !!}
        {!! Form::select('training_course_id',$training_course,null,['class'=>'form-control']) !!}
        @if(isset($errors)&&$errors->first('training_course_id'))
            <p class="text text-danger">{{$errors->first('training_course_id')}}</p>
        @endif
    </div>
    <div class="form-group">
        {!! Form::label('session_name','Session Title :',['class'=>'control-label']) !!}
        {!! Form::text('session_name',null,['class'=>'form-control','placeholder'=>'Enter session name']) !!}
        @if(isset($errors)&&$errors->first('session_name'))
            <p class="text text-danger">{{$errors->first('session_name')}}</p>
        @endif
    </div>
    <div class="form-group">
        {!! Form::label('start_date','Start Date :',['class'=>'control-label']) !!}
        {!! Form::text('start_date',null,['class'=>'form-control','placeholder'=>'Enter Start Date','date-picker'=>(isset($data)?"moment('{$data->start_date}').format('DD-MMM-YYYY')":"moment('".\Carbon\Carbon::parse(Request::old('start_date'))->format('Y-m-d')."').format('DD-MMM-YYYY')")]) !!}
        @if(isset($errors)&&$errors->first('start_date'))
            <p class="text text-danger">{{$errors->first('start_date')}}</p>
        @endif
    </div>
    <div class="form-group">
        {!! Form::label('end_date','End Date :',['class'=>'control-label']) !!}
        {!! Form::text('end_date',null,['class'=>'form-control','placeholder'=>'Enter Start Date','date-picker'=>(isset($data)?"moment('{$data->end_date}').format('DD-MMM-YYYY')":"moment('".\Carbon\Carbon::parse(Request::old('end_date'))->format('Y-m-d')."').format('DD-MMM-YYYY')")]) !!}
        @if(isset($errors)&&$errors->first('end_date'))
            <p class="text text-danger">{{$errors->first('end_date')}}</p>
        @endif
    </div>
    <div class="form-group">
        {!! Form::label('pay_amount','Pay Amount :',['class'=>'control-label']) !!}
        {!! Form::text('pay_amount',null,['class'=>'form-control','placeholder'=>'Pay Amount']) !!}
        @if(isset($errors)&&$errors->first('pay_amount'))
            <p class="text text-danger">{{$errors->first('pay_amount')}}</p>
        @endif
    </div>
   {{-- <div class="form-group">
        {!! Form::label('terms_and_conditions','Terms and Conditions :',['class'=>'control-label']) !!}
        {!! Form::textarea('terms_and_conditions',null,['class'=>'form-control','placeholder'=>'','id'=>'terms_and_conditions']) !!}
    </div>--}}
    <div class="form-group">
        {!! Form::label('test','Session Status : ',['class'=>'control-label','style'=>'margin-right:15px']) !!}
        <input type="checkbox" value="on" name="session_status"
               @if((isset($data)&&$data->session_status=='on')||Request::old('session_status')=='on')checked
               @endif id="session_status" class="switch-checkbox">
        <label for="session_status" class=""></label>
    </div>
    <div class="form-group">
        {!! Form::label('test','Login Status : ',['class'=>'control-label','style'=>'margin-right:15px']) !!}
        <input type="checkbox" value="on" name="login_status"
               @if((isset($data)&&$data->login_status=='on')||Request::old('login_status')=='on')checked
               @endif id="login_status" class="switch-checkbox">
        <label for="login_status" class=""></label>
    </div>
    <div class="form-group">
        {!! Form::label('test','Payment Status : ',['class'=>'control-label','style'=>'margin-right:15px']) !!}
        <input type="checkbox" value="on" name="payment_status"
               @if((isset($data)&&$data->payment_status=='on')||Request::old('payment_status')=='on')checked
               @endif id="payment_status" class="switch-checkbox">
        <label for="payment_status" class=""></label>
    </div>
    <div class="form-group">
        {!! Form::label('test','Application Status : ',['class'=>'control-label','style'=>'margin-right:15px']) !!}
        <input type="checkbox" value="on" name="application_status"
               @if((isset($data)&&$data->application_status=='on')||Request::old('application_status')=='on')checked
               @endif id="application_status" class="switch-checkbox">
        <label for="application_status" class=""></label>
    </div>
    <div class="form-group">
        {!! Form::label('test','Auto De-Activate Circular After End Date : ',['class'=>'control-label','style'=>'margin-right:15px']) !!}
        <input type="checkbox" value="1" name="auto_terminate"
               @if((isset($data)&&$data->auto_terminate=='1')||Request::old('auto_terminate')=='1')checked
               @endif id="auto_terminate" class="switch-checkbox">
        <label for="auto_terminate" class=""></label>
    </div>
    {{--<div class="form-group">
        {!! Form::label('admit_card_message','Message for admit card :',['class'=>'control-label']) !!}
        {!! Form::textarea('admit_card_message',null,['class'=>'form-control','placeholder'=>'','id'=>'admit_card_message']) !!}
    </div>--}}
    @if(isset($data))
        <button type="submit" class="btn btn-primary pull-right">
            <i class="fa fa-save"></i>&nbsp;Update Training session
        </button>
    @else
        <button type="submit" class="btn btn-primary pull-right">
            <i class="fa fa-save"></i>&nbsp;Save Training session
        </button>
    @endif
    {!! Form::close() !!}
</div>
<script src="//cdn.ckeditor.com/4.8.0/standard/ckeditor.js"></script>
<script>
    /*$(document).ready(function () {

        CKEDITOR.replace('terms_and_conditions');
        CKEDITOR.replace('admit_card_message');
    })*/
    history.pushState(null, null, location.href);
    window.onpopstate = function () {
        history.go(1);
    };
</script>