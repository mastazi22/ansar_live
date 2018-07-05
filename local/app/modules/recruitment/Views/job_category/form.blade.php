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
    @if(isset($errors)&&$errors->first('category_name_bng'))
        <p class="text text-danger">{{$errors->first('category_name_bng')}}</p>
    @endif
</div>
<div class="form-group">
    {!! Form::label('category_description','Job Category Description :',['class'=>'control-label']) !!}
    {!! Form::textarea('category_description',null,['size' => '30x5','class'=>'form-control','placeholder'=>'Enter category description']) !!}
</div>
<div class="form-group">
    {!! Form::label('category_rank','Job Category Rank :',['class'=>'control-label']) !!}
    {!! Form::text('category_rank',null,['class'=>'form-control','placeholder'=>'Enter category rank']) !!}
</div>
<div class="form-group">
    {!! Form::label('category_type','Job Category Type :',['class'=>'control-label']) !!}
    {!! Form::select('category_type',[''=>'--Select a type--','new_training'=>'New Training','apc_training'=>'APC Training','pc_training'=>'PC Training','other'=>'other'],null,['class'=>'form-control']) !!}
</div>
<div class="form-group">
    {!! Form::label('test','Status : ',['class'=>'control-label','style'=>'margin-right:15px']) !!}
    <input type="checkbox" value="active" name="status" @if(isset($data)&&$data->status=='active')checked
           @endif id="status" class="switch-checkbox">
    <label for="status" class=""></label>
</div>
<div class="form-group">
    {!! Form::label('category_header','Header :',['class'=>'control-label']) !!}
    {!! Form::textarea('category_header',null,['class'=>'form-control','placeholder'=>'','id'=>'category_header']) !!}
</div>
<div class="form-group">
    {!! Form::label('category_conditions','Conditions :',['class'=>'control-label']) !!}
    {!! Form::textarea('category_conditions',null,['class'=>'form-control','placeholder'=>'','id'=>'category_conditions']) !!}
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

<script src="//cdn.ckeditor.com/4.8.0/standard/ckeditor.js"></script>
<script>
    $(document).ready(function () {
        CKEDITOR.replace('category_header');
        CKEDITOR.replace('category_conditions');
    })
    history.pushState(null, null, location.href);
    window.onpopstate = function () {
        history.go(1);
    };
</script>