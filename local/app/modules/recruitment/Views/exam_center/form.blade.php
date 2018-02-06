<div ng-controller="jobCircularConstraintController" @if(isset($data)&&$data->constraint) ng-init="initConstraint('{{ $data->constraint->constraint}}')" @endif>

    @if(isset($data))
        {!! Form::model($data,['route'=>['recruitment.exam-center.update',$data],'method'=>'patch']) !!}
    @else
        {!! Form::open(['route'=>'recruitment.exam-center.store']) !!}
    @endif
    <div class="form-group">
        {!! Form::label('job_circular_id','Select Job Circular :',['class'=>'control-label']) !!}
        {!! Form::select('job_circular_id',$circulars,null,['class'=>'form-control']) !!}
        @if(isset($errors)&&$errors->first('job_circular_id'))
            <p class="text text-danger">{{$errors->first('job_category_id')}}</p>
        @endif
    </div>
    <div class="form-group">
        {!! Form::label('selection_place','Selection Place :',['class'=>'control-label']) !!}
        {!! Form::text('selection_place',null,['class'=>'form-control','placeholder'=>'Enter Selection Place']) !!}
        @if(isset($errors)&&$errors->first('selection_place'))
            <p class="text text-danger">{{$errors->first('selection_place')}}</p>
        @endif
    </div>

    <div class="form-group">
        {!! Form::label('selection_date','Selection Date :',['class'=>'control-label']) !!}
        {!! Form::text('selection_date',null,['class'=>'form-control','placeholder'=>'Enter Selection Date','date-picker'=>(isset($data)?"moment('{$data->selection_date}').format('DD-MMM-YYYY')":"moment('".\Carbon\Carbon::parse(Request::old('selection_date'))->format('Y-m-d')."').format('DD-MMM-YYYY')")]) !!}
        @if(isset($errors)&&$errors->first('selection_date'))
            <p class="text text-danger">{{$errors->first('selection_date')}}</p>
        @endif
    </div>
    <div class="form-group">
        {!! Form::label('written_viva_place','Written Viva Place :',['class'=>'control-label']) !!}
        {!! Form::text('written_viva_place',null,['class'=>'form-control','placeholder'=>'Written Viva Place']) !!}
        @if(isset($errors)&&$errors->first('written_viva_place'))
            <p class="text text-danger">{{$errors->first('written_viva_place')}}</p>
        @endif
    </div>
        <div class="form-group">
            {!! Form::label('written_viva_date','Written Viva Date :',['class'=>'control-label']) !!}
            {!! Form::text('written_viva_date',null,['class'=>'form-control','placeholder'=>'Enter Selection Date','date-picker'=>(isset($data)?"moment('{$data->written_viva_date}').format('DD-MMM-YYYY')":"moment('".\Carbon\Carbon::parse(Request::old('written_viva_date'))->format('Y-m-d')."').format('DD-MMM-YYYY')")]) !!}
            @if(isset($errors)&&$errors->first('written_viva_date'))
                <p class="text text-danger">{{$errors->first('written_viva_date')}}</p>
            @endif
        </div>
        <div class="form-group">
            {!! Form::label('Select applicant district','Selection Units',['class'=>'control-label']) !!}
            {!! Form::text('search_unit',null,['class'=>'form-control','placeholder'=>'Search Unit','style'=>'margin-bottom:10px']) !!}
            <div class="form-control" style="height: 200px;overflow: auto;">
                <ul>
                    @foreach($units as $u)
                        <li style="list-style: none">
                            @if(isset($data))
                                {!! Form::checkbox('selection_units[]',$u->id,in_array($u->unit_name_bng,explode(',',$data->selection_units)),['style'=>'vertical-align:sub','data-division-id'=>$u->division_id]) !!}
                                &nbsp;{{$u->unit_name_bng}}
                            @else
                                {!! Form::checkbox('selection_units[]',$u->id,false,['style'=>'vertical-align:sub']) !!}
                                &nbsp;{{$u->unit_name_bng}}
                            @endif
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
        <div class="form-group">
            {!! Form::label('written_viva_units','Written Viva Units',['class'=>'control-label']) !!}
            {!! Form::text('search_unit',null,['class'=>'form-control','placeholder'=>'Search Unit','style'=>'margin-bottom:10px']) !!}
            <div class="form-control" style="height: 200px;overflow: auto;">
                <ul>
                    @foreach($units as $u)
                        <li style="list-style: none">
                            @if(isset($data))
                                {!! Form::checkbox('written_viva_units[]',$u->id,in_array($u->unit_name_bng,explode(',',$data->selection_units)),['style'=>'vertical-align:sub']) !!}
                                &nbsp;{{$u->unit_name_bng}}
                            @else
                                {!! Form::checkbox('written_viva_units[]',$u->id,false,['style'=>'vertical-align:sub']) !!}
                                &nbsp;{{$u->unit_name_bng}}
                            @endif
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    @if(isset($data))
        <button type="submit" class="btn btn-primary pull-right">
            <i class="fa fa-save"></i>&nbsp;Update Exam Center
        </button>
    @else
        <button type="submit" class="btn btn-primary pull-right">
            <i class="fa fa-save"></i>&nbsp;Save exam Center
        </button>
    @endif
    {!! Form::close() !!}
</div>
<script>
    $(document).ready(function () {
        $("input[name='search_unit']").on('input',function (event) {
            var value = $(this).val();
            var s = $(this).siblings('div').find('ul');
            s.children('li').each(function () {
                var t = $(this).text().trim();
                var i = t.indexOf(value);
                if(t.indexOf(value)<=-1&&value){
                    $(this).css('display','none')
                }
                else{
                    $(this).css('display','block')
                }
            })
        })
    })
</script>