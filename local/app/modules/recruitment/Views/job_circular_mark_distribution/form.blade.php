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
        {!! Form::label('convert_written_mark','Convert Written Mark To:',['class'=>'control-label']) !!}
        {!! Form::text('convert_written_mark',null,['class'=>'form-control','placeholder'=>'Enter conversion  mark']) !!}
        @if(isset($errors)&&$errors->first('convert_written_mark'))
            <p class="text text-danger">{{$errors->first('convert_written_mark')}}</p>
        @endif
    </div>
    <div class="form-group">
        {!! Form::label('written_pass_mark','Written Qualifying Mark :',['class'=>'control-label']) !!}
        <div class="input-group">
            {!! Form::text('written_pass_mark',null,['class'=>'form-control','placeholder'=>'Enter qualifying mark']) !!}
            <span class="input-group-addon">%</span>
            @if(isset($errors)&&$errors->first('written_pass_mark'))
                <p class="text text-danger">{{$errors->first('written_pass_mark')}}</p>
            @endif
        </div>
    </div>
    <div class="form-group">
        {!! Form::label('viva','Viva Mark :',['class'=>'control-label']) !!}
        {!! Form::text('viva',null,['class'=>'form-control','placeholder'=>'Enter viva mark']) !!}
        @if(isset($errors)&&$errors->first('viva'))
            <p class="text text-danger">{{$errors->first('viva')}}</p>
        @endif
    </div>
    <div class="form-group">
        {!! Form::label('viva_pass_mark','Viva Qualifying Mark :',['class'=>'control-label']) !!}
        <div class="input-group">
            {!! Form::text('viva_pass_mark',null,['class'=>'form-control','placeholder'=>'Enter qualifying mark']) !!}
            <span class="input-group-addon">%</span>
            @if(isset($errors)&&$errors->first('viva_pass_mark'))
                <p class="text text-danger">{{$errors->first('viva_pass_mark')}}</p>
            @endif
        </div>
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