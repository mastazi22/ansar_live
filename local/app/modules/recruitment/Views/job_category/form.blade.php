@if(isset($data))
    {!! Form::model($data,['route'=>['recruitment.category.update',$data],'method'=>'patch']) !!}
@else
    {!! Form::open(['route'=>'recruitment.category.store']) !!}
@endif
<div class="form-group">
    {!! Form::label('category_name_eng','Job Category Name Eng :',['class'=>'control-label']) !!}
    {!! Form::text('category_name_eng',null,['class'=>'form-control','placeholder'=>'Enter category name in english']) !!}
    @if(isset($errors)&&$errors->first('category_name_eng'))
        <p class="text text-danger">{{$errors->first('category_name_eng')}}</p>
    @endif
</div>
<div class="form-group">
    {!! Form::label('category_name_bng','Job Category Name Bng :',['class'=>'control-label']) !!}
    {!! Form::text('category_name_bng',null,['class'=>'form-control','placeholder'=>'Enter category name in bangla']) !!}
</div>
<div class="form-group">
    {!! Form::label('category_description','Job Category Description :',['class'=>'control-label']) !!}
    {!! Form::textarea('category_description',null,['size' => '30x5','class'=>'form-control','placeholder'=>'Enter category description']) !!}
</div>
<div class="form-group">
    {!! Form::label('test','Status : ',['class'=>'control-label','style'=>'margin-right:15px']) !!}
    <input type="checkbox" value="active" name="status" @if(isset($data)&&$data->status=='active')checked
           @endif id="status" class="switch-checkbox">
    <label for="status" class=""></label>

</div>
@if(isset($data))
    <button type="submit" class="btn btn-primary pull-right">
        <i class="fa fa-save"></i>&nbsp;Update Job Category
    </button>
@else
    <button type="submit" class="btn btn-primary pull-right">
        <i class="fa fa-save"></i>&nbsp;Update Job Category
    </button>
@endif
{!! Form::close() !!}