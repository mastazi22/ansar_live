<div ng-controller="jobCircularConstraintController"
     @if(isset($data)&&$data->constraint) ng-init="initConstraint('{{ $data->constraint->constraint}}')" @endif>
    <?php $quota_list = isset($data)&&$data->applicantQuotaRelation ? $data->applicantQuotaRelation->pluck('id')->toArray() : [] ?>
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
        {!! Form::label('circular_code','Job Circular Code :',['class'=>'control-label']) !!}
        {!! Form::text('circular_code',null,['class'=>'form-control','placeholder'=>'Enter circular code']) !!}
        @if(isset($errors)&&$errors->first('circular_code'))
            <p class="text text-danger">{{$errors->first('circular_code')}}</p>
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
        {!! Form::label('pay_amount','Pay Amount :',['class'=>'control-label']) !!}
        {!! Form::text('pay_amount',null,['class'=>'form-control','placeholder'=>'Pay Amount']) !!}
        @if(isset($errors)&&$errors->first('pay_amount'))
            <p class="text text-danger">{{$errors->first('pay_amount')}}</p>
        @endif
    </div>
    <div class="form-group">
        {!! Form::label('terms_and_conditions','Terms and Conditions :',['class'=>'control-label']) !!}
        {!! Form::textarea('terms_and_conditions',null,['class'=>'form-control','placeholder'=>'','id'=>'terms_and_conditions']) !!}
    </div>
    <div class="form-group">
        {!! Form::label('test','Circular Status : ',['class'=>'control-label','style'=>'margin-right:15px']) !!}
        <input type="checkbox" value="running" name="circular_status"
               @if((isset($data)&&$data->circular_status=='running')||Request::old('circular_status')=='running')checked
               @endif id="circular_status" class="switch-checkbox">
        <label for="circular_status" class=""></label>
    </div>
    <div class="form-group">
        {!! Form::label('test','Status : ',['class'=>'control-label','style'=>'margin-right:15px']) !!}
        <input type="checkbox" value="active" name="status"
               @if((isset($data)&&$data->status=='active')||Request::old('status')=='active')checked
               @endif id="status" class="switch-checkbox">
        <label for="status" class=""></label>
    </div>
    <div class="form-group">
        {!! Form::label('test','Login Status : ',['class'=>'control-label','style'=>'margin-right:15px']) !!}
        <input type="checkbox" value="on" name="login_status"
               @if((isset($data)&&$data->login_status=='on')||Request::old('login_status')=='on')checked
               @endif id="login_status" class="switch-checkbox">
        <label for="login_status" class=""></label>
    </div>
    <div class="form-group">
        {!! Form::label('test','Payment Status : ',['class'=>'control-label','style'=>'margin-right:15px']) !!}
        <input type="checkbox" value="on" name="payment_status"
               @if((isset($data)&&$data->payment_status=='on')||Request::old('payment_status')=='on')checked
               @endif id="payment_status" class="switch-checkbox">
        <label for="payment_status" class=""></label>
    </div>
    <div class="form-group">
        {!! Form::label('test','Application Status : ',['class'=>'control-label','style'=>'margin-right:15px']) !!}
        <input type="checkbox" value="on" name="application_status"
               @if((isset($data)&&$data->application_status=='on')||Request::old('application_status')=='on')checked
               @endif id="application_status" class="switch-checkbox">
        <label for="application_status" class=""></label>
    </div>
    <div class="form-group">
        {!! Form::label('test','Admit Card Print Status : ',['class'=>'control-label','style'=>'margin-right:15px']) !!}
        <input type="checkbox" value="on" name="admit_card_print_status"
               @if((isset($data)&&$data->admit_card_print_status=='on')||Request::old('admit_card_print_status')=='on')checked
               @endif id="admit_card_print_status" class="switch-checkbox">
        <label for="admit_card_print_status" class=""></label>
    </div>
    <div class="form-group">
        {!! Form::label('test','Submit Problem Status : ',['class'=>'control-label','style'=>'margin-right:15px']) !!}
        <input type="checkbox" value="on" name="submit_problem_status"
               @if((isset($data)&&$data->submit_problem_status=='on')||Request::old('submit_problem_status')=='on')checked
               @endif id="submit_problem_status" class="switch-checkbox">
        <label for="submit_problem_status" class=""></label>
    </div>
    <div class="form-group">

        {!! Form::label('test','Quota applied for all divisions and districts : ',['class'=>'control-label','style'=>'margin-right:15px']) !!}
        <input type="checkbox" ng-readonly="quota_district_division==1&&filterFunc().length>0" value="on" name="quota_district_division" ng-model="quota_district_division"
               @if((isset($data)&&$data->quota_district_division=='on')||Request::old('quota_district_division')=='on')checked
               ng-init="quota_district_division=1"
               @endif id="quota_district_division" class="switch-checkbox" ng-model="quota_district_division"
               ng-true-value="1"
               ng-false-value="0">
        <label for="quota_district_division" class=""></label>
        <div class="form-control" ng-if="quota_district_division"
             style="height: 100px;overflow: auto;transition: all 1s">
            <ul ng-init="initQuotaArray({{count($circular_quota)}})">
                <?php $i = 0; ?>

                @forelse($circular_quota as $quota)
                    @if((count($quota_list)?in_array($quota->id,$quota_list):false)||(Request::old('quota_type')?in_array($quota->id,Request::old('quota_type')):false))
                        <li style="list-style: none">{!! Form::checkbox('quota_type[]',$quota->id,true,["style"=>"vertical-align:sub","class"=>"range-app","ng-model"=>"circular.selected[".$i."]","ng-init"=>"initQuota(".$i.",'".json_encode($quota)."')","ng-change"=>"changeQuota(".$i.",'".json_encode($quota)."')"]) !!}{{$quota->quota_name_bng}}</li>
                    @else
                        <li style="list-style: none">{!! Form::checkbox('quota_type[]',$quota->id,false,["style"=>"vertical-align:sub","class"=>"range-app","ng-model"=>"circular.selected[".$i."]","ng-change"=>"changeQuota(".$i.",'".json_encode($quota)."')"]) !!}{{$quota->quota_name_bng}}</li>
                    @endif
                    <?php $i++; ?>
                @empty
                    <li style='list-style: none'>No Quota Available</li>
                @endforelse
            </ul>
        </div>
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
                            {!! Form::checkbox('applicatn_range[]',$r->id,true,['style'=>'vertical-align:sub','class'=>'range-app']) !!}
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
                            {!! Form::checkbox('applicatn_units[]',$u->id,true,['style'=>'vertical-align:sub','data-division-id'=>$u->division_id]) !!}
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
        {!! Form::label('admit_card_message','Message for admit card :',['class'=>'control-label']) !!}
        {!! Form::textarea('admit_card_message',null,['class'=>'form-control','placeholder'=>'','id'=>'admit_card_message']) !!}
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
                    <div class="panel-group" id="accordine">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h4 class="panel-title">
                                    <a data-toggle="collapse" data-parent="#accordion" href="#common-rules" class="collapsed" aria-expanded="false">
                                        Commom Rules
                                    </a>
                                </h4>
                            </div>
                            <div id="common-rules" class="panel-collapse collapse in" aria-expanded="false">
                                <div class="panel-body">
                                    <div class="constraint-rule">
                                        <fieldset>
                                            <legend>Gender</legend>
                                            <div class="row">
                                                <div class="col-sm-6">
                                                    <input type="checkbox" ng-model="applicationRules[0].gender.male" ng-true-value="'male'"
                                                           ng-false-value="''" name="gender-male" value="male" id="gender-male"
                                                           class="box-checkbox">
                                                    <label for="gender-male">Male</label>
                                                </div>
                                                <div class="col-sm-6">
                                                    <input type="checkbox" ng-model="applicationRules[0].gender.female" ng-true-value="'female'"
                                                           ng-false-value="''" name="gender-female" value="female" id="gender-female"
                                                           class="box-checkbox">
                                                    <label for="gender-female">Female</label>
                                                </div>
                                            </div>
                                        </fieldset>
                                        {{--start age validation rules--}}
                                        <fieldset>
                                            <legend>
                                                Age
                                            </legend>
                                            {{--age validation rules--}}
                                            <div class="row">

                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label class="control-label">Min age</label>
                                                        <input type="text" placeholder="Min age" class="form-control"
                                                               ng-disabled="(applicationRules[0].gender.male!='male'&&applicationRules[0].gender.female!='female')"
                                                               ng-model="applicationRules[0].age.min">
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="control-label">Min age date</label>
                                                        <input type="text" placeholder="Min age date" date-picker=""
                                                               class="form-control"
                                                               ng-disabled="(applicationRules[0].gender.male!='male'&&applicationRules[0].gender.female!='female')"
                                                               ng-model="applicationRules[0].age.minDate">
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <div class="form-group">
                                                            <label class="control-label">Max age</label>
                                                            <input type="text" placeholder="Max age" class="form-control"
                                                                   ng-disabled="(applicationRules[0].gender.male!='male'&&applicationRules[0].gender.female!='female')"
                                                                   ng-model="applicationRules[0].age.max">
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <div class="form-group">
                                                            <label class="control-label">Max age date</label>
                                                            <input type="text" placeholder="Max age date" date-picker=""
                                                                   class="form-control"
                                                                   ng-disabled="(applicationRules[0].gender.male!='male'&&applicationRules[0].gender.female!='female')"
                                                                   ng-model="applicationRules[0].age.maxDate">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            {{--end age validation rules--}}
                                        </fieldset>
                                        {{--end age validation rules--}}
                                        {{--start height validation rules--}}
                                        <fieldset>
                                            <legend>Height</legend>
                                            <div class="row">
                                                <div class="col-sm-6">
                                                    <div class="form-group">
                                                        <label class="control-label">Male</label>
                                                        <div class="row">
                                                            <div class="col-md-6" style="padding-right: 0">
                                                                <input type="text" ng-disabled="applicationRules[0].gender.male!='male'"
                                                                       ng-model="applicationRules[0].height.male.feet" class="form-control"
                                                                       placeholder="Feet">
                                                            </div>
                                                            <div class="col-md-6" style="padding-right: 0">
                                                                <input type="text" ng-disabled="applicationRules[0].gender.male!='male'"
                                                                       ng-model="applicationRules[0].height.male.inch"
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
                                                                <input type="text" ng-disabled="applicationRules[0].gender.female!='female'"
                                                                       ng-model="applicationRules[0].height.female.feet" class="form-control"
                                                                       placeholder="Feet">
                                                            </div>
                                                            <div class="col-md-6">
                                                                <input type="text" ng-disabled="applicationRules[0].gender.female!='female'"
                                                                       ng-model="applicationRules[0].height.female.inch" class="form-control"
                                                                       placeholder="Inch">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </fieldset>
                                        {{--end height validation rules--}}
                                        {{--start weight validation rules--}}
                                        <fieldset>
                                            <legend>Weight</legend>
                                            <div class="row">
                                                <div class="col-sm-6">
                                                    <div class="form-group">
                                                        <label class="control-label">Male</label>
                                                        <input type="text" ng-disabled="applicationRules[0].gender.male!='male'"
                                                               ng-model="applicationRules[0].weight.male" class="form-control"
                                                               placeholder="Weight in kg">
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="form-group">
                                                        <label class="control-label">Female</label>
                                                        <input type="text" ng-disabled="applicationRules[0].gender.female!='female'"
                                                               ng-model="applicationRules[0].weight.female" class="form-control"
                                                               placeholder="Weight in kg">
                                                    </div>
                                                </div>
                                            </div>
                                        </fieldset>
                                        {{--end weight validation rules--}}
                                        {{--start chest validation rules--}}
                                        <fieldset>
                                            <legend>Chest</legend>
                                            <div class="row">
                                                <div class="col-sm-6">
                                                    <div class="form-group">
                                                        <label class="control-label">Male</label>
                                                        <div class="row">
                                                            <div class="col-sm-6">
                                                                <input type="text" ng-disabled="applicationRules[0].gender.male!='male'"
                                                                       ng-model="applicationRules[0].chest.male.min" class="form-control"
                                                                       placeholder="">
                                                            </div>
                                                            <div class="col-sm-6">
                                                                <input type="text" ng-disabled="applicationRules[0].gender.male!='male'"
                                                                       ng-model="applicationRules[0].chest.male.max" class="form-control"
                                                                       placeholder="">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="form-group">
                                                        <label class="control-label">Female</label>
                                                        <div class="row">
                                                            <div class="col-sm-6">
                                                                <input type="text" ng-disabled="applicationRules[0].gender.female!='female'"
                                                                       ng-model="applicationRules[0].chest.female.min" class="form-control"
                                                                       placeholder="">
                                                            </div>
                                                            <div class="col-sm-6">
                                                                <input type="text" ng-disabled="applicationRules[0].gender.female!='female'"
                                                                       ng-model="applicationRules[0].chest.female.max" class="form-control"
                                                                       placeholder="">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </fieldset>
                                        {{--end chest validation rules--}}
                                        <fieldset>
                                            <legend>Education</legend>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label class="control-label">Min education</label>
                                                        <select name="" id=""
                                                                ng-disabled="applicationRules[0].gender.male!='male'&&applicationRules[0].gender.female!='female'"
                                                                ng-model="applicationRules[0].education.min" class="form-control">
                                                            <option value="">--Select a degree--</option>
                                                            <option ng-repeat="(key,value) in minEduList" value="[[key]]">[[value]]
                                                            </option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label class="control-label">Max education</label>
                                                        <select name="" id=""
                                                                ng-disabled="applicationRules[0].gender.male!='male'&&applicationRules[0].gender.female!='female'"
                                                                ng-model="applicationRules[0].education.max"
                                                                class="form-control">
                                                            <option value="">--Select a degree--</option>
                                                            <option ng-repeat="(key,value) in minEduList" value="[[key]]">[[value]]
                                                            </option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </fieldset>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="panel panel-default" ng-repeat="q in filterFunc()" ng-init="initRules(q.id)">
                            <div class="panel-heading">
                                <h4 class="panel-title">
                                    <a data-toggle="collapse" data-parent="#accordion" href="#[[q.quota_name_eng.split(' ').join('_')]]" class="collapsed" aria-expanded="false">
                                        [[q.quota_name_eng]]
                                    </a>
                                </h4>
                            </div>
                            <div id="[[q.quota_name_eng.split(' ').join('_')]]" class="panel-collapse collapse" aria-expanded="false">
                                <div class="panel-body">
                                    <div class="constraint-rule">
                                        <fieldset>
                                            <legend>Gender</legend>
                                            <div class="row">
                                                <div class="col-sm-6">
                                                    <input type="checkbox" ng-model="applicationRules[q.id].gender.male" ng-true-value="'male'"
                                                           ng-false-value="''" name="gender-male-[[$index]]" value="male" id="gender-male-[[$index]]"
                                                           class="box-checkbox">
                                                    <label for="gender-male-[[$index]]">Male</label>
                                                </div>
                                                <div class="col-sm-6">
                                                    <input type="checkbox" ng-model="applicationRules[q.id].gender.female" ng-true-value="'female'"
                                                           ng-false-value="''" name="gender-female-[[$index]]" value="female" id="gender-female-[[$index]]"
                                                           class="box-checkbox">
                                                    <label for="gender-female-[[$index]]">Female</label>
                                                </div>
                                            </div>
                                        </fieldset>
                                        {{--start age validation rules--}}
                                        <fieldset>
                                            <legend>
                                                Age
                                            </legend>
                                            {{--age validation rules--}}
                                            <div class="row">

                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label class="control-label">Min age</label>
                                                        <input type="text" placeholder="Min age" class="form-control"
                                                               ng-disabled="(applicationRules[q.id].gender.male!='male'&&applicationRules[q.id].gender.female!='female')"
                                                               ng-model="applicationRules[q.id].age.min">
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="control-label">Min age date</label>
                                                        <input type="text" placeholder="Min age date" date-picker=""
                                                               class="form-control"
                                                               ng-disabled="(applicationRules[q.id].gender.male!='male'&&applicationRules[q.id].gender.female!='female')"
                                                               ng-model="applicationRules[q.id].age.minDate">
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <div class="form-group">
                                                            <label class="control-label">Max age</label>
                                                            <input type="text" placeholder="Max age" class="form-control"
                                                                   ng-disabled="(applicationRules[q.id].gender.male!='male'&&applicationRules[q.id].gender.female!='female')"
                                                                   ng-model="applicationRules[q.id].age.max">
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <div class="form-group">
                                                            <label class="control-label">Max age date</label>
                                                            <input type="text" placeholder="Max age date" date-picker=""
                                                                   class="form-control"
                                                                   ng-disabled="(applicationRules[q.id].gender.male!='male'&&applicationRules[q.id].gender.female!='female')"
                                                                   ng-model="applicationRules[q.id].age.maxDate">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            {{--end age validation rules--}}
                                        </fieldset>
                                        {{--end age validation rules--}}
                                        {{--start height validation rules--}}
                                        <fieldset>
                                            <legend>Height</legend>
                                            <div class="row">
                                                <div class="col-sm-6">
                                                    <div class="form-group">
                                                        <label class="control-label">Male</label>
                                                        <div class="row">
                                                            <div class="col-md-6" style="padding-right: 0">
                                                                <input type="text" ng-disabled="applicationRules[q.id].gender.male!='male'"
                                                                       ng-model="applicationRules[q.id].height.male.feet" class="form-control"
                                                                       placeholder="Feet">
                                                            </div>
                                                            <div class="col-md-6" style="padding-right: 0">
                                                                <input type="text" ng-disabled="applicationRules[q.id].gender.male!='male'"
                                                                       ng-model="applicationRules[q.id].height.male.inch"
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
                                                                <input type="text" ng-disabled="applicationRules[q.id].gender.female!='female'"
                                                                       ng-model="applicationRules[q.id].height.female.feet" class="form-control"
                                                                       placeholder="Feet">
                                                            </div>
                                                            <div class="col-md-6">
                                                                <input type="text" ng-disabled="applicationRules[q.id].gender.female!='female'"
                                                                       ng-model="applicationRules[q.id].height.female.inch" class="form-control"
                                                                       placeholder="Inch">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </fieldset>
                                        {{--end height validation rules--}}
                                        {{--start weight validation rules--}}
                                        <fieldset>
                                            <legend>Weight</legend>
                                            <div class="row">
                                                <div class="col-sm-6">
                                                    <div class="form-group">
                                                        <label class="control-label">Male</label>
                                                        <input type="text" ng-disabled="applicationRules[q.id].gender.male!='male'"
                                                               ng-model="applicationRules[q.id].weight.male" class="form-control"
                                                               placeholder="Weight in kg">
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="form-group">
                                                        <label class="control-label">Female</label>
                                                        <input type="text" ng-disabled="applicationRules[q.id].gender.female!='female'"
                                                               ng-model="applicationRules[q.id].weight.female" class="form-control"
                                                               placeholder="Weight in kg">
                                                    </div>
                                                </div>
                                            </div>
                                        </fieldset>
                                        {{--end weight validation rules--}}
                                        {{--start chest validation rules--}}
                                        <fieldset>
                                            <legend>Chest</legend>
                                            <div class="row">
                                                <div class="col-sm-6">
                                                    <div class="form-group">
                                                        <label class="control-label">Male</label>
                                                        <div class="row">
                                                            <div class="col-sm-6">
                                                                <input type="text" ng-disabled="applicationRules[q.id].gender.male!='male'"
                                                                       ng-model="applicationRules[q.id].chest.male.min" class="form-control"
                                                                       placeholder="">
                                                            </div>
                                                            <div class="col-sm-6">
                                                                <input type="text" ng-disabled="applicationRules[q.id].gender.male!='male'"
                                                                       ng-model="applicationRules[q.id].chest.male.max" class="form-control"
                                                                       placeholder="">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="form-group">
                                                        <label class="control-label">Female</label>
                                                        <div class="row">
                                                            <div class="col-sm-6">
                                                                <input type="text" ng-disabled="applicationRules[q.id].gender.female!='female'"
                                                                       ng-model="applicationRules[q.id].chest.female.min" class="form-control"
                                                                       placeholder="">
                                                            </div>
                                                            <div class="col-sm-6">
                                                                <input type="text" ng-disabled="applicationRules[q.id].gender.female!='female'"
                                                                       ng-model="applicationRules[q.id].chest.female.max" class="form-control"
                                                                       placeholder="">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </fieldset>
                                        {{--end chest validation rules--}}
                                        <fieldset>
                                            <legend>Education</legend>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label class="control-label">Min education</label>
                                                        <select name="" id=""
                                                                ng-disabled="applicationRules[q.id].gender.male!='male'&&applicationRules[q.id].gender.female!='female'"
                                                                ng-model="applicationRules[q.id].education.min" class="form-control">
                                                            <option value="">--Select a degree--</option>
                                                            <option ng-repeat="(key,value) in minEduList" value="[[key]]">[[value]]
                                                            </option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label class="control-label">Max education</label>
                                                        <select name="" id=""
                                                                ng-disabled="applicationRules[q.id].gender.male!='male'&&applicationRules[q.id].gender.female!='female'"
                                                                ng-model="applicationRules[q.id].education.max"
                                                                class="form-control">
                                                            <option value="">--Select a degree--</option>
                                                            <option ng-repeat="(key,value) in minEduList" value="[[key]]">[[value]]
                                                            </option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </fieldset>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default pull-right" data-dismiss="modal">Close</button>
                    <button type="button" ng-click="onSave('constraint')" class="btn btn-primary pull-left"
                            data-dismiss="modal">Save
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="//cdn.ckeditor.com/4.10.1/full/ckeditor.js"></script>
<script>
    $(document).ready(function () {
        $(".range-app").on('change', function (event) {
            var status = $(this).prop('checked');
            var v = $(this).val();
            if (status) {
                $('*[data-division-id="' + v + '"]').prop('checked', true)
            }
            else {
                $('*[data-division-id="' + v + '"]').prop('checked', false)
            }
        })
        CKEDITOR.replace('terms_and_conditions');
        CKEDITOR.replace('admit_card_message');
    })
    history.pushState(null, null, location.href);
    window.onpopstate = function () {
        history.go(1);
    };
</script>