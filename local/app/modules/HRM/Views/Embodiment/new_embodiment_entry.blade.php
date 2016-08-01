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
        $(document).ready(function () {
            $('#reporting_date').datePicker(false);
            $("#joining_date").datePicker(false);
            $('#r_date').datePicker(true);
            $("#j_date").datePicker(true);
        })
        var myApp = angular.module('myApp', []);
        GlobalApp.controller('NewEmbodimentController', function ($scope, $http, $sce) {
            $scope.isAdmin = parseInt('{{Auth::user()->type}}');
            $scope.ansarId = "";
            $scope.selectedUnit = "";
            $scope.selectedThana = "";
            $scope.selectedKpi = "";
            $scope.ansarDetail = {};
            $scope.units = [];
            $scope.thanas = [];
            $scope.totalLength = 0;
            $scope.ansar_ids = [];
            $scope.kpis = [];
            $scope.loadingUnit = false;
            $scope.loadingThana = false;
            $scope.loadingKpi = false;
            $scope.loadingDetail = false;
            $scope.loadingAnsar = false;
            $scope.joining_date = "";
            $scope.isAnsarAvailable = false;
            $scope.hh = 0;
            var j_date = "";
            var r_date = "";
            var rd = new Date();
//            if ($scope.isAdmin == 22) {
//                $scope.reporting_date = moment().format("D-MMM-YYYY");
//                $scope.joining_date = moment().format("D-MMM-YYYY");
//            }
//            $scope.reporting_date = rd.getFullYear() + "-" + (rd.getMonth() + 1) + "-" + (rd.getDate());
            $scope.msg = "";

            $scope.dcDistrict = parseInt('{{Auth::user()->district_id}}')

            $scope.loadDistrict = function () {
                $scope.loadingUnit = true;
                $http({
                    method: 'get',
                    url: '{{URL::to('HRM/DistrictName')}}',
                }).then(function (response) {
                    $scope.units = response.data;
                    $scope.loadingUnit = false;
                })
            }
            $scope.loadThana = function (d_id) {
                $scope.loadingThana = true;
                $scope.kpis = [];
                $http({
                    method: 'get',
                    url: '{{URL::to('HRM/ThanaName')}}',
                    params: {id: d_id}
                }).then(function (response) {
                    $scope.thanas = response.data;
                    $scope.selectedThana = "";
                    $scope.loadingThana = false;
                    $scope.selectedThana = "{{Request::old('thana_name_eng')}}";
                })
            }
            $scope.$watch('selectedUnit', function(n, o){
                $scope.loadThana(n);
            })
            $scope.$watch('selectedThana', function(n, o){
                $scope.loadKpi(n);
            })
            $scope.loadAnsarDetail = function (id) {
                $scope.loadingAnsar = true;
                $http({
                    method: 'get',
                    url: '{{URL::route('check-ansar')}}',
                    params: {ansar_id: id}
                }).then(function (response) {
                    $scope.ansarDetail = response.data
                    $scope.loadingAnsar = false;
                    console.log($scope.ansarDetail)
                    $scope.totalLength--;
                    $scope.loadingAnsar = false;
                })
            }
            $scope.$watch('ansarId', function(n, o){
                $scope.loadAnsarDetail(n);
            })
            $scope.makeQueue = function (id) {
                $scope.ansar_ids.push(id);
                $scope.totalLength += 1;
            }
            $scope.$watch('totalLength', function (n, o) {
                if (!$scope.loadingAnsar && n > 0) {
                    $scope.loadAnsarDetail($scope.ansar_ids.shift())
                }
                else {
                    if (!$scope.ansarId)$scope.ansarDetail = {}
                }
            })

            $scope.loadKpi = function (t_id) {
                $scope.loadingKpi = true;
                $http({
                    method: 'get',
                    url: '{{URL::route('kpi_name')}}',
                    params: {id: t_id}
                }).then(function (response) {
                    $scope.kpis = response.data
                    $scope.selectedKpi = "";
                    $scope.loadingKpi = false;
                    $scope.selectedKpi = "{{Request::old('kpi_id')}}";
                })
            }
            $scope.verifyMemorandumId = function () {
                var data = {
                    memorandum_id: $scope.memorandumId
                }
                $scope.isVerified = false;
                $scope.isVerifying = true;
                $http.post('{{action('UserController@verifyMemorandumId')}}', data).then(function (response) {
//                    alert(response.data.status)
                    $scope.isVerified = response.data.status;
                    $scope.isVerifying = false;
                }, function (response) {

                })
            }
//            $scope.dateCheck = function () {
//                j_date = new Date($scope.joining_date);
//                r_date = new Date();
//                if (j_date <= r_date && $scope.hh == 1) {
//                    $scope.msg = "Joining date must be greater than Reporting date"
//                } else {
//                    $scope.msg = "";
//                }
//
//            }
            $scope.dateConvert=function(date){
                return (moment(date).format('DD-MMM-Y'));
            }
            $scope.loadDistrict();
        })
        GlobalApp.directive('checkKpi', function ($http) {
            return{
                restrict:'AC',
                link: function (scope,elem,attrs) {
                    if(scope.ansarDetail.apd==undefined){
                        $(elem).on('change', function (e) {
                            var v = $(this).val()
//                            alert(v)
                            scope.loadingKpi = true;
                            $http({
                                method:'get',
                                params:{id:v,ansar_id:scope.ansarDetail.apd.id},
                                url:"{{URL::route('kpi_detail')}}"
                            }).then(function (response) {
                                console.log(response.data);
                                scope.loadingKpi = false;
                                switch (scope.ansarDetail.apd.id){
                                    case 1:
                                            if(response.data.detail.no_of_ansar<response.data.ansar_count.total+1){
                                                scope.isAnsarAvailable = false;
                                                $("body").notifyDialog({type:'error',message:'You can`t embodied this ansar(Rank: Ansar) in this kpi.Because total number of ansar in this kpi already exceed. First transfer or disembodied ansar from this kpi.'}).showDialog()
                                            }
                                            else{
                                                scope.isAnsarAvailable = true;
                                            }
                                        break;
                                    case 2:
                                        if(response.data.detail.no_of_apc<response.data.ansar_count.total+1){
                                            scope.isAnsarAvailable = false;
                                            $("body").notifyDialog({type:'error',message:'You can`t embodied this ansar(Rank: APC) in this kpi.Because total number of APC in this kpi already exceed. First transfer or disembodied APC from this kpi.'}).showDialog()
                                        }
                                        else{
                                            scope.isAnsarAvailable = true;
                                        }
                                        break;
                                    case 3:
                                        if(response.data.detail.no_of_pc<response.data.ansar_count.total+1){
                                            scope.isAnsarAvailable = false;
                                            $("body").notifyDialog({type:'error',message:'You can`t embodied this ansar(Rank: PC) in this kpi.Because total number of PC in this kpi already exceed. First transfer or disembodied PC from this kpi.'}).showDialog()
                                        }
                                        else{
                                            scope.isAnsarAvailable = true;
                                        }
                                        break;
                                }
                            }, function (response) {
                                scope.loadingKpi = false;
                            })
                        })
                    }

                }
            }
        })
    </script>
    <div ng-controller="NewEmbodimentController" ng-app>
        {{--<div class="breadcrumbplace">--}}
            {{--{!! Breadcrumbs::render('embodiment_entry') !!}--}}
        {{--</div>--}}
        @if(Session::has('success_message'))
            <div style="padding: 10px 20px 0 20px;">
                <div class="alert alert-success">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                    <span class="glyphicon glyphicon-ok"></span> {{Session::get('success_message')}}
                </div>
            </div>
        @endif
        <section class="content" style="position: relative;">

            <notify></notify>
            <div class="box box-solid">
                <div class="box-body">
                    {!! Form::open(array('route' => 'new-embodiment-entry', 'name' => 'newEmbodimentForm', 'novalidate')) !!}
                    <div class="row">
                        <div class="col-sm-4">
                            <div class="form-group required" ng-init="ansarId='{{Request::old('ansar_id')}}'">
                                <label for="ansar_id" class="control-label">Ansar ID</label>
                                <input type="text" name="ansar_id" id="ansar_id" class="form-control"
                                       placeholder="Enter Ansar ID" ng-model="ansarId"
                                       ng-change="makeQueue(ansarId)">
                                @if($errors->has('ansar_id'))
                                    <p class="text-danger">{{$errors->first('ansar_id')}}</p>
                                @endif
                            </div>
                            <div class="form-group required" ng-init="memorandumId='{{Request::old('memorandum_id')}}'">
                                <label class="control-label">Memorandum no.&nbsp;&nbsp;&nbsp;<span
                                            ng-show="isVerifying"><i
                                                class="fa fa-spinner fa-pulse"></i>&nbsp;Verifying</span><span
                                            class="text-danger"
                                            ng-if="isVerified&&!memorandumId">Memorandum ID is required.</span><span
                                            class="text-danger"
                                            ng-if="isVerified&&memorandumId">This id already taken.</span></label>
                                <input ng-blur="verifyMemorandumId()" ng-model="memorandumId"
                                       type="text" class="form-control" name="memorandum_id"
                                       placeholder="Enter Memorandum no." required>
                                @if($errors->has('memorandum_id'))
                                    <p class="text-danger">{{$errors->first('memorandum_id')}}</p>
                                @endif
                            </div>
                            <div ng-show="isAdmin!=22">
                                <div class="form-group required" ng-init="reporting_date='{{Request::old('reporting_date')}}'">
                                    <label for="reporting_date" class="control-label">Reporting Date</label>
                                    {!! Form::text('reporting_date', $value = Request::old('reporting_date'), $attributes = array('class' => 'form-control', 'id' => 'reporting_date', 'ng-model' => 'reporting_date', 'required')) !!}
                                    @if($errors->has('reporting_date'))
                                        <p class="text-danger">{{$errors->first('reporting_date')}}</p>
                                    @endif
                                </div>
                                <div class="form-group required" ng-init="joining_date='{{Request::old('joining_date')}}'">
                                    <label for="joining_date" class="control-label">Joining Date</label>
                                    {!! Form::text('joining_date', $value = Request::old('joining_date'), $attributes = array('class' => 'form-control', 'id' => 'joining_date', 'ng-model' => 'joining_date','required')) !!}
                                    @if($errors->has('joining_date'))
                                        <p class="text-danger">{{$errors->first('joining_date')}}</p>
                                    @endif
                                </div>
                            </div>
                            <!---->
                            <div ng-show="isAdmin==22">
                                <div class="form-group">
                                    <label for="r_date" class="control-label">Reporting Date</label>
                                    {!! Form::text('r_date', $value = null, $attributes = array('class' => 'form-control', 'id' => 'r_date', 'disabled')) !!}
                                </div>
                                <div class="form-group">
                                    <label for="j_date" class="control-label">Joining Date</label>
                                    {!! Form::text('j_date', $value = null, $attributes = array('class' => 'form-control', 'id' => 'j_date','disabled')) !!}
                                </div>
                            </div>
                            <!---->
                            <div class="form-group required" ng-init="selectedUnit='{{Request::old('division_name_eng')}}'">
                                <label for="e_unit" class="control-label">Select a Unit&nbsp;
                                    <img ng-show="loadingUnit" src="{{asset('dist/img/facebook.gif')}}"
                                         width="16"></label>
                                <select name="division_name_eng" ng-disabled="loadingUnit||ansarDetail.apd==undefined" id="e_unit"
                                        class="form-control"
                                        ng-model="selectedUnit" required>
                                    <option value="">--Select a Unit--</option>
                                    <option ng-repeat="u in units"
                                            ng-class="{'bg-danger':u.id==ansarDetail.unit_id}"
                                            ng-disabled="u.id==ansarDetail.unit_id" value="[[u.id]]">
                                        [[u.unit_name_bng]]
                                    </option>
                                </select>
                                @if($errors->has('division_name_eng'))
                                    <p class="text-danger">{{$errors->first('division_name_eng')}}</p>
                                @endif
                            </div>
                            <div class="form-group required" ng-init="selectedThana='{{Request::old('thana_name_eng')}}'">
                                <label for="e_thana" class="control-label">Select a Thana&nbsp;
                                    <img ng-show="loadingThana" src="{{asset('dist/img/facebook.gif')}}"
                                         width="16"></label>
                                <select name="thana_name_eng" ng-disabled="loadingThana||ansarDetail.apd==undefined" id="e_thana"
                                        class="form-control"
                                        ng-model="selectedThana" required>
                                    <option value="">--Select a Thana--</option>
                                    <option ng-repeat="t in thanas" value="[[t.id]]" ng-selected="t.id=='{{Request::old('thana_name_eng')}}'">[[t.thana_name_bng]]
                                    </option>
                                </select>
                                @if($errors->has('thana_name_eng'))
                                    <p class="text-danger">{{$errors->first('thana_name_eng')}}</p>
                                @endif
                            </div>
                            <div class="form-group required" ng-init="selectedKpi='{{Request::old('kpi_id')}}'">
                                <label for="e_kpi" class="control-label">Select a KPI&nbsp;
                                    <img ng-show="loadingKpi" src="{{asset('dist/img/facebook.gif')}}"
                                         width="16"></label>
                                <select name="kpi_id" check-kpi="[[selectedKpi]]" ng-disabled="loadingKpi||ansarDetail.apd==undefined" id="e_kpi" class="form-control"
                                        ng-model="selectedKpi" required>
                                    <option value="">--Select a KPI--</option>
                                    <option ng-repeat="k in kpis" value="[[k.id]]">[[k.kpi_name]]</option>
                                </select>
                                @if($errors->has('kpi_id'))
                                    <p class="text-danger">{{$errors->first('kpi_id')}}</p>
                                @endif
                            </div>
                            <button class="btn btn-primary">
                                Embodied
                            </button>
                        </div>
                        <div class="col-sm-8">
                            <div id="loading-box" ng-if="loadingAnsar">
                            </div>
                            <div ng-if="!ansarDetail.aoi.ansar_id">
                                <h3 style="text-align: center">No Ansar Found</h3>
                            </div>
                            <div ng-if="ansarDetail.aoi.ansar_id">
                                <div class="form-group">
                                    <div class="col-sm-8 col-sm-offset-2">
                                        <div class="table-responsive">
                                            <table class="table table-bordered">
                                                <tr>
                                                    <td rowspan="4"
                                                        style="vertical-align: middle;width: 130px;height: 150px">
                                                        <img style="width: 120px;height: 150px" src="{{URL::to('image').'?file='}}[[ansarDetail.apd.profile_pic]]" alt="">
                                                    </td>
                                                    <th>Name</th>
                                                    <td>[[ansarDetail.apd.ansar_name_bng]]</td>
                                                </tr>
                                                <tr>
                                                    <th>Rank</th>
                                                    <td>[[ansarDetail.apd.name_bng]]</td>
                                                </tr>
                                                <tr>
                                                    <th>Mobile No.</th>
                                                    <td>[[ansarDetail.apd.mobile_no_self]]</td>
                                                </tr>
                                                <tr>
                                                    <th>Home District</th>
                                                    <td>[[ansarDetail.apd.unit_name_bng]]</td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-sm-12">
                                        <div class="table-responsive">
                                            <table class="table table-bordered">
                                                <caption>প্যানেলভুক্তির  ও অফারের বিবরণ</caption>
                                                <tr>
                                                    <td>প্যানেলভুক্তির তারিখ</td>
                                                    <td>প্যানেল আইডি নং</td>
                                                    <td>বর্তমান অবস্থা</td>
                                                    <td>অফারের তারিখ</td>
                                                    <td>অফারের জেলা</td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        [[ansarDetail.api.panel_date?dateConvert(ansarDetail.api.panel_date):"N/A"]]
                                                    </td>
                                                    <td>
                                                        [[ansarDetail.api.memorandum_id?ansarDetail.api.memorandum_id:"N/A"]]
                                                    </td>
                                                    <td ng-if="1==ansarDetail.asi.block_list_status">Blocked
                                                    </td>
                                                    <td ng-if="0==ansarDetail.asi.block_list_status">
                                                        <span ng-if="1==ansarDetail.asi.free_status">Free</span>
                                                        <span ng-if="1==ansarDetail.asi.pannel_status">Panel</span>
                                                        <span ng-if="1==ansarDetail.asi.offer_sms_status">Offered</span>
                                                        <span ng-if="1==ansarDetail.asi.embodied_status">Embodied</span>
                                                        <span ng-if="1==ansarDetail.asi.freezing_status">Freeze</span>
                                                        <span ng-if="1==ansarDetail.asi.early_retierment_statBlockedus">Early retirement</span>
                                                        <span ng-if="1==ansarDetail.asi.block_list_status"></span>
                                                        <span ng-if="1==ansarDetail.asi.black_list_status">Blacked</span>
                                                        <span ng-if="1==ansarDetail.asi.rest_status">Rest</span>
                                                        <span ng-if="1==ansarDetail.asi.retierment_status">Retirement</span>
                                                    </td>
                                                    <td>
                                                        [[ansarDetail.aoi.offerDate?dateConvert(ansarDetail.aoi.offerDate):'N/A']]
                                                    </td>
                                                    <td>
                                                        [[ansarDetail.aoi.offered_district?ansarDetail.aoi.offered_district:'N/A']]
                                                    </td>
                                                    {{--<td>[[ansarDetail.aoci.offerCancel?ansarDetail.aoci.offerCancel:'N\A']]</td>--}}
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    {!! Form::close() !!}
                </div>
            </div>
        </section>
    </div>
@stop