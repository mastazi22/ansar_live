<div>
    @if(isset($data))
        {!! Form::model($data,['route'=>['recruitment.training.update',$data],'method'=>'patch']) !!}
    @else
        {!! Form::open(['route'=>'recruitment.training.store']) !!}
    @endif
    <div class="form-group">
        {!! Form::label('job_circular_id','Select Job Circular :',['class'=>'control-label']) !!}
        {!! Form::select('job_circular_id',$circulars,null,['class'=>'form-control']) !!}
        @if(isset($errors)&&$errors->first('job_circular_id'))
            <p class="text text-danger">{{$errors->first('job_circular_id')}}</p>
        @endif
    </div>
    <div class="form-group">
        {!! Form::label('start_date','Start Date :',['class'=>'control-label']) !!}
        {!! Form::text('start_date',null,['class'=>'form-control','placeholder'=>'Enter start date','date-picker'=>'']) !!}
        @if(isset($errors)&&$errors->first('start_date'))
            <p class="text text-danger">{{$errors->first('start_date')}}</p>
        @endif
    </div>
    <div class="form-group">
        {!! Form::label('end_date','End Date :',['class'=>'control-label']) !!}
        {!! Form::text('end_date',null,['class'=>'form-control','placeholder'=>'Enter end date','date-picker'=>'']) !!}
        @if(isset($errors)&&$errors->first('end_date'))
            <p class="text text-danger">{{$errors->first('end_date')}}</p>
        @endif
    </div>
    @if(isset($data))
        <button type="submit" class="btn btn-primary pull-right">
            <i class="fa fa-save"></i>&nbsp;Update
        </button>
    @else
        <button type="submit" class="btn btn-primary pull-right">
            <i class="fa fa-save"></i>&nbsp;Save
        </button>
    @endif
    {!! Form::close() !!}
</div>
<script>
</script>