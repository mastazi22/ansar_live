{{--User: Shreya--}}
{{--Date: 11/05/2015--}}
{{--Time: 11:00 AM--}}

@extends('template.master')
@section('title','List Of Ansar Before Guard Withdraw')
@section('breadcrumb')
    {!! Breadcrumbs::render('ansar_before_withdraw_list') !!}
@endsection

@section('content')
    <script>
        GlobalApp.controller('GuardBeforeWithdrawController', function ($scope, $http, $sce) {
            $scope.isAdmin = parseInt('{{Auth::user()->type}}')
            $scope.ansars="";
            $scope.params = ''
            $scope.allLoading = false;
            $scope.errorFound = 0;
            $scope.errorMessage='';
            $scope.loadAnsar = function () {
                $scope.allLoading = true;
                $http({
                    method: 'get',
                    url: '{{URL::route('load_ansar_before_withdraw')}}',
                    params: {
                        kpi_id: $scope.params.kpi,
                        division_id:$scope.params.range,
                        unit_id:$scope.params.unit,
                        thana_id:$scope.params.thana
                    }
                }).then(function (response) {
                    $scope.errorFound = 0;
                    $scope.ansars = response.data;
                    $scope.allLoading = false;
                },function(response){
                    $scope.errorFound = 1;
                    $scope.ansars = [];
                    $scope.errorMessage = $sce.trustAsHtml("<tr class='warning'><td colspan='"+$('.table').find('tr').find('th').length+"'>"+response.data+"</td></tr>");
                    $scope.allLoading = false;
                })
            }
//            $scope.loadAnsar();
        })

    </script>
    <div ng-controller="GuardBeforeWithdrawController">
        {{--<div class="breadcrumbplace">--}}
            {{--{!! Breadcrumbs::render('ansar_before_withdraw_list') !!}--}}
        {{--</div>--}}
        <section class="content">
            <div class="box box-solid">
                <div class="overlay" ng-if="allLoading">
                    <span class="fa">
                        <i class="fa fa-refresh fa-spin"></i> <b>Loading...</b>
                    </span>
                </div>
                <div class="box-body">
                    <filter-template
                            show-item="['range','unit','thana','kpi']"
                            type="all"
                            range-change="loadAnsar()"
                            unit-change="loadAnsar()"
                            thana-change="loadAnsar()"
                            kpi-change="loadAnsar()"
                            start-load="range"
                            on-load="loadAnsar()"
                            field-width="{range:'col-sm-3',unit:'col-sm-3',thana:'col-sm-3',kpi:'col-sm-3'}"
                            data = "params"
                    >

                    </filter-template>
                    {{--<div class="row">--}}
                        {{--<div class="col-sm-3" ng-show="isAdmin==11||isAdmin==33">--}}
                            {{--<div class="form-group">--}}
                                {{--<label class="control-label">--}}
                                    {{--@lang('title.range')&nbsp;&nbsp;--}}
                                    {{--<img src="{{asset('dist/img/facebook.gif')}}" style="width: 16px;"--}}
                                         {{--ng-show="loadingDiv">--}}
                                {{--</label>--}}
                                {{--<select class="form-control" ng-disabled="loadingDiv||loadingUnit||loadingThana||loadingKpi"--}}
                                        {{--ng-model="selectedDivision"--}}
                                        {{--ng-change="loadDistrict()" name="division_id">--}}
                                    {{--<option value="all">All</option>--}}
                                    {{--<option ng-repeat="d in divisions" value="[[d.id]]">[[d.division_name_bng]]--}}
                                    {{--</option>--}}
                                {{--</select>--}}
                            {{--</div>--}}
                        {{--</div>--}}
                        {{--<div class="col-sm-3" ng-show="isAdmin==11||isAdmin==33||isAdmin==66">--}}
                            {{--<div class="form-group">--}}
                                {{--<label class="control-label">--}}
                                    {{--@lang('title.unit')&nbsp;&nbsp;--}}
                                    {{--<img src="{{asset('dist/img/facebook.gif')}}" style="width: 16px;"--}}
                                         {{--ng-show="loadingUnit">--}}
                                {{--</label>--}}
                                {{--<select class="form-control" ng-disabled="loadingUnit||loadingThana||loadingKpi"--}}
                                        {{--ng-model="selectedDistrict"--}}
                                        {{--ng-change="loadThana(selectedDistrict)" name="unit_id">--}}
                                    {{--<option value="all">All</option>--}}
                                    {{--<option ng-repeat="d in districts" value="[[d.id]]">[[d.unit_name_bng]]--}}
                                    {{--</option>--}}
                                {{--</select>--}}
                            {{--</div>--}}
                        {{--</div>--}}
                        {{--<div class="col-sm-3">--}}
                            {{--<div class="form-group">--}}
                                {{--<label class="control-label">--}}
                                    {{--@lang('title.thana')&nbsp;&nbsp;--}}
                                    {{--<img src="{{asset('dist/img/facebook.gif')}}" style="width: 16px;"--}}
                                         {{--ng-show="loadingThana">--}}
                                {{--</label>--}}
                                {{--<select class="form-control" ng-disabled="loadingDiv||loadingUnit||loadingThana||loadingKpi"--}}
                                        {{--ng-model="selectedThana"--}}
                                        {{--ng-change="loadGuard(selectedThana)" name="thana_id">--}}
                                    {{--<option value="all">All</option>--}}
                                    {{--<option ng-repeat="t in thanas" value="[[t.id]]">[[t.thana_name_bng]]--}}
                                    {{--</option>--}}
                                {{--</select>--}}
                            {{--</div>--}}
                        {{--</div>--}}
                        {{--<div class="col-sm-3">--}}
                            {{--<div class="form-group">--}}
                                {{--<label class="control-label">--}}
                                    {{--@lang('title.kpi')&nbsp;&nbsp;--}}
                                    {{--<img src="{{asset('dist/img/facebook.gif')}}" style="width: 16px;"--}}
                                         {{--ng-show="loadingKpi">--}}
                                {{--</label>--}}
                                {{--<select class="form-control" ng-disabled="loadingDiv||loadingUnit||loadingThana||loadingKpi"--}}
                                        {{--ng-model="selectedKpi"--}}
                                        {{--ng-change="loadAnsar(selectedKpi)">--}}
                                    {{--<option value="all">All</option>--}}
                                    {{--<option ng-repeat="d in guards" value="[[d.id]]">[[d.kpi_name]]--}}
                                    {{--</option>--}}
                                {{--</select>--}}
                            {{--</div>--}}
                        {{--</div>--}}
                    {{--</div>--}}
                    <h4>Total Ansar : [[ansars.length]]</h4>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <tr>
                                <th>Sl No.</th>
                                <th>Ansar ID</th>
                                <th>Ansar Name</th>
                                <th>Rank</th>
                                <th>Own District</th>
                                <th>Own Thana</th>
                                <th>Kpi Name</th>
                                <th>Ansar Reporting Date</th>
                                <th>Ansar Joining Date</th>
                                <th>Withdraw Reason</th>
                                <th>Withdraw Date</th>
                            </tr>
                            <tbody ng-if="errorFound==1" ng-bind-html="errorMessage"></tbody>
                            <tr ng-show="ansars.length==0&&errorFound==0">
                                <td colspan="11" class="warning no-ansar">
                                    No Ansar is available to show
                                </td>
                            </tr>
                            <tr ng-show="ansars.length>0" ng-repeat="a in ansars">
                                <td>
                                    [[$index+1]]
                                </td>
                                <td>
                                    [[a.id]]
                                </td>
                                <td>
                                    [[a.name]]
                                </td>
                                <td>
                                    [[a.rank]]
                                </td>
                                <td>
                                    [[a.unit]]
                                </td>
                                <td>
                                    [[a.thana]]
                                </td>
                                <td>
                                    [[a.kpi_name]]
                                </td>
                                <td>
                                    [[a.r_date|dateformat:'DD-MMM-YYYY']]
                                </td>
                                <td>
                                    [[a.j_date|dateformat:'DD-MMM-YYYY']]
                                </td>
                                <td>
                                    [[a.reason]]
                                </td>
                                <td>
                                    [[a.date|dateformat:'DD-MMM-YYYY']]
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </section>
    </div>

@stop