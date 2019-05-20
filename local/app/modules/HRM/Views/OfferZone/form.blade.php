<script>
    GlobalApp.controller('MarksRulesController', ($scope, $http) => {
                @if(isset($data))

        setTimeout(()=>{
            $scope.$apply(()=>{
                $scope.selectedCircular = '{{$data['job_circular_id']}}'
                $scope.pointFor = '{{$data['point_for']}}'
                $scope.ruleName = '{{$data['rule_name']}}'
                $scope.quota = JSON.parse('{{$data['rules']}}'.replace(/&quot;/g,'"'));
//                console.log($scope.quota)
            });
        })

            @endif
            @if(Session::has('json_error'))
                $scope.errors = JSON.parse('{{Session::get("json_error")}}'.replace(/&quot;/g,'"'))
            @endif
                    @if(Session::has('json_input'))
                $scope.quota = JSON.parse('{{Session::get("json_input")}}'.replace(/&quot;/g,'"'))
                @endif
        const loadConstraint = (id) => {
            return $http({
                url: '/recruitment/circular/constraint/' + id,
                method: 'get'
            })
        }
        const loadQuotaList = (id) => {
            return $http({
                url: '/recruitment/circular/quota_list/' + id,
                method: 'get'
            })
        }
        $scope.constraint = {};
        $scope.quotaList = {};
        $scope.keys = [];
        $scope.$watch('selectedCircular',(n,o)=>{
            if(!n) return;
            Promise.all([
                loadConstraint(n),
                loadQuotaList(n)
            ]).then((result) => {
                try {
                    $scope.constraint = JSON.parse(result[0].data.constraint);
                    $scope.quotaList = result[1].data.quotaList;
                } catch (e) {
                    $scope.constraint = result[0].data.constraint;
                    $scope.quotaList = result[1].data.quotaList;
                }
                setTimeout(()=>{
                    $scope.$apply()
                })
                console.log($scope.constraint)
            }, (error) => {

            })

        })
    })
</script>

<div ng-controller="MarksRulesController">
    @if(isset($data))
        {!! Form::model($data,['route'=>['recruitment.marks_rules.update',$data['id']],'method'=>'patch']) !!}
    @else
        {!! Form::open(['route'=>'recruitment.marks_rules.store']) !!}
    @endif
    <div class="form-group">
        {!! Form::label('job_circular_id','Select Job Circular :',['class'=>'control-label']) !!}
        {!! Form::select('job_circular_id',$circulars,null,['class'=>'form-control','ng-model'=>'selectedCircular','ng-init'=>'selectedCircular="'.Request::old('job_circular_id').'"']) !!}
        @if(isset($errors)&&$errors->first('job_circular_id'))
            <p class="text text-danger">{{$errors->first('job_circular_id')}}</p>
        @endif
    </div>
    <div class="form-group">
        {!! Form::label('point_for','Rules For :',['class'=>'control-label']) !!}
        {!! Form::select('point_for',$rules_for,null,['class'=>'form-control','ng-model'=>'pointFor','ng-change'=>'ruleName=""','ng-init'=>'pointFor="'.Request::old('point_for').'"','ng-disabled'=>'!selectedCircular']) !!}
        @if(isset($errors)&&$errors->first('point_for'))
            <p class="text text-danger">{{$errors->first('point_for')}}</p>
        @endif
    </div>
    <div class="form-group">
        {!! Form::label('rule_name','Rule name :',['class'=>'control-label']) !!}
        {!! Form::select('rule_name',$rules_name,null,['class'=>'form-control','ng-model'=>'ruleName','ng-init'=>'ruleName="'.Request::old('rule_name').'"','ng-disabled'=>'!selectedCircular||!pointFor']) !!}
        @if(isset($errors)&&$errors->first('rule_name'))
            <p class="text text-danger">{{$errors->first('rule_name')}}</p>
        @endif
    </div>
    <div class="panel-group" id="accordion">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a href="#general_applicant" data-toggle="collapse" data-parent="#accordion">
                        Rules For General Applicant
                    </a>
                </h4>
            </div>
            <div id="general_applicant" class="panel-collapse collapse in">
                <div class="panel-body">
                    <div id="experience_rules" class="rules-class" ng-if="ruleName=='experience'">
                        <h4 class="text-center" style="border-bottom: 1px solid #000000">Rule for Experience</h4>
                        <div class="form-group">
                            {!! Form::label('','Min Experience(in years):',['class'=>'control-label']) !!}
                            <div class="row">
                                <div class="col-sm-12">
                                    {!! Form::text('quota[0][min_experience_years]',null,['class'=>'form-control','placeholder'=>'Years']) !!}
                                    @if(isset($errors)&&$errors->first('quota.0.min_experience_years'))
                                        <p class="text text-danger">{{$errors->first('quota.0.min_experience_years')}}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('quota[0][min_exp_point]','Min Point :',['class'=>'control-label']) !!}
                            {!! Form::text('quota[0][min_exp_point]',null,['class'=>'form-control','placeholder'=>'Min Point']) !!}
                            @if(isset($errors)&&$errors->first('quota.0.min_exp_point'))
                                <p class="text text-danger">{{$errors->first('quota.0.min_exp_point')}}</p>
                            @endif
                        </div>
                        <div class="form-group">
                            {!! Form::label('','Max Experience(in years):',['class'=>'control-label']) !!}
                            <div class="row">
                                <div class="col-sm-12">
                                    {!! Form::text('quota[0][max_experience_years]',null,['class'=>'form-control','placeholder'=>'Years']) !!}
                                    @if(isset($errors)&&$errors->first('quota.0.max_experience_years'))
                                        <p class="text text-danger">{{$errors->first('quota.0.max_experience_years')}}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('max_exp_point','Max Point :',['class'=>'control-label']) !!}
                            {!! Form::text('quota[0][max_exp_point]',null,['class'=>'form-control','placeholder'=>'Max Point']) !!}
                            @if(isset($errors)&&$errors->first('quota.0.max_exp_point'))
                                <p class="text text-danger">{{$errors->first('quota.0.max_exp_point')}}</p>
                            @endif
                        </div>
                    </div>
                    <div id="age_rules" class="rules-class" ng-if="ruleName=='age'">
                        <h4 class="text-center" style="border-bottom: 1px solid #000000">Rule for Age</h4>
                        <div class="form-group">
                            {!! Form::label('','Min Age(in years):',['class'=>'control-label']) !!}
                            <div class="row">
                                <div class="col-sm-12">
                                    {!! Form::text('quota[0][min_age_years]','[[constraint[0].age.min]]',['class'=>'form-control','placeholder'=>'Years','readonly'=>'readonly']) !!}
                                    @if(isset($errors)&&$errors->first('quota.0.min_age_years'))
                                        <p class="text text-danger">{{$errors->first('quota.0.min_age_years')}}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('min_age_point','Min Point :',['class'=>'control-label']) !!}
                            {!! Form::text('quota[0][min_age_point]',null,['class'=>'form-control','placeholder'=>'Min Point']) !!}
                            @if(isset($errors)&&$errors->first('quota.0.min_age_point'))
                                <p class="text text-danger">{{$errors->first('quota.0.min_age_point')}}</p>
                            @endif
                        </div>
                        <div class="form-group">
                            {!! Form::label('','Max Age(in years):',['class'=>'control-label']) !!}
                            <div class="row">
                                <div class="col-sm-12">
                                    {!! Form::text('quota[0][max_age_years]','[[constraint[0].age.max]]',['class'=>'form-control','placeholder'=>'Years','readonly'=>'readonly']) !!}
                                    @if(isset($errors)&&$errors->first('quota.0.max_age_years'))
                                        <p class="text text-danger">{{$errors->first('quota.0.max_age_years')}}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('max_age_point','Max Point :',['class'=>'control-label']) !!}
                            {!! Form::text('quota[0][max_age_point]',null,['class'=>'form-control','placeholder'=>'Max Point']) !!}
                            @if(isset($errors)&&$errors->first('quota.0.max_age_point'))
                                <p class="text text-danger">{{$errors->first('quota.0.max_age_point')}}</p>
                            @endif
                        </div>
                        <div class="form-group">
                            <label for="age_general_lower_better">
                                <input type="radio" id="age_general_lower_better" checked style="vertical-align: text-top" name="quota[0][priority]" value="0">&nbsp;
                                Lower is Better
                            </label>&nbsp;&nbsp;
                            <label for="age_general_higher_better">
                                <input type="radio" id="age_general_higher_better" style="vertical-align: text-top" name="quota[0][priority]" value="1">&nbsp;
                                Higher is Better
                            </label>
                        </div>

                    </div>
                    <div id="height_rules" class="rules-class" ng-if="ruleName=='height'">
                        <div class="panel-group" id="general_applicant_gender">
                            {{--height panel--}}
                            <div class="panel panel-default"  ng-if="constraint[0].gender.male">
                                <div class="panel-heading">
                                    <h4 class="panel-title">
                                        <a data-toggle="collapse" data-parent="#general_applicant_gender" href="#general_applicant_male">Male</a>
                                    </h4>
                                </div>
                                <div id="general_applicant_male" class="panel-collapse collapse in">
                                    <div class="panel-body">

                                        <div class="form-group">
                                            {!! Form::label('','Min Height:',['class'=>'control-label']) !!}
                                            <div class="row">
                                                <div class="col-sm-6">
                                                    <div class="input-group">
                                                        {!! Form::text('quota[0][male][min_height_feet]','[[constraint[0].height.male.feet]]',['class'=>'form-control','placeholder'=>'Feet','readonly'=>'readonly']) !!}
                                                        <span class="input-group-addon">Feet</span>
                                                    </div>
                                                    @if(isset($errors)&&$errors->first('quota.0.male.min_height_feet.'))
                                                        <p class="text text-danger">{{$errors->first('quota.0.male..min_height_feet')}}</p>
                                                    @endif
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="input-group">
                                                        {!! Form::text('quota[0][male][min_height_inch]','[[constraint[0].height.male.inch]]',['class'=>'form-control','placeholder'=>'Inch','readonly'=>'readonly']) !!}
                                                        <span class="input-group-addon">Inch</span>
                                                    </div>
                                                    @if(isset($errors)&&$errors->first('quota.0.male.min_height_inch'))
                                                        <p class="text text-danger">{{$errors->first('quota.0.male.min_height_inch')}}</p>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('min_point','Min Point :',['class'=>'control-label']) !!}
                                            {!! Form::text('quota[0][male][min_point]',null,['class'=>'form-control','placeholder'=>'Min Point']) !!}
                                            @if(isset($errors)&&$errors->first('quota.0.male.min_point'))
                                                <p class="text text-danger">{{$errors->first('quota.0.male.min_point')}}</p>
                                            @endif
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('','Max Height:',['class'=>'control-label']) !!}
                                            <div class="row">
                                                <div class="col-sm-6">
                                                    <div class="input-group">
                                                        {!! Form::text('quota[0][male][max_height_feet]',null,['class'=>'form-control','placeholder'=>'Feet']) !!}
                                                        <span class="input-group-addon">Feet</span>
                                                    </div>
                                                    @if(isset($errors)&&$errors->first('quota.0.male.max_height_feet'))
                                                        <p class="text text-danger">{{$errors->first('quota.0.male.max_height_feet')}}</p>
                                                    @endif
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="input-group">
                                                        {!! Form::text('quota[0][male][max_height_inch]',null,['class'=>'form-control','placeholder'=>'Inch']) !!}
                                                        <span class="input-group-addon">Inch</span>
                                                    </div>
                                                    @if(isset($errors)&&$errors->first('quota.0.male.max_height_inch'))
                                                        <p class="text text-danger">{{$errors->first('quota.0.male.max_height_inch')}}</p>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('max_point','Max Point :',['class'=>'control-label']) !!}
                                            {!! Form::text('quota[0][male][max_point]',null,['class'=>'form-control','placeholder'=>'Max Point']) !!}
                                            @if(isset($errors)&&$errors->first('quota.0.male.max_point'))
                                                <p class="text text-danger">{{$errors->first('quota.0.male.max_point')}}</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="panel panel-default"  ng-if="constraint[0].gender.female">
                                <div class="panel-heading">
                                    <h4 class="panel-title">
                                        <a data-toggle="collapse" data-parent="#general_applicant_gender" href="#general_applicant_female">Male</a>
                                    </h4>
                                </div>
                                <div id="general_applicant_female" class="panel-collapse collapse in">
                                    <div class="panel-body">

                                        <div class="form-group">
                                            {!! Form::label('','Min Height:',['class'=>'control-label']) !!}
                                            <div class="row">
                                                <div class="col-sm-6">
                                                    <div class="input-group">
                                                        {!! Form::text('quota[0][female][min_height_feet]','[[constraint[0].height.female.feet]]',['class'=>'form-control','placeholder'=>'Feet','readonly'=>'readonly']) !!}
                                                        <span class="input-group-addon">Feet</span>
                                                    </div>
                                                    @if(isset($errors)&&$errors->first('quota.0.female.min_height_feet'))
                                                        <p class="text text-danger">{{$errors->first('quota.0.female.min_height_feet')}}</p>
                                                    @endif
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="input-group">
                                                        {!! Form::text('quota[0][female][min_height_inch]','[[constraint[0].height.female.inch]]',['class'=>'form-control','placeholder'=>'Inch','readonly'=>'readonly']) !!}
                                                        <span class="input-group-addon">Inch</span>
                                                    </div>
                                                    @if(isset($errors)&&$errors->first('quota.0.female.min_height_inch'))
                                                        <p class="text text-danger">{{$errors->first('quota.0.female.min_height_inch')}}</p>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('min_point','Min Point :',['class'=>'control-label']) !!}
                                            {!! Form::text('quota[0][female][min_point]',null,['class'=>'form-control','placeholder'=>'Min Point']) !!}
                                            @if(isset($errors)&&$errors->first('quota.0.female.min_point'))
                                                <p class="text text-danger">{{$errors->first('quota.0.female.min_point')}}</p>
                                            @endif
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('','Max Height:',['class'=>'control-label']) !!}
                                            <div class="row">
                                                <div class="col-sm-6">
                                                    <div class="input-group">
                                                        {!! Form::text('quota[0][female][max_height_feet]',null,['class'=>'form-control','placeholder'=>'Feet']) !!}
                                                        <span class="input-group-addon">Feet</span>
                                                    </div>
                                                    @if(isset($errors)&&$errors->first('quota.0.female.max_height_feet'))
                                                        <p class="text text-danger">{{$errors->first('quota.0.female.max_height_feet')}}</p>
                                                    @endif
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="input-group">
                                                        {!! Form::text('quota[0][female][max_height_inch]',null,['class'=>'form-control','placeholder'=>'Inch']) !!}
                                                        <span class="input-group-addon">Inch</span>
                                                    </div>
                                                    @if(isset($errors)&&$errors->first('quota.0.female.max_height_inch'))
                                                        <p class="text text-danger">{{$errors->first('quota.0.female.max_height_inch')}}</p>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('max_point','Max Point :',['class'=>'control-label']) !!}
                                            {!! Form::text('quota[0][female][max_point]',null,['class'=>'form-control','placeholder'=>'Max Point']) !!}
                                            @if(isset($errors)&&$errors->first('quota.0.female.max_point.'))
                                                <p class="text text-danger">{{$errors->first('quota.0.female.max_point.')}}</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            {{--height panel--}}
                        </div>
                    </div>
                    <div id="education_rules" class="rules-class" ng-if="ruleName=='education'">
                        <h4 class="text-center" style="border-bottom: 1px solid #000000">Rule for Education</h4>
                        <div class="form-group">

                            <table class="table table-bordered">
                                <tr>
                                    <th>#</th>
                                    <th>Education degree</th>
                                    <th>Priority</th>
                                    <th>Point</th>
                                </tr>
                                <?php $i = 0;$j = 0?>
                                @foreach($educations as $education)
                                    <tr class="edu_c" data-id="{{$education->id}}">
                                        <td>{{++$i}}</td>
                                        <td><strong>{{$education->education_name}}</strong></td>
                                        <td><strong>{{$education->priority}}</strong></td>
                                        <td>
                                            {!! Form::text("quota[0][edu_point][$j][point]",null,['placeholder'=>'point']) !!}
                                            {!! Form::hidden("quota[0][edu_point][$j][priority]",$education->priority) !!}
                                        </td>
                                    </tr>
                                    <?php $j++; ?>
                                @endforeach
                            </table>

                        </div>
                        <div class="form-group">
                            <h4>Choose a option</h4>
                            <div class="radio">
                                <label>{!! Form::radio('quota[0][edu_p_count]',1,isset($data)&&isset($data['edu_p_count'])?intval($data['edu_p_count'])==1:false) !!}
                                    Point count only ascending priority</label>
                            </div>
                            <div class="radio">
                                <label>{!! Form::radio('quota[0][edu_p_count]',2,isset($data)&&isset($data['edu_p_count'])?intval($data['edu_p_count'])==2:false) !!}
                                    Point count only descending priority</label>
                            </div>
                            <div class="radio">
                                <label>{!! Form::radio('quota[0][edu_p_count]',3,isset($data)&&isset($data['edu_p_count'])?intval($data['edu_p_count'])==3:false) !!}
                                    Sum all education point</label>
                            </div>
                        </div>
                    </div>
                    <div id="training_rules" class="rules-class" ng-if="ruleName=='training'">
                        <h4 class="text-center" style="border-bottom: 1px solid #000000">Rule for Training</h4>
                        <div class="form-group">
                            {!! Form::label('training_point','Training Point :',['class'=>'control-label']) !!}
                            {!! Form::text('quota[0][training_point]',null,['class'=>'form-control','placeholder'=>'Training Point']) !!}
                            @if(isset($errors)&&$errors->first('quota.0.training_point]'))
                                <p class="text text-danger">{{$errors->first('quota.0.training_point]')}}</p>
                            @endif
                        </div>
                    </div>
                    <p ng-if="!ruleName">Please select a rule</p>
                </div>
            </div>
        </div>
        <div class="panel panel-default" ng-repeat="q in quotaList">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a href="#[[q.quota_name_eng.split(' ').join('_')]]" data-toggle="collapse" data-parent="#accordion">
                        Rules For [[q.quota_name_bng]] Applicant
                    </a>
                </h4>
            </div>
            <div id="[[q.quota_name_eng.split(' ').join('_')]]" class="panel-collapse collapse">
                <div class="panel-body">
                    <div id="experience_rules" class="rules-class" ng-if="ruleName=='experience'">
                        <h4 class="text-center" style="border-bottom: 1px solid #000000">Rule for Experience</h4>
                        <div class="form-group">
                            {!! Form::label('','Min Experience(in years):',['class'=>'control-label']) !!}
                            <div class="row">
                                <div class="col-sm-12">
                                    {!! Form::text('quota[ [[q.id]] ][min_experience_years]',null,['class'=>'form-control','placeholder'=>'Years']) !!}
                                    @if(isset($errors)&&$errors->first('quota.[[q.id]].min_experience_years'))
                                        <p class="text text-danger">{{$errors->first('quota.[[q.id]].min_experience_years')}}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('min_exp_point','Min Point :',['class'=>'control-label']) !!}
                            {!! Form::text('quota[ [[q.id]] ][min_exp_point]',null,['class'=>'form-control','placeholder'=>'Min Point']) !!}
                            @if(isset($errors)&&$errors->first('quota.[[q.id]].min_exp_point'))
                                <p class="text text-danger">{{$errors->first('quota.[[q.id]].min_exp_point')}}</p>
                            @endif
                        </div>
                        <div class="form-group">
                            {!! Form::label('','Max Experience(in years):',['class'=>'control-label']) !!}
                            <div class="row">
                                <div class="col-sm-12">
                                    {!! Form::text('quota[ [[q.id]] ][max_experience_years]',null,['class'=>'form-control','placeholder'=>'Years']) !!}
                                    @if(isset($errors)&&$errors->first('quota.[[q.id]].max_experience_years'))
                                        <p class="text text-danger">{{$errors->first('quota.[[q.id]].max_experience_years')}}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('max_exp_point','Max Point :',['class'=>'control-label']) !!}
                            {!! Form::text('quota[ [[q.id]] ][max_exp_point]',null,['class'=>'form-control','placeholder'=>'Max Point']) !!}
                            @if(isset($errors)&&$errors->first('quota.[[q.id]].max_exp_point'))
                                <p class="text text-danger">{{$errors->first('quota.[[q.id]].max_exp_point')}}</p>
                            @endif
                        </div>
                    </div>
                    <div id="age_rules" class="rules-class" ng-if="ruleName=='age'">
                        <h4 class="text-center" style="border-bottom: 1px solid #000000">Rule for Age</h4>
                        <div class="form-group">
                            {!! Form::label('','Min Age(in years):',['class'=>'control-label']) !!}
                            <div class="row">
                                <div class="col-sm-12">
                                    {!! Form::text('quota[ [[q.id]] ][min_age_years]','[[constraint[q.id].age.min]]',['class'=>'form-control','placeholder'=>'Years','readonly'=>'readonly']) !!}
                                    <p ng-if="errors&&errors['quota.'+q.id+'.min_age_years']" class="text text-danger">[[errors['quota.'+q.id+'.min_age_years'][0] ]]</p>

                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('min_age_point','Min Point :',['class'=>'control-label']) !!}
                            {!! Form::text('quota[ [[q.id]] ][min_age_point]',Request::old('quota[ [[q.id]] ][min_age_point]'),['class'=>'form-control','placeholder'=>'Min Point','ng-model'=>'quota[q.id].min_age_point']) !!}
                            <p ng-if="errors&&errors['quota.'+q.id+'.min_age_point']" class="text text-danger">[[errors['quota.'+q.id+'.min_age_point'][0] ]]</p>
                        </div>
                        <div class="form-group">
                            {!! Form::label('','Max Age(in years):',['class'=>'control-label']) !!}
                            <div class="row">
                                <div class="col-sm-12">
                                    {!! Form::text('quota[ [[q.id]] ][max_age_years]','[[constraint[q.id].age.max]]',['class'=>'form-control','placeholder'=>'Years','readonly'=>'readonly']) !!}
                                    <p ng-if="errors&&errors['quota.'+q.id+'.max_age_years']" class="text text-danger">[[errors['quota.'+q.id+'.max_age_years'][0] ]]</p>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('max_age_point','Max Point :',['class'=>'control-label']) !!}
                            {!! Form::text('quota[ [[q.id]] ][max_age_point]',Request::old('quota[ [[q.id]] ][max_age_point]'),['class'=>'form-control','placeholder'=>'Max Point','ng-model'=>'quota[q.id].max_age_point']) !!}
                            <p ng-if="errors&&errors['quota.'+q.id+'.max_age_point']" class="text text-danger">[[errors['quota.'+q.id+'.max_age_point'][0] ]]</p>
                        </div>
                        <div class="form-group">
                            <label for="age_general_lower_better">
                                <input type="radio" id="age_general_lower_better" style="vertical-align: text-top" checked name="quota[ [[q.id]] ][priority]" value="0">&nbsp;
                                Lower is Better
                            </label>&nbsp;&nbsp;
                            <label for="age_general_higher_better">
                                <input type="radio" id="age_general_higher_better" style="vertical-align: text-top" name="quota[ [[q.id]] ][priority]" value="1">&nbsp;
                                Higher is Better
                            </label>
                        </div>


                    </div>
                    <div id="height_rules" class="rules-class" ng-if="ruleName=='height'">
                        <h4 class="text-center" style="border-bottom: 1px solid #000000">Rule for Height</h4>
                        <div class="panel-group" id="[[q.quota_name_eng.split(' ').join('_')]]_gender">
                            {{--height panel--}}
                            <div class="panel panel-default"  ng-if="constraint[q.id].gender.male">
                                <div class="panel-heading">
                                    <h4 class="panel-title">
                                        <a data-toggle="collapse" data-parent="#[[q.quota_name_eng.split(' ').join('_')]]_gender" href="#[[q.quota_name_eng.split(' ').join('_')]]_male">Male</a>
                                    </h4>
                                </div>
                                <div id="[[q.quota_name_eng.split(' ').join('_')]]_male" class="panel-collapse collapse in">
                                    <div class="panel-body">

                                        <div class="form-group">
                                            {!! Form::label('','Min Height:',['class'=>'control-label']) !!}
                                            <div class="row">
                                                <div class="col-sm-6">
                                                    <div class="input-group">
                                                        {!! Form::text('quota[ [[q.id]] ][male][min_height_feet]','[[constraint[q.id].height.male.feet]]',['class'=>'form-control','placeholder'=>'Feet','readonly'=>'readonly']) !!}
                                                        <span class="input-group-addon">Feet</span>
                                                    </div>
                                                    <p ng-if="errors&&errors['quota.'+q.id+'.male.min_height_feet']" class="text text-danger">[[errors['quota.'+q.id+'.male.min_height_feet'][0] ]]</p>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="input-group">
                                                        {!! Form::text('quota[ [[q.id]] ][male][min_height_inch]','[[constraint[q.id].height.male.inch]]',['class'=>'form-control','placeholder'=>'Inch','readonly'=>'readonly']) !!}
                                                        <span class="input-group-addon">Inch</span>
                                                    </div>
                                                    <p ng-if="errors&&errors['quota.'+q.id+'.male.min_height_inch']" class="text text-danger">[[errors['quota.'+q.id+'.male.min_height_inch'][0] ]]</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('min_point','Min Point :',['class'=>'control-label']) !!}
                                            {!! Form::text('quota[ [[q.id]] ][male][min_point]',null,['class'=>'form-control','placeholder'=>'Min Point','ng-model'=>'quota[q.id].male.min_point']) !!}
                                            <p ng-if="errors&&errors['quota.'+q.id+'.male.min_point']" class="text text-danger">[[errors['quota.'+q.id+'.male.min_point'][0] ]]</p>
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('','Max Height:',['class'=>'control-label']) !!}
                                            <div class="row">
                                                <div class="col-sm-6">
                                                    <div class="input-group">
                                                        {!! Form::text('quota[ [[q.id]] ][male][max_height_feet]',null,['class'=>'form-control','placeholder'=>'Feet','ng-model'=>'quota[q.id].male.max_height_feet']) !!}
                                                        <span class="input-group-addon">Feet</span>
                                                    </div>
                                                    <p ng-if="errors&&errors['quota.'+q.id+'.male.max_height_feet']" class="text text-danger">[[errors['quota.'+q.id+'.male.max_height_feet'][0] ]]</p>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="input-group">
                                                        {!! Form::text('quota[ [[q.id]] ][male][max_height_inch]',null,['class'=>'form-control','placeholder'=>'Inch','ng-model'=>'quota[q.id].male.max_height_inch']) !!}
                                                        <span class="input-group-addon">Inch</span>
                                                    </div>
                                                    <p ng-if="errors&&errors['quota.'+q.id+'.male.max_height_inch']" class="text text-danger">[[errors['quota.'+q.id+'.male.max_height_inch'][0] ]]</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('max_point','Max Point :',['class'=>'control-label']) !!}
                                            {!! Form::text('quota[ [[q.id]] ][male][max_point]',null,['class'=>'form-control','placeholder'=>'Max Point','ng-model'=>'quota[q.id].male.max_point']) !!}
                                            <p ng-if="errors&&errors['quota.'+q.id+'.male.max_point']" class="text text-danger">[[errors['quota.'+q.id+'.male.max_point'][0] ]]</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="panel panel-default"  ng-if="constraint[q.id].gender.female">
                                <div class="panel-heading">
                                    <h4 class="panel-title">
                                        <a data-toggle="collapse" data-parent="#[[q.quota_name_eng.split(' ').join('_')]]_gender" href="#[[q.quota_name_eng.split(' ').join('_')]]_female">Female</a>
                                    </h4>
                                </div>
                                <div id="[[q.quota_name_eng.split(' ').join('_')]]_female" class="panel-collapse collapse in">
                                    <div class="panel-body">

                                        <div class="form-group">
                                            {!! Form::label('','Min Height:',['class'=>'control-label']) !!}
                                            <div class="row">
                                                <div class="col-sm-6">
                                                    <div class="input-group">
                                                        {!! Form::text('quota[ [[q.id]] ][female][min_height_feet]','[[constraint[q.id].height.female.feet]]',['class'=>'form-control','placeholder'=>'Feet','readonly'=>'readonly']) !!}
                                                        <span class="input-group-addon">Feet</span>
                                                    </div>
                                                    <p ng-if="errors&&errors['quota.'+q.id+'.female.min_height_feet']" class="text text-danger">[[errors['quota.'+q.id+'.female.min_height_feet'][0] ]]</p>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="input-group">
                                                        {!! Form::text('quota[ [[q.id]] ][female][min_height_inch]','[[constraint[q.id].height.female.inch]]',['class'=>'form-control','placeholder'=>'Inch','readonly'=>'readonly']) !!}
                                                        <span class="input-group-addon">Inch</span>
                                                    </div>
                                                    <p ng-if="errors&&errors['quota.'+q.id+'.female.min_height_inch']" class="text text-danger">[[errors['quota.'+q.id+'.female.min_height_inch'][0] ]]</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('min_point','Min Point :',['class'=>'control-label']) !!}
                                            {!! Form::text('quota[ [[q.id]] ][female][min_point]',null,['class'=>'form-control','placeholder'=>'Min Point','ng-model'=>'quota[q.id].female.min_point']) !!}
                                            <p ng-if="errors&&errors['quota.'+q.id+'.female.min_point']" class="text text-danger">[[errors['quota.'+q.id+'.female.min_point'][0] ]]</p>
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('','Max Height:',['class'=>'control-label']) !!}
                                            <div class="row">
                                                <div class="col-sm-6">
                                                    <div class="input-group">
                                                        {!! Form::text('quota[ [[q.id]] ][female][max_height_feet]',null,['class'=>'form-control','placeholder'=>'Feet','ng-model'=>'quota[q.id].female.max_height_feet']) !!}
                                                        <span class="input-group-addon">Feet</span>
                                                    </div>
                                                    <p ng-if="errors&&errors['quota.'+q.id+'.female.max_height_feet']" class="text text-danger">[[errors['quota.'+q.id+'.female.max_height_feet'][0] ]]</p>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="input-group">
                                                        {!! Form::text('quota[ [[q.id]] ][female][max_height_inch]',null,['class'=>'form-control','placeholder'=>'Inch','ng-model'=>'quota[q.id].female.max_height_inch']) !!}
                                                        <span class="input-group-addon">Inch</span>
                                                    </div>
                                                    <p ng-if="errors&&errors['quota.'+q.id+'.female.max_height_inch']" class="text text-danger">[[errors['quota.'+q.id+'.female.max_height_inch'][0] ]]</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('max_point','Max Point :',['class'=>'control-label']) !!}
                                            {!! Form::text('quota[ [[q.id]] ][female][max_point]',null,['class'=>'form-control','placeholder'=>'Max Point','ng-model'=>'quota[q.id].female.max_point']) !!}
                                            <p ng-if="errors&&errors['quota.'+q.id+'.female.max_point']" class="text text-danger">[[errors['quota.'+q.id+'.female.max_point'][0] ]]</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            {{--height panel--}}
                        </div>
                    </div>
                    <div id="education_rules" class="rules-class" ng-if="ruleName=='education'">
                        <h4 class="text-center" style="border-bottom: 1px solid #000000">Rule for Education</h4>
                        <div class="form-group">

                            <table class="table table-bordered">
                                <tr>
                                    <th>#</th>
                                    <th>Education degree</th>
                                    <th>Priority</th>
                                    <th>Point</th>
                                </tr>
                                <?php $i = 0;$j = 0?>
                                @foreach($educations as $education)
                                    <tr class="edu_c" data-id="{{$education->id}}">
                                        <td>{{++$i}}</td>
                                        <td><strong>{{$education->education_name}}</strong></td>
                                        <td><strong>{{$education->priority}}</strong></td>
                                        <td>
                                            {!! Form::text("quota[ [[q.id]] ][edu_point][$j][point]",null,['placeholder'=>'point']) !!}
                                            {!! Form::hidden("quota[ [[q.id]] ][edu_point][$j][priority]",$education->priority) !!}
                                        </td>
                                    </tr>
                                    <?php $j++; ?>
                                @endforeach
                            </table>

                        </div>
                        <div class="form-group">
                            <h4>Choose a option</h4>
                            <div class="radio">
                                <label>{!! Form::radio('quota[ [[q.id]] ][edu_p_count]',1,isset($data)&&isset($data['edu_p_count'])?intval($data['edu_p_count'])==1:false) !!}
                                    Point count only ascending priority</label>
                            </div>
                            <div class="radio">
                                <label>{!! Form::radio('quota[ [[q.id]] ][edu_p_count]',2,isset($data)&&isset($data['edu_p_count'])?intval($data['edu_p_count'])==2:false) !!}
                                    Point count only descending priority</label>
                            </div>
                            <div class="radio">
                                <label>{!! Form::radio('quota[ [[q.id]] ][edu_p_count]',3,isset($data)&&isset($data['edu_p_count'])?intval($data['edu_p_count'])==3:false) !!}
                                    Sum all education point</label>
                            </div>
                        </div>
                    </div>
                    <div id="training_rules" class="rules-class" ng-if="ruleName=='training'">
                        <h4 class="text-center" style="border-bottom: 1px solid #000000">Rule for Training</h4>
                        <div class="form-group">
                            {!! Form::label('training_point','Training Point :',['class'=>'control-label']) !!}
                            {!! Form::text('quota[ [[q.id]] ][training_point]',null,['class'=>'form-control','placeholder'=>'Training Point']) !!}
                            @if(isset($errors)&&$errors->first('quota.[[q.id]].training_point'))
                                <p class="text text-danger">{{$errors->first('quota.[[q.id]].training_point')}}</p>
                            @endif
                        </div>
                    </div>
                    <p ng-if="!ruleName">Please select a rule</p>
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
{{--
<script>
    $(document).ready(function () {
        var constraint;

        function initCheck() {
            var v = $("select[name='rule_name']").val();
            var cid = $("select[name='job_circular_id']").val();
            if (cid) {
                loadConstraint(cid);
            }
            if (!v) return;
            var id = `#${v}_rules`;
            $(id).show();
        }

        $("select[name='rule_name']").on('change', function (evt) {
            var v = $(this).val();
            $(".rules-class").hide();
            if (!v) {
                return;
            }
            var id = `#${v}_rules`;
            $(id).show();
            modifyRule();
        })
        $("select[name='job_circular_id']").on('change', function (evt) {
            var v = $(this).val();
            loadConstraint(v);
        })

        function loadConstraint(id) {
            $.ajax({
                url: '{{URL::to("/recruitment/circular/constraint")}}/' + id,
                type: 'get',
                success: function (response) {
                    try {
                        constraint = JSON.parse(response).constraint
                    } catch (exp) {
                        constraint = response.constraint;
                    }
                    modifyRule();
                    console.log(constraint)
                },
                error: function (res) {
                    console.log(res)
                }
            })
        }

        function modifyRule() {
            var t = $(".edu_c");
            var e = constraint.education;
            t.each(function (obj) {
                var a = +$(this).attr("data-id");
                if (a < +e.min || a > +e.max) {
                    $(this).remove();
                }
            })
        }

        initCheck();
    })
</script>--}}
