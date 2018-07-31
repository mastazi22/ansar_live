@if(isset($data))
    {!! Form::model($data,['route'=>['recruitment.training.center.update',$data],'method'=>'patch','id'=>'center_form']) !!}
@else
    {!! Form::open(['route'=>'recruitment.training.center.store','id'=>'center_form']) !!}
@endif
<div class="form-group">
    {!! Form::label('center_name','Training Center Name :',['class'=>'control-label']) !!}
    {!! Form::text('center_name',null,['class'=>'form-control','placeholder'=>'Enter center name']) !!}
    @if(isset($errors)&&$errors->first('center_name'))
        <p class="text text-danger">{{$errors->first('center_name')}}</p>
    @endif
</div>
<div class="form-group">
    <filter-template
            show-item="['range','unit','thana']"
            type="single"
            data="param"
            start-load="range"
            field-name="{unit:'unit_id',range:'division_id',thana:'thana_id'}"
            layout-vertical="1"
    >
    </filter-template>
</div>
<div class="form-group">
    {!! Form::label('Select trainee division','Select trainee division',['class'=>'control-label']) !!}
    <div class="form-control" style="height: 200px;overflow: auto;">
        <ul>
            @foreach($ranges as $r)
                <li style="list-style: none">
                    @if(isset($data))
                        {!! Form::checkbox('trainee_divisions[]',$r->id,in_array($r->id,explode(',',$data->trainee_divisions)),['style'=>'vertical-align:sub','class'=>'range-app']) !!}
                        &nbsp;{{$r->division_name_bng}}
                    @else
                        {!! Form::checkbox('trainee_divisions[]',$r->id,false,['style'=>'vertical-align:sub','class'=>'range-app']) !!}
                        &nbsp;{{$r->division_name_bng}}
                    @endif
                </li>
            @endforeach
        </ul>
    </div>
</div>
<div class="form-group">
    {!! Form::label('Select trainee district','Select trainee district',['class'=>'control-label']) !!}
    <div class="form-control" style="height: 200px;overflow: auto;">
        <ul>
            @foreach($units as $u)
                <li style="list-style: none">
                    @if(isset($data))
                        {!! Form::checkbox('trainee_units[]',$u->id,in_array($u->division_id,explode(',',$data->trainee_divisions))&&in_array($u->id,explode(',',$data->trainee_units)),['style'=>'vertical-align:sub','data-division-id'=>$u->division_id]) !!}
                        &nbsp;{{$u->unit_name_bng}}
                    @else
                        {!! Form::checkbox('trainee_units[]',$u->id,false,['style'=>'vertical-align:sub','data-division-id'=>$u->division_id,'class'=>'unit-app']) !!}
                        &nbsp;{{$u->unit_name_bng}}
                    @endif
                </li>
            @endforeach
        </ul>
    </div>
</div>
<div class="form-group">
    {!! Form::label('Select trainee district','Trainee district quota',['class'=>'control-label']) !!}
    <div class="form-control" style="height: 200px;overflow: auto;">
        <ul style="padding:0">
            <?php $i = 0;?>
            @foreach($units as $u)
                <li style="list-style: none">
                    @if(isset($data))
                        <?php $d = $data->quota()->where('unit_id', $u->id)->first();?>
                        @if($d)
                            <div style="overflow: hidden;padding: 5px" data-unit-quota-id="{{$u->id}}">
                                {{$u->unit_name_bng}}:
                                <input type="hidden" name="quota[{{$i}}][unit_id]" value="{{$u->id}}">
                                <input type="text" value="{{$d?$d->no_of_quota:''}}" class="pull-right"
                                       name="quota[{{$i}}][no_of_quota]">
                            </div>
                        @else
                            <div style="overflow: hidden;padding: 5px" class="hidden" data-unit-quota-id="{{$u->id}}">
                                {{$u->unit_name_bng}}:
                                <input type="hidden" disabled="disabled" name="quota[{{$i}}][unit_id]"
                                       value="{{$u->id}}">
                                <input disabled="disabled" type="text" value="" class="pull-right"
                                       name="quota[{{$i}}][no_of_quota]">
                            </div>
                        @endif
                    @else
                        <div style="overflow: hidden;padding: 5px" class="hidden" data-unit-quota-id="{{$u->id}}">
                            {{$u->unit_name_bng}}:
                            <input type="hidden" disabled="disabled" name="quota[{{$i}}][unit_id]" value="{{$u->id}}">
                            <input disabled="disabled" type="text" value="" class="pull-right"
                                   name="quota[{{$i}}][no_of_quota]">
                        </div>
                    @endif
                </li>
                <?php $i++;?>
            @endforeach
        </ul>
    </div>
</div>
@if(isset($data))
    <button type="submit" class="btn btn-primary pull-right">
        <i class="fa fa-save"></i>&nbsp;Update Training Center
    </button>
@else
    <button type="submit" class="btn btn-primary pull-right">
        <i class="fa fa-save"></i>&nbsp;Save Training Center
    </button>
@endif
{!! Form::close() !!}

<script src="//cdn.ckeditor.com/4.8.0/standard/ckeditor.js"></script>
<script>
    $(document).ready(function () {
        $(".range-app").on('change', function (event) {
            var status = $(this).prop('checked');
            var v = $(this).val();
            if (status) {
                $('*[data-division-id="' + v + '"]').prop('checked', true).each(function () {
                    $("div[data-unit-quota-id='" + this.value + "']").removeClass("hidden").children('input').prop('disabled', false);
                })

            }
            else {
                $('*[data-division-id="' + v + '"]').prop('checked', false).each(function () {
                    $("div[data-unit-quota-id='" + this.value + "']").addClass("hidden").children('input').prop('disabled', true);
                })
            }
        })
        $(".unit-app").on('change', function (event) {
            var status = $(this).prop('checked');
            var av = $(this).attr('data-division-id');


            if (status) {
                $(".range-app[value='" + av + "']").prop('checked', true);
                $("div[data-unit-quota-id='" + this.value + "']").removeClass("hidden").children('input').prop('disabled', false);
            }
            else {
                $("div[data-unit-quota-id='" + this.value + "']").addClass("hidden").children('input').prop('disabled', true);
            }
            var t = $('*[data-division-id="' + av + '"]:checked').length;
            if (t <= 0) $(".range-app[value='" + av + "']").prop('checked', false);
        })
    })
    $("#center_form").ajaxForm({
        success: function (response) {
            if (response.status) {
                window.location.href = '{{URL::route('recruitment.training.center.index')}}'
            } else {
                alert(response.message)
            }
        },
        error: function (response) {
            alert("An error occur while saving. Contact with administrator")
            console.log(response)
        }
    })
    history.pushState(null, null, location.href);
    window.onpopstate = function () {
        history.go(1);
    };
</script>