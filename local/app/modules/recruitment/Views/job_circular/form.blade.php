<div ng-controller="jobCircularConstraintController" @if(isset($data)&&$data->constraint) ng-init="initConstraint('{{ $data->constraint->constraint}}')" @endif>

    @if(isset($data))
        {!! Form::model($data,['route'=>['recruitment.circular.update',$data],'method'=>'patch']) !!}
    @else
        {!! Form::open(['route'=>'recruitment.circular.store']) !!}
    @endif
    <div class="form-group">
        {!! Form::label('job_category_id','Select Job Category :',['class'=>'control-label']) !!}
        {!! Form::select('job_category_id',$categories,null,['class'=>'form-control']) !!}
        @if(isset($errors)&&$errors->first('job_category_id'))
            <p class="text text-danger">{{$errors->first('job_category_id')}}</p>
        @endif
    </div>
    <div class="form-group">
        {!! Form::label('circular_name','Job Circular Title :',['class'=>'control-label']) !!}
        {!! Form::text('circular_name',null,['class'=>'form-control','placeholder'=>'Enter circular name']) !!}
        @if(isset($errors)&&$errors->first('circular_name'))
            <p class="text text-danger">{{$errors->first('circular_name')}}</p>
        @endif
    </div>

    <div class="form-group">
        {!! Form::label('start_date','Start Date :',['class'=>'control-label']) !!}
        {!! Form::text('start_date',null,['class'=>'form-control','placeholder'=>'Enter Start Date','date-picker'=>(isset($data)?"moment('{$data->start_date}').format('DD-MMM-YYYY')":"moment('".\Carbon\Carbon::parse(Request::old('start_date'))->format('Y-m-d')."').format('DD-MMM-YYYY')")]) !!}
        @if(isset($errors)&&$errors->first('start_date'))
            <p class="text text-danger">{{$errors->first('start_date')}}</p>
        @endif
    </div>
    <div class="form-group">
        {!! Form::label('end_date','End Date :',['class'=>'control-label']) !!}
        {!! Form::text('end_date',null,['class'=>'form-control','placeholder'=>'Enter Start Date','date-picker'=>(isset($data)?"moment('{$data->end_date}').format('DD-MMM-YYYY')":"moment('".\Carbon\Carbon::parse(Request::old('end_date'))->format('Y-m-d')."').format('DD-MMM-YYYY')")]) !!}
        @if(isset($errors)&&$errors->first('end_date'))
            <p class="text text-danger">{{$errors->first('end_date')}}</p>
        @endif
    </div>
    <div class="form-group">
        {!! Form::label('test','Status : ',['class'=>'control-label','style'=>'margin-right:15px']) !!}
        <input type="checkbox" value="active" name="status"
               @if((isset($data)&&$data->status=='active')||Request::old('status')=='active')checked
               @endif id="status" class="switch-checkbox">
        <label for="status" class=""></label>

    </div>
        <div class="form-group">
            {!! Form::label('Select applicant division','Select applicant division',['class'=>'control-label']) !!}
            <div class="form-control" style="height: 200px;overflow: auto;">
                <ul>
                    @foreach($ranges as $r)
                        <li style="list-style: none">
                            @if(isset($data))
                                {!! Form::checkbox('applicatn_range[]',$r->id,in_array($r->id,explode(',',$data->applicatn_range)),['style'=>'vertical-align:sub','class'=>'range-app']) !!}
                                &nbsp;{{$r->division_name_bng}}
                            @else
                                {!! Form::checkbox('applicatn_range[]',$r->id,true,['style'=>'vertical-align:sub']) !!}
                                &nbsp;{{$r->division_name_bng}}
                            @endif
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
        <div class="form-group">
            {!! Form::label('Select applicant district','Select applicant district',['class'=>'control-label']) !!}
            <div class="form-control" style="height: 200px;overflow: auto;">
                <ul>
                    @foreach($units as $u)
                        <li style="list-style: none">
                            @if(isset($data))
                                {!! Form::checkbox('applicatn_units[]',$u->id,in_array($u->division_id,explode(',',$data->applicatn_range))&&in_array($u->id,explode(',',$data->applicatn_units)),['style'=>'vertical-align:sub','data-division-id'=>$u->division_id]) !!}
                                &nbsp;{{$u->unit_name_bng}}
                            @else
                                {!! Form::checkbox('applicatn_units[]',$u->id,true,['style'=>'vertical-align:sub']) !!}
                                &nbsp;{{$u->unit_name_bng}}
                            @endif
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    <div class="form-group">
        {!! Form::label('test','Auto De-Activate Circular After End Date : ',['class'=>'control-label','style'=>'margin-right:15px']) !!}
        <input type="checkbox" value="1" name="auto_terminate"
               @if((isset($data)&&$data->auto_terminate=='1')||Request::old('auto_terminate')=='1')checked
               @endif id="auto_terminate" class="switch-checkbox">
        <label for="auto_terminate" class=""></label>

    </div>
    <div class="form-group">
        <input type="hidden" name="constraint">
        <button class="btn btn-block btn-link btn-lg" onclick="return false" data-toggle="modal"
                data-target="#constraint-modal">Add rules for circular
        </button>
    </div>
    @if(isset($data))
        <button type="submit" class="btn btn-primary pull-right">
            <i class="fa fa-save"></i>&nbsp;Update Job Circular
        </button>
    @else
        <button type="submit" class="btn btn-primary pull-right">
            <i class="fa fa-save"></i>&nbsp;Save Job Circular
        </button>
    @endif
    {!! Form::close() !!}
    <div id="constraint-modal" class="modal fade" role="dialog">
        <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Constraint</h4>
                </div>
                <div class="modal-body">
                    <div class="constraint-rule">

                        <fieldset>
                            <legend>Gender</legend>
                            <div class="row">
                                <div class="col-sm-6">
                                    <input type="checkbox" ng-model="constraint.gender.male" ng-true-value="'male'"
                                           ng-false-value="''" name="gender-male" value="male" id="gender-male"
                                           class="box-checkbox">
                                    <label for="gender-male">Male</label>
                                </div>
                                <div class="col-sm-6">
                                    <input type="checkbox" ng-model="constraint.gender.female" ng-true-value="'female'"
                                           ng-false-value="''" name="gender-female" value="female" id="gender-female"
                                           class="box-checkbox">
                                    <label for="gender-female">Female</label>
                                </div>
                            </div>
                        </fieldset>
                        <fieldset>
                            <legend>Age</legend>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label">Min age</label>
                                        <input type="text" placeholder="Min age" class="form-control" ng-disabled="constraint.gender.male!='male'&&constraint.gender.female!='female'"
                                               ng-model="constraint.age.min">
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label">Min age date</label>
                                        <input type="text" placeholder="Min age date" date-picker="" date-format="dd-mm-yy" class="form-control" ng-disabled="constraint.gender.male!='male'&&constraint.gender.female!='female'"
                                               ng-model="constraint.age.minDate">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <div class="form-group">
                                            <label class="control-label">Max age</label>
                                            <input type="text" placeholder="Max age" class="form-control" ng-disabled="constraint.gender.male!='male'&&constraint.gender.female!='female'"
                                                   ng-model="constraint.age.max">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="form-group">
                                            <label class="control-label">Max age date</label>
                                            <input type="text" placeholder="Max age date" date-picker="" date-format="dd-mm-yy" class="form-control" ng-disabled="constraint.gender.male!='male'&&constraint.gender.female!='female'"
                                                   ng-model="constraint.age.maxDate">
                                        </div>
                                    </div>
                                </div>
                                {{--<div class="col-md-4">
                                    <div class="form-group">
                                        <div class="form-group">
                                            <label class="control-label">On date</label>
                                            <input type="text" date-picker="" class="form-control" ng-disabled="constraint.gender.male!='male'&&constraint.gender.female!='female'"
                                                   ng-model="constraint.age.date">
                                        </div>
                                    </div>
                                </div>--}}
                            </div>
                        </fieldset>
                        <fieldset>
                            <legend>Height</legend>
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label class="control-label">Male</label>
                                        <div class="row">
                                            <div class="col-md-6" style="padding-right: 0">
                                                <input type="text" ng-disabled="constraint.gender.male!='male'"
                                                       ng-model="constraint.height.male.feet" class="form-control"
                                                       placeholder="Feet">
                                            </div>
                                            <div class="col-md-6" style="padding-right: 0">
                                                <input type="text" ng-disabled="constraint.gender.male!='male'"
                                                       ng-model="constraint.height.male.inch"
                                                       class="form-control" placeholder="Inch">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label class="control-label">Female</label>
                                        <div class="row">
                                            <div class="col-md-6" style="padding-right: 0">
                                                <input type="text" ng-disabled="constraint.gender.female!='female'"
                                                       ng-model="constraint.height.female.feet" class="form-control"
                                                       placeholder="Feet">
                                            </div>
                                            <div class="col-md-6">
                                                <input type="text" ng-disabled="constraint.gender.female!='female'"
                                                       ng-model="constraint.height.female.inch" class="form-control"
                                                       placeholder="Inch">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </fieldset>
                        <fieldset>
                            <legend>Weight</legend>
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label class="control-label">Male</label>
                                        <input type="text" ng-disabled="constraint.gender.male!='male'"
                                               ng-model="constraint.weight.male" class="form-control"
                                               placeholder="Weight in kg">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label class="control-label">Female</label>
                                        <input type="text" ng-disabled="constraint.gender.female!='female'"
                                               ng-model="constraint.weight.female" class="form-control"
                                               placeholder="Weight in kg">
                                    </div>
                                </div>
                            </div>
                        </fieldset>
                        <fieldset>
                            <legend>Chest</legend>
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label class="control-label">Male</label>
                                        <div class="row">
                                            <div class="col-sm-6">
                                                <input type="text" ng-disabled="constraint.gender.male!='male'"
                                                       ng-model="constraint.chest.male.min" class="form-control" placeholder="">
                                            </div>
                                            <div class="col-sm-6">
                                                <input type="text" ng-disabled="constraint.gender.male!='male'"
                                                       ng-model="constraint.chest.male.max" class="form-control" placeholder="">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label class="control-label">Female</label>
                                        <div class="row">
                                            <div class="col-sm-6">
                                                <input type="text" ng-disabled="constraint.gender.male!='female'"
                                                       ng-model="constraint.chest.female.min" class="form-control" placeholder="">
                                            </div>
                                            <div class="col-sm-6">
                                                <input type="text" ng-disabled="constraint.gender.male!='female'"
                                                       ng-model="constraint.chest.female.max" class="form-control" placeholder="">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </fieldset>

                        <fieldset>
                            <legend>Education</legend>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label">Min education</label>
                                        <select name="" id=""
                                                ng-disabled="constraint.gender.male!='male'&&constraint.gender.female!='female'"
                                                ng-model="constraint.education.min" class="form-control">
                                            <option value="">--Select a degree--</option>
                                            <option ng-repeat="(key,value) in minEduList" value="[[key]]">[[value]]</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label">Max education</label>
                                        <select name="" id=""
                                                ng-disabled="constraint.gender.male!='male'&&constraint.gender.female!='female'"
                                                ng-model="constraint.education.max"
                                                class="form-control">
                                            <option value="">--Select a degree--</option>
                                            <option ng-repeat="(key,value) in minEduList" value="[[key]]">[[value]]</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </fieldset>

                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default pull-right" data-dismiss="modal">Close</button>
                    <button type="button" ng-click="onSave('constraint')" class="btn btn-primary pull-left" data-dismiss="modal">Save</button>
                </div>
            </div>

        </div>
    </div>
</div>
<script>
    $(document).ready(function () {
        $(".range-app").on('change',function (event) {
            var status = $(this).prop('checked');
            var v = $(this).val();
            if(status){
                $('*[data-division-id="'+v+'"]').prop('checked',true)
            }
            else{
                $('*[data-division-id="'+v+'"]').prop('checked',false)
            }
        })
    })
</script>