@if(isset($data))
    {!! Form::model($data,['route'=>['recruitment.circular.update',$data],'method'=>'patch']) !!}
@else
    {!! Form::open(['route'=>'recruitment.circular.store']) !!}
@endif
<div class="form-group">
    {!! Form::label('job_category_id','Select Job Category :',['class'=>'control-label']) !!}
    {!! Form::select('job_category_id',$categories,null,['class'=>'form-control']) !!}
    @if(isset($errors)&&$errors->first('job_category_id'))
        <p class="text text-danger">{{$errors->first('job_category_id')}}</p>
    @endif
</div>
<div class="form-group">
    {!! Form::label('circular_name','Job Circular Title :',['class'=>'control-label']) !!}
    {!! Form::text('circular_name',null,['class'=>'form-control','placeholder'=>'Enter circular name']) !!}
    @if(isset($errors)&&$errors->first('circular_name'))
        <p class="text text-danger">{{$errors->first('circular_name')}}</p>
    @endif
</div>

<div class="form-group">
    {!! Form::label('start_date','Start Date :',['class'=>'control-label']) !!}
    {!! Form::text('start_date',null,['class'=>'form-control','placeholder'=>'Enter Start Date','date-picker']) !!}
    @if(isset($errors)&&$errors->first('start_date'))
        <p class="text text-danger">{{$errors->first('start_date')}}</p>
    @endif
</div>
<div class="form-group">
    {!! Form::label('end_date','End Date :',['class'=>'control-label']) !!}
    {!! Form::text('end_date',null,['class'=>'form-control','placeholder'=>'Enter Start Date','date-picker']) !!}
    @if(isset($errors)&&$errors->first('end_date'))
        <p class="text text-danger">{{$errors->first('end_date')}}</p>
    @endif
</div>
<div class="form-group">
    {!! Form::label('test','Status : ',['class'=>'control-label','style'=>'margin-right:15px']) !!}
    <input type="checkbox" value="active" name="status" @if(isset($data)&&$data->status=='active')checked
           @endif id="status" class="switch-checkbox">
    <label for="status" class=""></label>

</div>
<div class="form-group">
    {!! Form::label('test','Auto De-Activate Circular After End Date : ',['class'=>'control-label','style'=>'margin-right:15px']) !!}
    <input type="checkbox" value="1" name="auto_terminate" @if(isset($data)&&$data->status=='active')checked
           @endif id="status" class="switch-checkbox">
    <label for="status" class=""></label>

</div>
@if(isset($data))
    <button type="submit" class="btn btn-primary pull-right">
        <i class="fa fa-save"></i>&nbsp;Update Job Circular
    </button>
@else
    <button type="submit" class="btn btn-primary pull-right">
        <i class="fa fa-save"></i>&nbsp;Save Job Circular
    </button>
@endif
{!! Form::close() !!}