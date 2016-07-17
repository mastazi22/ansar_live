@extends('template.master')
        @section('title','Dashboard')
        @section('small_title','Human Resource Management')
        @section('breadcrumb')
        {!! Breadcrumbs::render('hrm') !!}
        @endsection
@section('content')
        <!-- Content Wrapper. Contains page content -->
<script>


    GlobalApp.controller('TotalAnsar', function ($http, $scope) {

        $scope.allAnsar = [];
        $scope.loadingAnsar = true;
        $scope.embodimentData = {};
        $scope.graphData = [];
        $scope.loadAnsar = function () {
            $http({
                url: "{{URL::to('HRM/getTotalAnsar')}}",
                method: 'get',
            }).then(function (response) {
//                alert(JSON.stringify(response.data));
                $scope.allAnsar = response.data;
                console.log(response.data);
                $scope.loadingAnsar = false;
            }, function (response) {
                $scope.loadingAnsar = false;
            })
        }
        $scope.loadAnsar();

        $scope.loadRecentAnsar = function () {

            $http({
                url: "{{URL::to('HRM/getrecentansar')}}",
                method: 'get',
            }).then(function (response) {
//                alert(JSON.stringify(response.data));
                $scope.recentAnsar = response.data;
                console.log(response.data);
//                $scope.loadingAnsar = false;
            }, function (response) {
//                $scope.loadingAnsar = false;
            })
        }
        $scope.loadRecentAnsar();
        
        
        $scope.progressInfo = [];
        $scope.loadingProgressInfo = true;
        $scope.progressData = function () {
            $http({
                url: "{{URL::to('HRM/progress_info')}}",
                method: 'get',
            }).then(function (response) {
                $scope.progressInfo = response.data;
                $scope.loadingProgressInfo = false;
            }, function (response) {
                $scope.loadingProgressInfo = false;
            })
        }
        $scope.progressData();
        $http({
            url: "{{URL::to('HRM/graph_embodiment')}}",
            method: 'get',
        }).then(function (response) {
            $scope.graphData=response.data
        })
        $scope.graphDisembodiment = [];
        {{--$scope.graphDisembodimentData = function () {--}}
            {{--$http({--}}
                {{--url: "{{URL::to('graph_disembodiment')}}",--}}
                {{--method: 'get',--}}
            {{--}).then(function (response) {--}}
                {{--$scope.graphData.push({d:response.data})--}}
            {{--})--}}
        {{--}--}}
        {{--$scope.graphDisembodimentData();--}}
    })
    GlobalApp.directive('graph', function () {
        return{
            restrict:'A',
            link: function (scope, elem, attr) {
                scope.$watch('graphData', function (n,o) {
                    var labels=[],ea=[],ed=[];
                    if(Object.keys(n).length>0){

                        n.ea.forEach(function (item) {
                            labels.push(item.month)
                            ea.push(item.total)
                        })
                        n.da.forEach(function (item) {
                            ed.push(item.total)
                        })
                        $.getScript('http://www.chartjs.org/assets/Chart.js', function () {

                            var data = {
                                labels: labels,
                                datasets: [
                                    {
                                        fillColor: "rgba(0,60,100,1)",
                                        strokeColor: "black",
                                        pointColor: "rgba(220,220,220,1)",
                                        pointStrokeColor: "#fff",
                                        data: ea
                                    },
                                    {
                                        fillColor: "rgba(151,187,205,0.5)",
                                        strokeColor: "rgba(151,187,205,1)",
                                        pointColor: "rgba(151,187,205,1)",
                                        pointStrokeColor: "#fff",
                                        data: ed
                                    }
                                ]
                            }

                            var options = {
                                animation: true,
                            };

                            //Get the context of the canvas element we want to select
                            var c = $('#graph-embodiment');
                            var ct = c.get(0).getContext('2d');
                            var ctx = document.getElementById("graph-embodiment").getContext("2d");
                            /*********************/
                            new Chart(ctx).Bar(data, options);
                            //document.getElementById("legendDiv").innerHTML = new Chart(ctx).Bar(data,options).generateLegend();

                        })
                    }
                },true)
            }
        }
    })
</script>
<section class="content"  ng-controller="TotalAnsar">

    <!-- =========================================================== -->
    <div class="row">
        <div class="col-md-3 col-sm-6 col-xs-12">

        </div>
        <!-- /.col -->
        <!-- show line-->
        <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="line-bar-top"></div>
            <div class="info-box bg-aqua"><span class="info-box-icon"><img src="{{asset('dist/img/not_verified.png')}}"></span>

                <div class="info-box-content"><span class="info-box-text">Total Not Verified</span> <span
                            class="info-box-number"><a href="{{URL::to('HRM/show_ansar_list')}}/not_verified_ansar" class="btn-link" style="color: #FFFFFF !important;">[[allAnsar.totalNotVerified]]</a>
                        <img src="{{asset('dist/img/facebook-white.gif')}}" width="20" ng-show="loadingAnsar"></span>

                    <div class="progress">
                        <div class="progress-bar" style="width: 70%"></div>
                    </div>
                    <span class="progress-description"><a href="{{URL::to('HRM/show_recent_ansar_list')}}/not_verified_ansar" class="btn-link" style="color:#FFFFFF">Recent-[[recentAnsar.recentNotVerified]]</a></span></div>
                <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
        </div>

        <!-- /.col -->
        <div class="col-md-3 line-bar-middle col-sm-6 col-xs-12">
            <div class="info-box bg-aqua"><span class="info-box-icon"><i class="fa fa-envelope"></i></span>

                <div class="info-box-content"><span class="info-box-text">Total Offered</span> <span
                            class="info-box-number"><a href="{{URL::to('HRM/show_ansar_list')}}/offerred_ansar" class="btn-link" style="color: #FFFFFF !important;">[[allAnsar.totalOffered]]</a>
                        <img src="{{asset('dist/img/facebook-white.gif')}}" width="20" ng-show="loadingAnsar"></span>

                    <div class="progress">
                        <div class="progress-bar" style="width: 70%"></div>
                    </div>
                    <span class="progress-description" style="color:#FFFFFF"><a href="{{URL::to('HRM/show_recent_ansar_list')}}/offerred_ansar" style="color:#FFFFFF" class="btn-link">Recent-[[recentAnsar.recentOffered]]</a></span></div>
                <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
        </div>
        <!-- /.col -->
        <div class="col-md-3 line-bar-middle col-sm-6 col-xs-12">
            <div class="info-box bg-aqua"><span class="info-box-icon"><img src="{{asset('dist/img/freeze.png')}}"></span>

                <div class="info-box-content"><span class="info-box-text">Total Freeze</span> <span
                            class="info-box-number"><a href="{{URL::to('HRM/show_ansar_list')}}/freezed_ansar" class="btn-link" style="color: #FFFFFF !important;">[[allAnsar.totalFreeze]]</a>
                        <img src="{{asset('dist/img/facebook-white.gif')}}" width="20" ng-show="loadingAnsar"></span>

                    <div class="progress">
                        <div class="progress-bar" style="width: 70%"></div>
                    </div>
                    <span class="progress-description"><a href="{{URL::to('HRM/show_recent_ansar_list')}}/freezed_ansar" style="color:#FFFFFF" class="btn-link">Recent-[[recentAnsar.recentFreeze]]</a></span></div>
                <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
        </div>
        <!-- /.col -->
        <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="info-box bg-aqua"><span class="info-box-icon"><img src="{{asset('dist/img/ansars.png')}}"></span>

                <div class="info-box-content"><span class="info-box-text">Total Ansar</span> <span
                            class="info-box-number"><a href="{{URL::to('HRM/show_ansar_list')}}/all_ansar" class="btn-link" style="color: #FFFFFF !important;" >[[allAnsar.totalAnsar]]</a>
                        <img src="{{asset('dist/img/facebook-white.gif')}}" width="20" ng-show="loadingAnsar"></span>

                    <div class="progress">
                        <div class="progress-bar" style="width: 70%"></div>
                    </div>
                    <span class="progress-description"><a href="{{URL::to('HRM/show_recent_ansar_list')}}/all_ansar" style="color:#FFFFFF" class="btn-link" >Recent-[[recentAnsar.recentAnsar]]</a></span></div>
                <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
        </div>
        <!-- /.col -->
        <div class="col-md-3 line-bar-middle col-sm-6 col-xs-12">
            <div class="info-box bg-aqua"><span class="info-box-icon"><img src="{{asset('dist/img/free.png')}}"></span>

                <div class="info-box-content"><span class="info-box-text">Total Free status</span> <span
                            class="info-box-number"><a href="{{URL::to('HRM/show_ansar_list')}}/free_ansar" class="btn-link" style="color: #FFFFFF !important;">[[allAnsar.totalFree]]</a>
                        <img src="{{asset('dist/img/facebook-white.gif')}}" width="20" ng-show="loadingAnsar"></span>

                    <div class="progress">
                        <div class="progress-bar" style="width: 70%"></div>
                    </div>
                    <span class="progress-description"><a href="{{URL::to('HRM/show_recent_ansar_list')}}/free_ansar" style="color:#FFFFFF" class="btn-link">Recent-[[recentAnsar.recentFree]]</a></span></div>
                <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
        </div>
        <!-- /.col -->
        <div class="col-md-3 line-bar-middle col-sm-6 col-xs-12">
            <div class="info-box bg-aqua"><span class="info-box-icon"><i class="fa fa-bed"></i></span>

                <div class="info-box-content"><span class="info-box-text">Total Rest</span> <span
                            class="info-box-number"><a href="{{URL::to('HRM/show_ansar_list')}}/rest_ansar" class="btn-link" style="color: #FFFFFF !important;">[[allAnsar.totalRest]]</a>
                        <img src="{{asset('dist/img/facebook-white.gif')}}" width="20" ng-show="loadingAnsar"></span>

                    <div class="progress">
                        <div class="progress-bar" style="width: 70%"></div>
                    </div>
                    <span class="progress-description"><a href="{{URL::to('HRM/show_recent_ansar_list')}}/rest_ansar" style="color:#FFFFFF" class="btn-link">Recent-[[recentAnsar.recentRest]]</a></span></div>
                <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
        </div>
        <!-- /.col -->
        <div class="col-md-3 line-bar-middle col-sm-6 col-xs-12">
            <div class="info-box bg-aqua"><span class="info-box-icon"><img src="{{asset('dist/img/blocklist.png')}}"></span>

                <div class="info-box-content"><span class="info-box-text">Total block list</span> <span
                            class="info-box-number"><a href="{{URL::to('HRM/show_ansar_list')}}/blocked_ansar" class="btn-link" style="color: #FFFFFF !important;">[[allAnsar.totalBlockList]]</a>
                        <img src="{{asset('dist/img/facebook-white.gif')}}" width="20" ng-show="loadingAnsar"></span>

                    <div class="progress">
                        <div class="progress-bar" style="width: 70%"></div>
                    </div>
                    <span class="progress-description"><a href="{{URL::to('HRM/show_recent_ansar_list')}}/blocked_ansar" style="color:#FFFFFF" class="btn-link">Recent-[[recentAnsar.recentBlockList]]</a></span></div>
                <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
        </div>
        <!-- /.col -->
        <div class="col-md-3 col-sm-6 col-xs-12">
            {{--<div class="info-box bg-aqua"> <span class="info-box-icon"><i class="fa fa-exclamation-circle"></i></span>--}}
            {{--<div class="info-box-content"> <span class="info-box-text">Total Not Verified (Status Free)</span> <span class="info-box-number">322</span>--}}
            {{--<div class="progress">--}}
            {{--<div class="progress-bar" style="width: 70%"></div>--}}
            {{--</div>--}}
            {{--<span class="progress-description">70% Increase in 30 Days </span> </div>--}}
            {{--<!-- /.info-box-content -->--}}
            {{--</div>--}}
            <!-- /.info-box -->
        </div>
        <!-- /.col -->
        <!-- show line-->
        <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="line-bar-bottom"></div>
            <div class="info-box bg-aqua"><span class="info-box-icon"><img src="{{asset('dist/img/queue.png')}}"></span>

                <div class="info-box-content"><span class="info-box-text">Total Panel</span> <span
                            class="info-box-number"><a href="{{URL::to('HRM/show_ansar_list')}}/paneled_ansar" class="btn-link" style="color: #FFFFFF !important;">[[allAnsar.totalPanel]]</a>
                        <img src="{{asset('dist/img/facebook-white.gif')}}" width="20" ng-show="loadingAnsar"></span>

                    <div class="progress">
                        <div class="progress-bar" style="width: 70%"></div>
                    </div>
                    <span class="progress-description"><a href="{{URL::to('HRM/show_recent_ansar_list')}}/paneled_ansar" style="color:#FFFFFF" class="btn-link">Recent-[[recentAnsar.recentPanel]]</a></span></div>
                <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
        </div>
        <!-- /.col -->
        <div class="col-md-3 line-bar-middle col-sm-6 col-xs-12">
            <div class="info-box bg-aqua"><span class="info-box-icon"><img src="{{asset('dist/img/embodiment2.png')}}"></span>

                <div class="info-box-content"><span class="info-box-text">Total Embodied</span> <span
                            class="info-box-number"><a href="{{URL::to('HRM/show_ansar_list')}}/embodied_ansar" class="btn-link" style="color: #FFFFFF !important;">[[allAnsar.totalEmbodied]]</a>
                        <img src="{{asset('dist/img/facebook-white.gif')}}" width="20" ng-show="loadingAnsar"></span>

                    <div class="progress">
                        <div class="progress-bar" style="width: 70%"></div>
                    </div>
                    <span class="progress-description"><a href="{{URL::to('HRM/show_recent_ansar_list')}}/embodied_ansar" style="color:#FFFFFF" class="btn-link">Recent-[[recentAnsar.recentEmbodied]]</a></span></div>
                <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->

        </div>
        <!-- /.col -->
        <div class="col-md-3 line-bar-middle col-sm-6 col-xs-12">
            <div class="info-box bg-aqua"><span class="info-box-icon"><img src="{{asset('dist/img/blacklist.png')}}"></span>

                <div class="info-box-content"><span class="info-box-text">Total Black list</span> <span
                            class="info-box-number"><a href="{{URL::to('HRM/show_ansar_list')}}/blacked_ansar" class="btn-link" style="color: #FFFFFF !important;">[[allAnsar.totalBlackList]]</a>
                        <img src="{{asset('dist/img/facebook-white.gif')}}" width="20" ng-show="loadingAnsar"></span>

                    <div class="progress">
                        <div class="progress-bar" style="width: 70%"></div>
                    </div>
                    <span class="progress-description"><a href="{{URL::to('HRM/show_recent_ansar_list')}}/blacked_ansar" style="color:#FFFFFF" class="btn-link">Recent-[[recentAnsar.recentBlackList]]</a></span></div>
                <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
        </div>
        <!-- /.col -->
    </div>
    <!-- /.row -->
    <!-- =========================================================== -->
    <div class="row">
        <div class="col-sm-12 col-md-4 col-xs-12">
            <div class="box box-solid" style="margin-top: 30px">
                <div class="box-header with-border">
                    <h3 class="box-title">Progress Info</h3>
                </div>
                <!-- /.box-header -->
                <div class="box-body" style="padding-left: 0;padding-right: 0">
                    <div class="label-hrm">
                        <div class="label-hrm-title">
                            <span class="info-box-text" style="color: #00733e;white-space: normal;overflow: auto;text-overflow: initial">Total Ansar will complete 3 years cycle within 2 month</span>
                        </div>

                        <div class="label-hrm-calculation">
                                <span class="info-box-number" style="color: #00733e;white-space: normal;overflow: auto;text-overflow: initial"><a href="{{URL::to('HRM/service_ended_in_three_years')}}/[[progressInfo.totalServiceEndedInThreeYears]]" class="btn-link">[[progressInfo.totalServiceEndedInThreeYears]]</a><img src="{{asset('dist/img/facebook.gif')}}" width="20" ng-show="loadingProgressInfo">
                                     </span>
                        </div>
                        <br style="clear: left;"/>
                    </div>
                    <div class="label-hrm" style="background: #E6E6E6">
                        <div class="label-hrm-title">
                            <span class="info-box-text" style="color: #880000;white-space: normal;overflow: auto;text-overflow: initial">Total Ansar will reached 50 years of age within 3 month</span>
                        </div>

                        <div class="label-hrm-calculation">
                            <span class="info-box-number" style="color: #880000"><a href="{{URL::to('HRM/ansar_reached_fifty_years')}}/[[progressInfo.totalAnsarReachedFiftyYearsOfAge]]" class="btn-link">[[progressInfo.totalAnsarReachedFiftyYearsOfAge]]</a><img src="{{asset('dist/img/facebook.gif')}}" width="20" ng-show="loadingProgressInfo"></span>
                        </div>
                        <br style="clear: left;"/>
                    </div>
                    <div class="label-hrm">
                        <div class="label-hrm-title">
                            <span class="info-box-text" style="color: #00517e;white-space: normal;overflow: auto;text-overflow: initial">Total list of member who are not interested for more than 10 times</span>
                        </div>

                        <div class="label-hrm-calculation">
                            <span class="info-box-number" style="color: #00517e"><a href="{{URL::to('HRM/ansar_not_interested')}}/[[progressInfo.totalNotInterestedMembersUptoTenTimes]]" class="btn-link">[[progressInfo.totalNotInterestedMembersUptoTenTimes]]</a><img src="{{asset('dist/img/facebook.gif')}}" width="20" ng-show="loadingProgressInfo"></span>
                        </div>
                        <br style="clear: left;"/>
                    </div>
                    {{--<div class="progress">--}}
                    {{--<div class="progress-bar progress-bar-green" role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width: 40%">--}}
                    {{--<span class="sr-only">40% Complete (success)</span>--}}
                    {{--</div>--}}
                    {{--</div>--}}
                    {{--<div class="progress">--}}
                    {{--<div class="progress-bar progress-bar-aqua" role="progressbar" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100" style="width: 20%">--}}
                    {{--<span class="sr-only">20% Complete</span>--}}
                    {{--</div>--}}
                    {{--</div>--}}
                    {{--<div class="progress">--}}
                    {{--<div class="progress-bar progress-bar-yellow" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: 60%">--}}
                    {{--<span class="sr-only">60% Complete (warning)</span>--}}
                    {{--</div>--}}
                    {{--</div>--}}
                    {{--<div class="progress">--}}
                    {{--<div class="progress-bar progress-bar-red" role="progressbar" aria-valuenow="80" aria-valuemin="0" aria-valuemax="100" style="width: 80%">--}}
                    {{--<span class="sr-only">80% Complete</span>--}}
                    {{--</div>--}}
                    {{--</div>--}}
                </div>
                <!-- /.box-body -->
            </div>
        </div>
        <div class="col-sm-12 col-md-8 col-xs-12">
            <div class="box box-solid">
                <div class="box-header with-border">
                    <h3 class="box-title">Total Ansar Emboded and Disemboded in recent Year</h3>
                    <div id="graph-level" class="col-md-8 col-sm-6 col-xs-8" style="float: right">
                        <div class="label-graph">
                            <div class="label-graph-title">
                                <span class="info-box-text" style="color: #000000">Emboded</span>
                            </div>
                            <div class="label-graph-name">
                                <div style="background-color: rgba(0,60,100,1); border-radius:50%; width: 20px; height: 20px; padding-right: 5px"></div>
                            </div>
                            <div class="label-graph-title">
                                <span class="info-box-text" style="color: #000000">Disemboded</span>
                            </div>
                            <div class="label-graph-name">
                                <div style="background-color: rgba(151,187,205,0.5); border-radius:50%; width: 20px; height: 20px; padding-right: 5px"></div>
                            </div>
                            <br style="clear: left;"/>
                        </div>
                    </div>
                </div>
                <!-- /.box-header -->
                <div class="box-body">
                    <canvas id="graph-embodiment" graph style="width: 100%; height: 160px;" class="well"></canvas>
                </div>
                <!-- /.box-body -->
            </div>
        </div>
        {{--<div class="col-sm-4 col-md-4">--}}
        {{--<div class="box box-solid">--}}
        {{--<div class="box-header with-border">--}}
        {{--<h3 class="box-title">Total Ansar Disemboded in recent Year</h3>--}}
        {{--</div><!-- /.box-header -->--}}
        {{--<div class="box-body">--}}
        {{--<canvas id="graph-disembodiment" style="width: 100%" class="well"></canvas>--}}
        {{--</div><!-- /.box-body -->--}}
        {{--</div>--}}
        {{--</div>--}}
    </div>
</section>
<!-- /.content-wrapper -->

@endsection
      