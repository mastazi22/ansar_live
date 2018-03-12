<div>
    @if(isset($data))
        {!! Form::model($data,['route'=>['recruitment.marks_rules.update',$data],'method'=>'patch']) !!}
    @else
        {!! Form::open(['route'=>'recruitment.marks_rules.store']) !!}
    @endif
    <div class="form-group">
        {!! Form::label('job_circular_id','Select Job Circular :',['class'=>'control-label']) !!}
        {!! Form::select('job_circular_id',$circulars,null,['class'=>'form-control']) !!}
        @if(isset($errors)&&$errors->first('job_circular_id'))
            <p class="text text-danger">{{$errors->first('job_circular_id')}}</p>
        @endif
    </div>
    <div class="form-group">
        {!! Form::label('point_for','Rules For :',['class'=>'control-label']) !!}
        {!! Form::select('point_for',$rules_for,null,['class'=>'form-control']) !!}
        @if(isset($errors)&&$errors->first('point_for'))
            <p class="text text-danger">{{$errors->first('point_for')}}</p>
        @endif
    </div>
    <div class="form-group">
        {!! Form::label('rule_name','Rule name :',['class'=>'control-label']) !!}
        {!! Form::select('rule_name',$rules_name,null,['class'=>'form-control']) !!}
        @if(isset($errors)&&$errors->first('rule_name'))
            <p class="text text-danger">{{$errors->first('rule_name')}}</p>
        @endif
    </div>
    <div id="height_rules" class="rules-class" style="display: none">
        <h4 class="text-center" style="border-bottom: 1px solid #000000">Rule for Height</h4>
        <div class="form-group">
            {!! Form::label('','Min Height:',['class'=>'control-label']) !!}
            <div class="row">
                <div class="col-sm-6">
                    <div class="input-group">
                        {!! Form::text('min_height_feet',null,['class'=>'form-control','placeholder'=>'Feet']) !!}
                        <span class="input-group-addon">Feet</span>
                        @if(isset($errors)&&$errors->first('min_height_feet'))
                            <p class="text text-danger">{{$errors->first('min_height_feet')}}</p>
                        @endif
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="input-group">
                        {!! Form::text('min_height_inch',null,['class'=>'form-control','placeholder'=>'Inch']) !!}
                        <span class="input-group-addon">Inch</span>
                        @if(isset($errors)&&$errors->first('min_height_inch'))
                            <p class="text text-danger">{{$errors->first('min_height_inch')}}</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <div class="form-group">
            {!! Form::label('min_point','Min Point :',['class'=>'control-label']) !!}
            {!! Form::text('min_point',null,['class'=>'form-control','placeholder'=>'Min Point']) !!}
            @if(isset($errors)&&$errors->first('min_point'))
                <p class="text text-danger">{{$errors->first('min_point')}}</p>
            @endif
        </div>
        <div class="form-group">
            {!! Form::label('','Max Height:',['class'=>'control-label']) !!}
            <div class="row">
                <div class="col-sm-6">
                    <div class="input-group">
                        {!! Form::text('max_height_feet',null,['class'=>'form-control','placeholder'=>'Feet']) !!}
                        <span class="input-group-addon">Feet</span>
                        @if(isset($errors)&&$errors->first('max_height_feet'))
                            <p class="text text-danger">{{$errors->first('max_height_feet')}}</p>
                        @endif
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="input-group">
                        {!! Form::text('max_height_inch',null,['class'=>'form-control','placeholder'=>'Inch']) !!}
                        <span class="input-group-addon">Inch</span>
                        @if(isset($errors)&&$errors->first('max_height_inch'))
                            <p class="text text-danger">{{$errors->first('max_height_inch')}}</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <div class="form-group">
            {!! Form::label('max_point','Max Point :',['class'=>'control-label']) !!}
            {!! Form::text('max_point',null,['class'=>'form-control','placeholder'=>'Max Point']) !!}
            @if(isset($errors)&&$errors->first('max_point'))
                <p class="text text-danger">{{$errors->first('max_point')}}</p>
            @endif
        </div>
    </div>
    <div id="education_rules" class="rules-class" style="display: none;">
        <h4 class="text-center" style="border-bottom: 1px solid #000000">Rule for Education</h4>
        <div class="form-group">

            <table class="table table-bordered">
                <tr>
                    <th>#</th>
                    <th>Education degree</th>
                    <th>Priority</th>
                    <th>Point</th>
                </tr>
                <?php $i=0;$j=0?>
                @foreach($educations as $education)
                    <tr class="edu_c" data-id="{{$education->id}}">
                        <td>{{++$i}}</td>
                        <td><strong>{{$education->education_name}}</strong></td>
                        <td><strong>{{$education->priority}}</strong></td>
                        <td>
                            {!! Form::text("edu_point[$j][point]",null,['placeholder'=>'point']) !!}
                            {!! Form::hidden("edu_point[$j][priority]",$education->priority) !!}
                        </td>
                    </tr>
                    <?php $j++; ?>
                @endforeach
            </table>

        </div>
        <div class="form-group">
            <h4>Choose a option</h4>
            <div class="radio">
                <label><input type="radio" name="edu_p_count" value="1">Point count only ascending priority</label>
            </div>
            <div class="radio">
                <label><input type="radio" name="edu_p_count" value="2">Point count only descending priority</label>
            </div>
            <div class="radio">
                <label><input type="radio" name="edu_p_count" value="3">Sum all education point</label>
            </div>
        </div>
    </div>
    <div id="training_rules" class="rules-class" style="display: none;">
        <h4 class="text-center" style="border-bottom: 1px solid #000000">Rule for Training</h4>
        <div class="form-group">
            {!! Form::label('training_point','Training Point :',['class'=>'control-label']) !!}
            {!! Form::text('training_point',null,['class'=>'form-control','placeholder'=>'Training Point']) !!}
            @if(isset($errors)&&$errors->first('training_point'))
                <p class="text text-danger">{{$errors->first('training_point')}}</p>
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
    $(document).ready(function () {
        var constraint;
        function initCheck() {
            var v = $("select[name='rule_name']").val();
            var cid = $("select[name='job_circular_id']").val();
            if(cid){
                loadConstraint(cid);
            }
            if(!v) return;
            var id = `#${v}_rules`;
            $(id).show();
        }
        $("select[name='rule_name']").on('change',function (evt) {
            var v = $(this).val();
            $(".rules-class").hide();
            if(!v) {

                return;
            }
            var id = `#${v}_rules`;
            $(id).show();
            modifyRule();
        })
        $("select[name='job_circular_id']").on('change',function (evt) {
            var v = $(this).val();
            loadConstraint(v);
        })
        function loadConstraint(id) {
            $.ajax({
                url:'{{URL::to("/recruitment/circular/constraint")}}/'+id,
                type:'get',
                success:function (response) {
                    try{
                        constraint = JSON.parse(response)
                    }catch(exp){
                        constraint = response;
                    }
                    modifyRule();
                    console.log(constraint)
                },
                error:function (res) {
                    console.log(res)
                }
            })
        }
        function modifyRule(){
            var t = $(".edu_c");
            var e = constraint.education;
            t.each(function (obj) {
                var a = +$(this).attr("data-id");
                if(a<+e.min||a>+e.max){
                    $(this).remove();
                }
            })
        }
        initCheck();
    })
</script>