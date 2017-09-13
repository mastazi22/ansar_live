{!! Form::model($data,['url'=>'/HRM/system_setting_update/'.$data->id ]) !!}

<div class="form-group">
    {!! Form::label('setting_name','Setting Name',['class'=>'control-label']) !!}
    {!! Form::text('setting_name',null,['class'=>'form-control','disabled'=>'disabled']) !!}
</div>
<div class="form-group">
    {!! Form::label('active','Is Active ',['class'=>'control-label']) !!}
    {!! Form::checkbox('active',1,null,['style'=>'vertical-align:sub']) !!}
</div>
<div class="form-group">
    {!! Form::label('setting_value','Setting Value',['class'=>'control-label']) !!}
    <div class="form-control" style="height: 200px;overflow: auto;">
        <ul>
            @foreach($units as $u)
                <li style="list-style: none">
                    @if(in_array($u->id,explode(',',$data->setting_value)))
                        {!! Form::checkbox('values[]',$u->id,true,['style'=>'vertical-align:sub']) !!}
                        &nbsp;{{$u->unit_name_bng}}
                    @else
                        {!! Form::checkbox('values[]',$u->id,null,['style'=>'vertical-align:sub']) !!}
                        &nbsp;{{$u->unit_name_bng}}
                    @endif
                </li>
            @endforeach
        </ul>
    </div>
</div>
<div class="form-group">
    {!! Form::label('description','Setting Description',['class'=>'control-label']) !!}
    {!! Form::textarea('description',null,['class'=>'form-control','size'=>'30x5']) !!}
</div>
<div class="form-group">
    {!! Form::submit('Update Setting',['class'=>'btn btn-primary']) !!}
</div>

{!! Form::close() !!}