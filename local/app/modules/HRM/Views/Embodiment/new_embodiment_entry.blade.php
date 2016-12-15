{{--User: Shreya--}}
{{--Date: 11/8/2015--}}
{{--Time: 11:48 AM--}}

@extends('template.master')
@section('title','Embodiment')
@section('breadcrumb')
    {!! Breadcrumbs::render('embodiment_entry') !!}
    @endsection
@section('content')
    <script>
        GlobalApp.controller('NewEmbodimentController', function ($scope, $http, $timeout) {
            $scope.ansarId = "";
            $scope.errors = ''
            $scope.queue = []
            $scope.ee = true;
            $scope.ansarDetail = {};
            $scope.units = [];
            $scope.thanas = [];
            $scope.totalLength = 0;
            $scope.ansar_ids = [];
            $scope.loadingKpi = false;
            $scope.loadingDetail = false;
            $scope.loadingAnsar = false;
            $scope.joining_date = "";
            $scope.isAnsarAvailable = false;
            $scope.ea = [];
            $scope.hh = 0;
            var j_date = "";
            var r_date = "";
            var rd = new Date();
            $scope.msg = "";
            $scope.$watch('selected', function (n,o) {
                if(n){
                    var l = 0;
                    n.forEach(function (value, index) {
                        if(value!==false){
                            l++
                        }
                    })
                    if(l>0) $scope.ee = false
                    else $scope.ee = true;
                    if(n.length==l&& n.length==0){
                        $scope.selectAll = true
                    }
                    else $scope.selectAll = false
                }
            },true)
            $scope.changeAll = function () {

                if($scope.selectAll){
                    $scope.ansarDetail.forEach(function (value,index) {

                        $scope.selected[index] = value.ansar_id;

                    })
                }
                else{
                    $scope.selected = Array.apply(null,Array($scope.ansarDetail.length)).map(Boolean.prototype.valueOf,false);
                }

            }
            $scope.pppp = function (value) {
                return value!==false;
            }
            $scope.loadAnsarDetail = function (id) {
                $("#embodied-modal").modal('hide')
                $scope.loadingAnsar = true;
                $http({
                    method: 'get',
                    url: '{{URL::route('check-ansar')}}',
                    params: {ansar_id: $scope.q,unit: $scope.params.unit}
                }).then(function (response) {
                    $scope.queue.shift();
                    if($scope.queue.length>1) $scope.loadAnsarDetail();
                    $scope.ansarDetail = response.data.apd?response.data.apd:[]
                    $scope.auid = $scope.ansarDetail.length>0?angular.copy($scope.ansarDetail[0]):$scope.auid
                    $scope.selected = Array.apply(null,Array($scope.ansarDetail.length)).map(Boolean.prototype.valueOf,false)
                    $scope.loadingAnsar = false;
                    console.log($scope.ansarDetail)
                    $scope.totalLength--;
                    $scope.loadingAnsar = false;


                }, function () {
                    $scope.loadingAnsar = false;
                })
            }
        })
    </script>
    <div ng-controller="NewEmbodimentController">
        <section class="content" style="position: relative;">
            <div class="box box-solid">
                <div class="overlay" ng-if="loadingAnsar">
                    <span class="fa">
                        <i class="fa fa-refresh fa-spin"></i> <b>Loading...</b>
                    </span>
                </div>
                <div class="box-body">

                    <filter-template
                            show-item="['range','unit']"
                            type="single"
                            data="params"
                            start-load="range"
                            unit-change = "loadAnsarDetail()"
                            on-load="loadAnsarDetail()"
                            field-width="{range:'col-sm-4',unit:'col-sm-4'}"
                             >
                        </filter-template>
                    <div class="table-responsive">
                        <table class="table table-bordered table-stripped">
                            <caption>
                                <database-search q="q" on-change="loadAnsarDetail()" queue="queue"></database-search>
                            </caption>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Rank</th>
                                <th>Home District</th>
                                <th>প্যানেলভুক্তির তারিখ</th>
                                <th>প্যানেল আইডি নং</th>
                                <th>বর্তমান অবস্থা</th>
                                <th>অফারের তারিখ</th>
                                <th>
                                    <input type="checkbox" ng-model="selectAll" ng-change="changeAll()" ng-disabled="ansarDetail.length<=0">
                                </th>
                            </tr>
                            <tr ng-repeat="ansar in ansarDetail">
                                <td>[[$index+1]]</td>
                                <td>[[ansar.ansar_name_bng]]</td>
                                <td>[[ansar.name_bng]]</td>
                                <td>[[ansar.home_district]]</td>
                                <td>[[ansar.panel_date|dateformat:"DD-MMM-YYYY"]]</td>
                                <td>[[ansar.memorandum_id]]</td>
                                <td>Offered</td>
                                <td>[[ansar.offerDate|dateformat:"DD-MMM-YYYY"]]</td>
                                <td>
                                    <input type="checkbox" ng-model="selected[$index]" ng-false-value="false" ng-true-value="[[ansar.ansar_id]]">
                                </td>
                            </tr>
                            <tr ng-if="ansarDetail==undefined||ansarDetail.length<=0">
                                <td class="warning" colspan="9">No Ansar available</td>
                            </tr>
                        </table>
                    </div>

                    {!! Form::open(['route'=>'print_letter','target'=>'_blank','ng-show'=>'status','class'=>'pull-left']) !!}
                    {!! Form::hidden('option','memorandumNo') !!}
                    {!! Form::hidden('id','[[memorandumId]]') !!}
                    {!! Form::hidden('type','EMBODIMENT') !!}
                    @if(auth()->user()->type!=22)
                        {!! Form::hidden('unit','[[ params.unit?params.unit:auid.ouid ]]') !!}
                    @else
                        {!! Form::hidden('unit',auth()->user()->district?auth()->user()->district->id:'') !!}
                    @endif
                    <button class="btn btn-primary"><i class="fa fa-print"></i>&nbsp;Print Embodied Letter</button>
                    {!! Form::close() !!}
                    <a href="#" class="btn btn-primary pull-right" ng-disabled="ee" data-target="#embodied-modal" data-toggle="modal">
                        Embodied
                    </a>
                </div>
                <div id="embodied-modal" class="modal fade" role="dialog">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            {!! Form::open(array('route' => 'new-embodiment-entry', 'name' => 'newEmbodimentForm', 'novalidate','form-submit','errors','loading','status','on-reset'=>'loadAnsarDetail()')) !!}
                            <div class="modal-header">
                                <h4 class="modal-title">Embodiment Form</h4>
                            </div>
                            <div class="modal-body">
                                <input type="hidden" name="ansar_ids[]" ng-repeat="s in selected|filter:pppp" value="[[s]]">
                                <div class="form-group required">
                                    <label class="control-label">Memorandum no. & Date</label>

                                    <div class="row">
                                        <div class="col-md-7" style="padding-right: 0">
                                            <input ng-model="memorandumId"
                                                   type="text" class="form-control" name="memorandum_id"
                                                   placeholder="Enter Memorandum no." required>
                                        </div>
                                        <div class="col-md-5">
                                            <input date-picker ng-model="memDate"
                                                   type="text" class="form-control" name="mem_date"
                                                   placeholder="Memorandum Date" required>
                                        </div>

                                    </div>
                                    <p class="text-danger" ng-if="errors.memorandum_id!=undefined">[[errors.memorandum_id[0] ]]</p>
                                </div>
                                <div class="form-group required">
                                    <label for="reporting_date" class="control-label">Reporting Date</label>
                                    {!! Form::text('reporting_date', null, $attributes = array('class' => 'form-control', 'id' => 'reporting_date', 'ng-model' => 'reporting_date','date-picker', 'required')) !!}
                                    <p class="text-danger" ng-if="errors.reporting_date!=undefined">[[errors.reporting_date[0] ]]</p>
                                </div>
                                <div class="form-group required">
                                    <label for="joining_date" class="control-label">Embodiment Date</label>
                                    {!! Form::text('joining_date', null, $attributes = array('class' => 'form-control', 'id' => 'joining_date','date-picker', 'ng-model' => 'joining_date','required')) !!}
                                    <p class="text-danger" ng-if="errors.joining_date!=undefined">[[errors.joining_date[0] ]]</p>
                                </div>
                                <!---->
                                <!---->
                                <filter-template
                                        show-item="['unit','thana','kpi']"
                                        type="single"
                                        data="param"
                                        start-load="unit"
                                        layout-vertical="1"
                                        field-name="{unit:'division_name_eng',thana:'thana_name_eng',kpi:'kpi_id'}"
                                        error-key="{unit:'division_name_eng',thana:'thana_name_eng',kpi:'kpi_id'}"
                                        error-message="{division_name_eng:errors.division_name_eng[0],thana_name_eng:errors.thana_name_eng[0],kpi_id:errors.kpi_id[0]}"
                                >

                                </filter-template>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-primary pull-right" ng-disabled="loading">
                                    <i class="fa fa-spinner fa-pulse" ng-show="loading"></i>Embodied
                                </button>
                            </div>
                            {!! Form::close() !!}
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@stop