@extends('template.master')
@section('title','Schedule Jobs')
@section('breadcrumb')
    {!! Breadcrumbs::render('ansar_scheduled_jobs') !!}
@endsection
@section('content')
    <script>
        GlobalApp.controller("AnsarScheduleJobViewController", function ($scope, $http) {
            $scope.allLoading = false;
            //methods
            $scope.loadPage = function () {
                $scope.allLoading = true;
            };
        });
    </script>
    <div ng-controller="AnsarScheduleJobViewController" style="position: relative;">
        <section class="content">
            <div class="box box-solid">
                <div class="box-title" style="margin-top: 1%;padding-right: 1%;">
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
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
