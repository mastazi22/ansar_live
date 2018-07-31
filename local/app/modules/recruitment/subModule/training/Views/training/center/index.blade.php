@extends('template.master')
@section('title','Training Center')
@section('small_title')
    <a href="{{URL::route('recruitment.training.center.create')}}" class="btn btn-primary btn-sm"><i class="fa fa-list"></i>&nbsp;Add New Center</a>
    @endsection
@section('breadcrumb')
    {!! Breadcrumbs::render('job_category') !!}
@endsection
@section('content')
    <script>
        GlobalApp.controller('TrainingCenterController',function ($scope, $http,$filter) {

            $scope.trainingCenters = [];
            $scope.queue = [];
            $scope.dataErrors = {
                status:false,
                message:''
            }
            $scope.allLoading = false;
            $scope.loadTrainingCenters = function () {
                $scope.allLoading = true;
                $http({
                    url:window.location.href,
                    method:'get',
                    params:{
                        q:$scope.q
                    }
                }).then(function (response) {
                    $scope.queue.shift();
                    if ($scope.queue.length > 1) $scope.loadTrainingCenters();

                    $scope.trainingCenters = response.data;
                    $scope.dataErrors.status = false;
                    $scope.dataErrors.message = '';
                    $scope.allLoading = false;

                },function (response) {

                    $scope.dataErrors.status = true;
                    $scope.dataErrors.message = 'An error occur while data loading. Please try again later';

                    $scope.trainingCenters = [];
                    $scope.allLoading = false;
                    alert($scope.dataErrors.status)
                })

            }
            $scope.loadTrainingCenters()
            $scope.categoryType = function (v) {
                if(!v) return '';
                v = v.split('_');
                if(v.length===1) return $filter('ucfirst')(v[0]);
                else return $filter('ucfirst')(v[0])+" "+$filter('ucfirst')(v[1]);
            }

        })
    </script>
    <div ng-controller="TrainingCenterController">
        <section class="content" >
            <div ng-if="$scope.dataErrors.status" class="alert alert-danger">
                <i class="fa fa-warning"></i>&nbsp;[[$scope.dataErrors.message]]
                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
            </div>
            @if(Session::has('session_error'))
                <div class="alert alert-danger">
                    <i class="fa fa-warning"></i>&nbsp;{{Session::get('session_error')}}
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                </div>
                @elseif(Session::has('session_success'))
                <div class="alert alert-success">
                    <i class="fa fa-check"></i>&nbsp;{{Session::get('session_success')}}
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                </div>
                @endif
            <div class="box box-solid">
                <div class="overlay" ng-if="allLoading">
                    <span class="fa">
                        <i class="fa fa-refresh fa-spin"></i> <b>Loading...</b>
                    </span>
                </div>
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-8">
                            <h4 class="text text-bold">Training Center</h4>
                        </div>
                        <div class="col-md-4">
                            <database-search q="q" place-holder="Search Here" queue="queue" on-change="loadTrainingCenters()"></database-search>

                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <tr>
                                <th>SL. No</th>
                                <th>Center Name</th>
                                <th>Center Division</th>
                                <th>Center District</th>
                                <th>Center Thana</th>
                                <th>Action</th>
                            </tr>
                            <tr ng-repeat="jc in trainingCenters">
                                <td>[[$index+1]]</td>
                                <td>[[jc.center_name]]</td>
                                <td>[[jc.division.division_name_bng]]</td>
                                <td>
                                    [[jc.unit.unit_name_bng]]
                                </td>
                                <td>[[jc.thana.thana_name_bng]]</td>
                                <td>
                                    <a href="{{URL::to('/recruitment.training/center')}}/[[jc.id]]/edit" class="btn btn-primary btn-sm">
                                        <i class="fa fa-edit"></i>&nbsp;Edit
                                    </a>
                                </td>
                            </tr>
                            <tr ng-if="trainingCenters<=0">
                                <td class="warning" colspan="6">No training center available</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>
    </div>
@stop