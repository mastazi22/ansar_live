@if(isset($data))
    {!! Form::model($data,['route'=>['recruitment.training.courses.update',$data],'method'=>'patch']) !!}
@else
    {!! Form::open(['route'=>'recruitment.training.courses.store']) !!}
@endif
<div class="form-group">
    {!! Form::label('course_name','Training Course Name :',['class'=>'control-label']) !!}
    {!! Form::text('course_name',null,['class'=>'form-control','placeholder'=>'Enter course name']) !!}
    @if(isset($errors)&&$errors->first('course_name'))
        <p class="text text-danger">{{$errors->first('course_name')}}</p>
    @endif
</div>
<div class="form-group">
    {!! Form::label('course_category_id','Training Course Category :',['class'=>'control-label']) !!}
    {!! Form::select('course_category_id',$categories,null,['class'=>'form-control']) !!}
    @if(isset($errors)&&$errors->first('course_category_id'))
        <p class="text text-danger">{{$errors->first('course_category_id')}}</p>
    @endif
</div>
<div class="form-group">
    {!! Form::label('course_center_ids','Training Course Center :',['class'=>'control-label']) !!}
    <div class="form-control" style="height: 150px;overflow: auto;">
        <ul>
            @forelse($centers as $r)
                <li style="list-style: none">
                    @if(isset($data))
                        {!! Form::checkbox('course_center_ids[]',$r->id,$data->center()->where('training_center.id',$r->id)->exists(),['style'=>'vertical-align:sub','class'=>'range-app']) !!}
                        &nbsp;{{$r->center_name}}
                    @else
                        {!! Form::checkbox('course_center_ids[]',$r->id,false,['style'=>'vertical-align:sub','class'=>'range-app']) !!}
                        &nbsp;{{$r->center_name}}
                    @endif
                </li>
                @empty
                <div class="text-gray text-bold">No training center available</div>
            @endforelse
        </ul>
    </div>
    @if(isset($errors)&&$errors->first('course_center_ids'))
        <p class="text text-danger">{{$errors->first('course_center_ids')}}</p>
    @endif
</div>
<div class="form-group">
    {!! Form::label('number_of_applicants','Number of applicants :',['class'=>'control-label']) !!}
    {!! Form::text('number_of_applicants',null,['class'=>'form-control','placeholder'=>'Enter number of applicant']) !!}
</div>
<div class="form-group">
    {!! Form::label('certificate_message','Certificate Text :',['class'=>'control-label']) !!}
    {!! Form::textarea('certificate_message',null,['class'=>'form-control','placeholder'=>'','id'=>'course_header']) !!}
</div>
<div class="form-group">
    {!! Form::label('terms_and_conditions','Terms and conditions :',['class'=>'control-label']) !!}
    {!! Form::textarea('terms_and_conditions',null,['class'=>'form-control','placeholder'=>'','id'=>'course_conditions']) !!}
</div>
@if(isset($data))
    <button type="submit" class="btn btn-primary pull-right">
        <i class="fa fa-save"></i>&nbsp;Update Training Course
    </button>
@else
    <button type="submit" class="btn btn-primary pull-right">
        <i class="fa fa-save"></i>&nbsp;Save Training Course
    </button>
@endif
{!! Form::close() !!}

<script src="//cdn.ckeditor.com/4.8.0/standard/ckeditor.js"></script>
<script>
    $(document).ready(function () {
        CKEDITOR.replace('course_header');
        CKEDITOR.replace('course_conditions');
    })
    history.pushState(null, null, location.href);
    window.onpopstate = function () {
        history.go(1);
    };
</script>