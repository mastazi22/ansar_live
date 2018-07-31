@if(isset($data))
    {!! Form::model($data,['route'=>['recruitment.training.category.update',$data],'method'=>'patch']) !!}
@else
    {!! Form::open(['route'=>'recruitment.training.category.store']) !!}
@endif
<div class="form-group">
    {!! Form::label('training_category_name_eng','Training Category Name Eng :',['class'=>'control-label']) !!}
    {!! Form::text('training_category_name_eng',null,['class'=>'form-control','placeholder'=>'Enter category name in english']) !!}
    @if(isset($errors)&&$errors->first('training_category_name_eng'))
        <p class="text text-danger">{{$errors->first('training_category_name_eng')}}</p>
    @endif
</div>
<div class="form-group">
    {!! Form::label('training_category_name_bng','Training Category Name Bng :',['class'=>'control-label']) !!}
    {!! Form::text('training_category_name_bng',null,['class'=>'form-control','placeholder'=>'Enter category name in bangla']) !!}
    @if(isset($errors)&&$errors->first('training_category_name_bng'))
        <p class="text text-danger">{{$errors->first('training_category_name_bng')}}</p>
    @endif
</div>
<div class="form-group">
    {!! Form::label('training_category_description','Training Category Description :',['class'=>'control-label']) !!}
    {!! Form::textarea('training_category_description',null,['size' => '30x5','class'=>'form-control','placeholder'=>'Enter category description']) !!}
</div>
<div class="form-group">
    {!! Form::label('training_category_rank','Training Category Rank :',['class'=>'control-label']) !!}
    {!! Form::text('training_category_rank',null,['class'=>'form-control','placeholder'=>'Enter category rank']) !!}
</div>
<div class="form-group">
    {!! Form::label('training_category_type','Training Category Type :',['class'=>'control-label']) !!}
    {!! Form::select('training_category_type',[''=>'--Select a type--','new_training'=>'New Training','apc_training'=>'APC Training','pc_training'=>'PC Training','other'=>'other'],null,['class'=>'form-control']) !!}
</div>
<div class="form-group">
    {!! Form::label('test','Status : ',['class'=>'control-label','style'=>'margin-right:15px']) !!}
    <input type="checkbox" value="active" name="status" @if(isset($data)&&$data->status=='active')checked
           @endif id="status" class="switch-checkbox">
    <label for="status" class=""></label>
</div>
<div class="form-group">
    {!! Form::label('training_category_header','Header :',['class'=>'control-label']) !!}
    {!! Form::textarea('training_category_header',null,['class'=>'form-control','placeholder'=>'','id'=>'category_header']) !!}
</div>
<div class="form-group">
    {!! Form::label('training_category_conditions','Conditions :',['class'=>'control-label']) !!}
    {!! Form::textarea('training_category_conditions',null,['class'=>'form-control','placeholder'=>'','id'=>'category_conditions']) !!}
</div>
@if(isset($data))
    <button type="submit" class="btn btn-primary pull-right">
        <i class="fa fa-save"></i>&nbsp;Update Training Category
    </button>
@else
    <button type="submit" class="btn btn-primary pull-right">
        <i class="fa fa-save"></i>&nbsp;Save Training Category
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