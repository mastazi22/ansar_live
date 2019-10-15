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
            //methods
            $scope.loadPage = function () {
                $scope.allLoading = true;
                $scope.errorFound = 0;
                $scope.errorMessage = "";
                $http({
                    method: 'get',
                    url: '{{URL::route('ansar_scheduled_jobs_report')}}',
                    params: {
                        q: $scope.q
                        // gender: $scope.param.gender == undefined ? 'all' : $scope.param.gender
                    }
                }).then(function (response) {
                    $scope.ansarList = response.data;
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
        });
    </script>
    <div ng-controller="AnsarScheduleJobViewController" ng-init="loadPage()" style="position: relative;">
        <section class="content">
            <div class="box box-solid">
                <div class="box-title" style="margin-top: 1%;padding-right: 1%;">
                    <div class="row" style="margin: 0;padding: 1%">
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
                    <div class="row">
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
                                <td>[[ansar.personal_info.ansar_name_bng]]</td>
                                <td>[[ansar.personal_info.designation.name_bng]]</td>
                                <td>[[ansar.personal_info.sex]]</td>
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
