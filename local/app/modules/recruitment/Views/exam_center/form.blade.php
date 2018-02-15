<style>
    .item-selected {
        display: inline-block;
        padding: 5px 10px;
        margin: 0 2px 6px 0;
        box-shadow: 0px 0px 5px 0px #cccccc;
        border-radius: 15px;
        background: #49980e;
        color: #ffffff;
    }
</style>

<div ng-controller="jobCircularConstraintController"
     @if(isset($data)&&$data->constraint) ng-init="initConstraint('{{ $data->constraint->constraint}}')" @endif>

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
        <div class="row">
            <div class="col-sm-6">
                {!! Form::label('selection_date','Selection Date :',['class'=>'control-label']) !!}
                {!! Form::text('selection_date',null,['class'=>'form-control','placeholder'=>'Enter Selection Date','date-picker'=>(isset($data)?"moment('".\Carbon\Carbon::parse($data->selection_date)->format('Y-m-d')."').format('DD-MMM-YYYY')":"moment('".\Carbon\Carbon::parse(Request::old('selection_date'))->format('Y-m-d')."').format('DD-MMM-YYYY')")]) !!}
                @if(isset($errors)&&$errors->first('selection_date'))
                    <p class="text text-danger">{{$errors->first('selection_date')}}</p>
                @endif
            </div>
            <div class="col-sm-6">
                {!! Form::label('selection_time','Selection Time :',['class'=>'control-label']) !!}
                {!! Form::text('selection_time',null,['class'=>'form-control time-set','placeholder'=>'HH:MM AM/PM']) !!}
                @if(isset($errors)&&$errors->first('selection_time'))
                    <p class="text text-danger">{{$errors->first('selection_time')}}</p>
                @endif
            </div>
        </div>
    </div>
    <div class="form-group">
        {!! Form::label('written_viva_place','Written Viva Place :',['class'=>'control-label']) !!}
        {!! Form::text('written_viva_place',null,['class'=>'form-control','placeholder'=>'Written Viva Place']) !!}
        @if(isset($errors)&&$errors->first('written_viva_place'))
            <p class="text text-danger">{{$errors->first('written_viva_place')}}</p>
        @endif
    </div>
    <div class="form-group">
        <div class="row">
            <div class="col-sm-6">
                {!! Form::label('viva_date','Viva Date :',['class'=>'control-label']) !!}
                {!! Form::text('viva_date',null,['class'=>'form-control','placeholder'=>'Enter Viva Date','date-picker'=>(isset($data)?"moment('".\Carbon\Carbon::parse($data->viva_date)->format('Y-m-d')."').format('DD-MMM-YYYY')":"moment('".\Carbon\Carbon::parse(Request::old('viva_date'))->format('Y-m-d')."').format('DD-MMM-YYYY')")]) !!}
                @if(isset($errors)&&$errors->first('viva_date'))
                    <p class="text text-danger">{{$errors->first('viva_date')}}</p>
                @endif
            </div>
            <div class="col-sm-6">
                {!! Form::label('viva_time','Viva Time :',['class'=>'control-label']) !!}
                {!! Form::text('viva_time',null,['class'=>'form-control','placeholder'=>'HH:MM AM/PM']) !!}
                @if(isset($errors)&&$errors->first('viva_time'))
                    <p class="text text-danger">{{$errors->first('viva_time')}}</p>
                @endif
            </div>
        </div>
    </div>
    <div class="form-group">
        <div class="row">
            <div class="col-sm-6">
                {!! Form::label('written_date','Written Date :',['class'=>'control-label']) !!}
                {!! Form::text('written_date',null,['class'=>'form-control','placeholder'=>'Enter Written Date','date-picker'=>(isset($data)?"moment('".\Carbon\Carbon::parse($data->written_date)->format('Y-m-d')."').format('DD-MMM-YYYY')":"moment('".\Carbon\Carbon::parse(Request::old('written_date'))->format('Y-m-d')."').format('DD-MMM-YYYY')")]) !!}
                @if(isset($errors)&&$errors->first('written_date'))
                    <p class="text text-danger">{{$errors->first('written_date')}}</p>
                @endif
            </div>
            <div class="col-sm-6">
                {!! Form::label('written_time','Written Time :',['class'=>'control-label']) !!}
                {!! Form::text('written_time',null,['class'=>'form-control','placeholder'=>'HH:MM AM/PM']) !!}
                @if(isset($errors)&&$errors->first('written_time'))
                    <p class="text text-danger">{{$errors->first('written_viva_time')}}</p>
                @endif
            </div>
        </div>
    </div>
    <div class="form-group">
        {!! Form::label('Select applicant district','Select Unit',['class'=>'control-label']) !!}
        <div id="selected-items">
            @if(isset($data))

                @foreach($data->units()->get(['unit_name_bng','tbl_units.id']) as $u)
                    <span data-name="{{$u->id}}" class="item-selected">{{$u->unit_name_bng}}</span>
                @endforeach

            @endif
        </div>
        {!! Form::text('search_unit',null,['class'=>'form-control','placeholder'=>'Search Unit','style'=>'margin-bottom:10px']) !!}
        <div class="form-control" style="height: 200px;overflow: auto;">
            <ul>
                @foreach($units as $u)
                    <li style="list-style: none">
                        @if(isset($data))
                            {!! Form::checkbox('units[]',$u->id,$data->units()->where('tbl_units.id',$u->id)->exists(),['style'=>'vertical-align:sub','data-division-id'=>$u->division_id]) !!}
                            &nbsp;{{$u->unit_name_bng}}
                        @else
                            {!! Form::checkbox('units[]',$u->id,false,['style'=>'vertical-align:sub']) !!}
                            &nbsp;{{$u->unit_name_bng}}
                        @endif
                    </li>
                @endforeach
            </ul>
        </div>
        {{$errors->first('units','<p class="text text-danger">:message</p>')}}
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
        $("input[name='search_unit']").on('input', function (event) {
            var value = $(this).val();
            var s = $(this).siblings('div').find('ul');
            s.children('li').each(function () {
                var t = $(this).text().trim();
                var i = t.indexOf(value);
                if (t.indexOf(value) <= -1 && value) {
                    $(this).css('display', 'none')
                }
                else {
                    $(this).css('display', 'block')
                }
            })
        })
        $("*[name='units[]']").on('change', function (evt) {
            if ($(this).is(':checked')) {
                var html = '<span class="item-selected" data-name="' + $(this).val() + '">' + $(this).parents('li').text().trim() + '</span>'
                $("#selected-items").append(html);
            }
            else {
                /*alert($('span[data-name="'+$(this).val()+'"]').html())
                 alert('span[data-name="'+$(this).val()+'"]')*/
                $('span[data-name="' + $(this).val() + '"]').remove();
            }
        })

    })
    $.fn.selectRange = function (start, end) {
//        alert(1)
        if (end === undefined) {
            end = start;
        }
        return this.each(function () {
            if ('selectionStart' in this) {
                this.selectionStart = start;
                this.selectionEnd = end;
            } else if (this.setSelectionRange) {
                this.setSelectionRange(start, end);
            } else if (this.createTextRange) {
                var range = this.createTextRange();
                range.collapse(true);
                range.moveEnd('character', end);
                range.moveStart('character', start);
                range.select();
            }
        });
    };
</script>