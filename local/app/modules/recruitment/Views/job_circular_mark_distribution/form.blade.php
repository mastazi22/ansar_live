<script>
    GlobalApp.controller('MarkDistribution', function ($scope) {
        @if(isset($data))
            $scope.additionalFelds = JSON.parse('{!! !$data->additional_marks?json_encode('[]'):json_encode($data->additional_marks)!!}');
            console.log($scope.additionalFelds)
        @else
            $scope.additionalFelds = [];
        @endif
    });
</script>

<div ng-controller="MarkDistribution">
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
        {!! Form::checkbox('is_physical_checkbox','checked',@(isset($data)&&!empty($data->physical))?true:false,['class'=>'field-active']) !!}
        {!! Form::label('physical','Physical Mark :',['class'=>'control-label']) !!}
        {!! Form::text('physical',null,['class'=>'form-control','placeholder'=>'Enter physical mark']) !!}
        @if(isset($errors)&&$errors->first('physical'))
            <p class="text text-danger">{{$errors->first('physical')}}</p>
        @endif
    </div>
    <div class="form-group">
        {!! Form::checkbox('is_education_and_training_checkbox','checked',@(isset($data)&&!empty($data->edu_training))?true:false,['class'=>'field-active']) !!}
        {!! Form::label('edu_training','Education & Training Mark :',['class'=>'control-label']) !!}
        {!! Form::text('edu_training',null,['class'=>'form-control','placeholder'=>'Enter education & training mark']) !!}
        @if(isset($errors)&&$errors->first('edu_training'))
            <p class="text text-danger">{{$errors->first('edu_training')}}</p>
        @endif
    </div>
    <div class="form-group">
        {!! Form::checkbox('is_physical_and_age_checkbox','checked',@(isset($data)&&!empty($data->physical_age))?true:false,['class'=>'field-active']) !!}
        {!! Form::label('physical_age','Physical & Age Mark :',['class'=>'control-label']) !!}
        {!! Form::text('physical_age',null,['class'=>'form-control','placeholder'=>'Enter education & training mark']) !!}
        @if(isset($errors)&&$errors->first('physical_age'))
            <p class="text text-danger">{{$errors->first('physical_age')}}</p>
        @endif
    </div>
    <div class="form-group">
        {!! Form::checkbox('is_education_and_experience_checkbox','checked',@(isset($data)&&!empty($data->edu_experience))?true:false,['class'=>'field-active']) !!}
        {!! Form::label('edu_experience','Education & Experience Mark :',['class'=>'control-label']) !!}
        {!! Form::text('edu_experience',null,['class'=>'form-control','placeholder'=>'Enter education & experience mark']) !!}
        @if(isset($errors)&&$errors->first('edu_training'))
            <p class="text text-danger">{{$errors->first('edu_training')}}</p>
        @endif
    </div>
    <div class="well">
        <div class="form-group">
            {!! Form::checkbox('is_written_checkbox','checked',@(isset($data)&&!empty($data->written))?true:false,['class'=>'field-active']) !!}
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
    </div>
    <div class="well">
        <div class="form-group">
            {!! Form::checkbox('is_viva_checkbox','checked',@(isset($data)&&!empty($data->viva))?true:false,['class'=>'field-active']) !!}
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
    </div>
    <div class="well">
        <div class="form-group">
            <label for="" class="control-label">
                Additional Fields
                <button class="btn btn-primary btn-xs" ng-click="$event.preventDefault();additionalFelds.push(0)"><i
                            class="fa fa-plus"></i></button>
            </label>
            <div ng-repeat="af in additionalFelds track by $index" style="margin-bottom: 10px">
                <div class="row" style="margin-bottom: 10px">
                    <div class="col-sm-3">
                        <label for="" class="control-label">Title:</label>
                    </div>
                    <div class="col-sm-9">
                        <input type="text" class="form-control" value="[[af?af.label:'']]" name="additional_fields[ [[$index]] ][label]">
                    </div>
                </div>
                <div class="row" style="margin-bottom: 5px">
                    <div class="col-sm-3">
                        <label for="" class="control-label">Value:</label>
                    </div>
                    <div class="col-sm-9">
                        <input type="text" value="[[af?af.value:'']]" class="form-control" name="additional_fields[ [[$index]] ][value]">
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12">
                        <button class="btn btn-danger btn-xs pull-right"
                                ng-click="$event.preventDefault();additionalFelds.splice($index,1)">Remove
                        </button>
                    </div>
                </div>
            </div>
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
    changeField();
    jQuery('.field-active').change(function () {
        changeField();
    });

    function changeField() {
        //==== physical
        if (jQuery("input[name='is_physical_checkbox']").prop('checked')) {
            fieldChecked(jQuery("input[name='physical']"), true);
        } else {
            fieldUnChecked(jQuery("input[name='physical']"), true);
        }

        //==== education and training
        if (jQuery("input[name='is_education_and_training_checkbox']").prop("checked")) {
            fieldChecked(jQuery("input[name='edu_training']"), true);
        } else {
            fieldUnChecked(jQuery("input[name='edu_training']"), true);
        }

        //is_education_and_experience_checkbox
        if (jQuery("input[name='is_education_and_experience_checkbox']").prop("checked")) {
            fieldChecked(jQuery("input[name='edu_experience']"), true);
        } else {
            fieldUnChecked(jQuery("input[name='edu_experience']"), true);
        }

        //is_physical_and_age_checkbox
        if (jQuery("input[name='is_physical_and_age_checkbox']").prop("checked")) {
            fieldChecked(jQuery("input[name='physical_age']"), true);
        } else {
            fieldUnChecked(jQuery("input[name='physical_age']"), true);
        }

        //==== Written
        if (jQuery("input[name='is_written_checkbox']").prop("checked")) {
            fieldChecked(jQuery("input[name='written']"), true);
            fieldChecked(jQuery("input[name='convert_written_mark']"), false);
            fieldChecked(jQuery("input[name='written_pass_mark']"), false);
        } else {
            fieldUnChecked(jQuery("input[name='written']"), true);
            fieldUnChecked(jQuery("input[name='convert_written_mark']"), false);
            fieldUnChecked(jQuery("input[name='written_pass_mark']"), false);
        }

        //==== Viva
        if (jQuery("input[name='is_viva_checkbox']").prop("checked")) {
            fieldChecked(jQuery("input[name='viva']"), true);
            fieldChecked(jQuery("input[name='viva_pass_mark']"), false);
        } else {
            fieldUnChecked(jQuery("input[name='viva']"), true);
            fieldUnChecked(jQuery("input[name='viva_pass_mark']"), false);
        }
    }

    function fieldChecked(element, isRequired) {
        jQuery(element).removeAttr('disabled');
        if (isRequired) {
            jQuery(element).attr("required", "required");
        }
    }

    function fieldUnChecked(element, isRequired) {
        if (!isRequired) {
            jQuery(element).removeAttr('required');
        }
        jQuery(element).val("");
        jQuery(element).attr('disabled', 'disabled');
    }
</script>