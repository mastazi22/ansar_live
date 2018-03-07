<div>
    @if(isset($data))
        {!! Form::model($data,['route'=>['recruitment.mark_distribution.update',$data],'method'=>'patch']) !!}
    @else
        {!! Form::open(['route'=>'recruitment.mark_distribution.store']) !!}
    @endif
    <div class="form-group">
        {!! Form::label('job_circular_id','Select Job Circular :',['class'=>'control-label']) !!}
        {!! Form::select('job_circular_id',$circulars,null,['class'=>'form-control']) !!}
        @if(isset($errors)&&$errors->first('job_circular_id'))
            <p class="text text-danger">{{$errors->first('job_circular_id')}}</p>
        @endif
    </div>
    <div class="form-group">
        {!! Form::label('physical','Physical Mark :',['class'=>'control-label']) !!}
        {!! Form::text('physical',null,['class'=>'form-control','placeholder'=>'Enter physical mark']) !!}
        @if(isset($errors)&&$errors->first('physical'))
            <p class="text text-danger">{{$errors->first('physical')}}</p>
        @endif
    </div>
    <div class="form-group">
        {!! Form::label('edu_training','Education & Training Mark :',['class'=>'control-label']) !!}
        {!! Form::text('edu_training',null,['class'=>'form-control','placeholder'=>'Enter education & training mark']) !!}
        @if(isset($errors)&&$errors->first('edu_training'))
            <p class="text text-danger">{{$errors->first('edu_training')}}</p>
        @endif
    </div>
    <div class="form-group">
        {!! Form::label('written','Written Mark :',['class'=>'control-label']) !!}
        {!! Form::text('written',null,['class'=>'form-control','placeholder'=>'Enter written mark']) !!}
        @if(isset($errors)&&$errors->first('written'))
            <p class="text text-danger">{{$errors->first('written')}}</p>
        @endif
    </div>
    <div class="form-group">
        {!! Form::label('viva','Viva Mark :',['class'=>'control-label']) !!}
        {!! Form::text('viva',null,['class'=>'form-control','placeholder'=>'Enter viva mark']) !!}
        @if(isset($errors)&&$errors->first('viva'))
            <p class="text text-danger">{{$errors->first('viva')}}</p>
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