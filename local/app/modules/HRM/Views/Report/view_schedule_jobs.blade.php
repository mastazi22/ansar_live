@extends('template.master')
@section('title','Schedule Jobs')
@section('breadcrumb')
    {!! Breadcrumbs::render('ansar_scheduled_jobs') !!}
@endsection
@section('content')
    <script>
        GlobalApp.controller("AnsarScheduleJobViewController", function ($scope, $http) {
            $scope.allLoading = false;
            $scope.ansarList = {};
            $scope.param = {};
            $scope.rank = 'all';
            $scope.total = 0;
            $scope.ansarCount = 0;
            $scope.apcCount = 0;
            $scope.pcCount = 0;
            //methods
            $scope.loadPage = function () {
                $scope.allLoading = true;
                $scope.errorFound = 0;
                $scope.errorMessage = "";
                $http({
                    method: 'get',
                    url: '{{URL::route('ansar_scheduled_jobs_report')}}',
                    params: {
                        q: $scope.q,
                        rank: $scope.rank,
                        // gender:'Female'
                        // gender: $scope.param.gender == undefined ? 'all' : $scope.param.gender
                    }
                }).then(function (response) {
                    $scope.ansarList = response.data["list"];
                    $scope.total = response.data["total"];
                    $scope.ansarCount = response.data["ansarTotal"];
                    $scope.apcCount = response.data["apcCount"];
                    $scope.pcCount = response.data["pcCount"];
                    $scope.allLoading = false;
                }, function (response) {
                    $scope.ansarList = {};
                    $scope.errorFound = 1;
                    $scope.errorMessage = "Error occurred!";
                    $scope.allLoading = false;
                })
            };
            $scope.convertDateObj = function (dateStr) {
                return new Date(dateStr);
            };
            $scope.changeRank = function (i) {
                $scope.rank = i;
                $scope.loadPage()
            }
        });
    </script>
    <div ng-controller="AnsarScheduleJobViewController" ng-init="loadPage()" style="position: relative;">
        <section class="content">
            <div class="box box-solid">
                <div class="box-title" style="margin-top: 1%;padding-right: 1%;">
                    <div class="row" style="margin: 0;padding: 0 1%">
                        <!--<filter-template
                                show-item="['gender']"
                                type="all"
                                gender-change="loadPage()"
                                enable-offer-zone="1"
                                on-load="loadPage()"
                                data="param"
                                field-width="{gender:'col-sm-3'}"
                        ></filter-template>-->
                    </div>
                    <div class="row" style="margin: 0;padding: 0 1%">
                        <div class="col-md-8">
                            <h4 class="text text-bold">
                                <a class="btn btn-primary text-bold" href="#" ng-click="changeRank('all')">Total
                                    Ansars ([[total!=undefined?total:0]])</a>&nbsp;
                                <a class="btn btn-primary text-bold" href="#" ng-click="changeRank(3)">PC
                                    ([[pcCount!=undefined?pcCount:0]])</a>
                                <a class="btn btn-primary text-bold" href="#" ng-click="changeRank(2)">APC
                                    ([[apcCount!=undefined?apcCount:0]])</a>&nbsp;
                                <a class="btn btn-primary text-bold" href="#" ng-click="changeRank(1)">Ansar
                                    ([[ansarCount!=undefined?ansarCount:0]])</a>&nbsp;
                            </h4>
                        </div>
                        <div class="col-md-4" style="float: right">
                            <database-search q="q" queue="queue" on-change="loadPage()"></database-search>
                        </div>
                    </div>
                </div>
                <div class="overlay" ng-if="allLoading">
                    <span class="fa">
                        <i class="fa fa-refresh fa-spin"></i> <b>Loading...</b>
                    </span>
                </div>
                <div class="box-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Rank</th>
                                <th>Gender</th>
                                <th>Form Status</th>
                                <th>To Status</th>
                                <th>Active Date</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr ng-repeat="ansar in ansarList">
                                <td>[[ansar.ansar_id]]</td>
                                <td>[[ansar.ansar_name_bng]]</td>
                                <td>[[ansar.name_bng]]</td>
                                <td>[[ansar.sex]]</td>
                                <td>[[ansar.from_status]]</td>
                                <td>[[ansar.to_status]]</td>
                                <td>[[convertDateObj(ansar.activation_date) | date:'mediumDate']]</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
