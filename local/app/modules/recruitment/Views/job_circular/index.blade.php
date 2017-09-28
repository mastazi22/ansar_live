@extends('template.master')
@section('title','Job Circular')
@section('small_title')
    <a href="{{URL::route('recruitment.circular.create')}}" class="btn btn-primary btn-sm"><i class="fa fa-clipboard"></i>&nbsp;Add New Circular</a>
    @endsection
@section('breadcrumb')
    {!! Breadcrumbs::render('job_circular') !!}
@endsection
@section('content')
    <script>
        GlobalApp.controller('JobCircularController',function ($scope, $http) {

            $scope.jobCirculars = [];
            $scope.queue = [];
            $scope.dataErrors = {
                status:false,
                message:''
            }
            $scope.allLoading = false;
            $scope.loadJobCircular = function () {
                $scope.allLoading = true;
                $http({
                    url:window.location.href,
                    method:'get',
                    params:{
                        q:$scope.q
                    }
                }).then(function (response) {
                    $scope.queue.shift();
                    if ($scope.queue.length > 1) $scope.loadJobCategories();

                    $scope.jobCirculars = response.data;
                    $scope.dataErrors.status = false;
                    $scope.dataErrors.message = '';
                    $scope.allLoading = false;

                },function (response) {

                    $scope.dataErrors.status = true;
                    $scope.dataErrors.message = 'An error occur while data loading. Please try again later';

                    $scope.jobCirculars = [];
                    $scope.allLoading = false;
                    alert($scope.dataErrors.status)
                })

            }
            $scope.loadJobCircular()

        })
    </script>
    <div ng-controller="JobCircularController">
        <section class="content" >
            <div ng-if="$scope.dataErrors.status" class="alert alert-danger">
                <i class="fa fa-warning"></i>&nbsp;[[$scope.dataErrors.message]]
            </div>
            @if(Session::has('session_error'))
                <div class="alert alert-danger">
                    <i class="fa fa-warning"></i>&nbsp;{{Session::get('session_error')}}
                </div>
                @elseif(Session::has('session_success'))
                <div class="alert alert-success">
                    <i class="fa fa-check"></i>&nbsp;{{Session::get('session_success')}}
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
                            <h4 class="text text-bold">All Job Circular</h4>
                        </div>
                        <div class="col-md-4">
                            <database-search q="q" place-holder="Search Job Circular" queue="queue" on-change="loadJobCircular()"></database-search>

                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <tr>
                                <th>SL. No</th>
                                <th>Job Circular Title</th>
                                <th>Job Circular Category</th>
                                <th>Start Date</th>
                                <th>End Date</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                            <tr ng-repeat="jc in jobCirculars">
                                <td>[[$index+1]]</td>
                                <td>[[jc.circular_name]]</td>
                                <td>[[jc.category.category_name_bng?jc.category.category_name_bng:jc.category.category_name_eng]]</td>
                                <td>[[jc.start_date|dateformat:"DD-MMM-YYYY"]]</td>
                                <td>[[jc.end_date|dateformat:"DD-MMM-YYYY"]]</td>
                                <td>[[jc.status|ucfirst]]</td>
                                <td>
                                    <a href="{{URL::to('/recruitment/circular')}}/[[jc.id]]/edit" class="btn btn-primary btn-sm">
                                        <i class="fa fa-edit"></i>&nbsp;Edit
                                    </a>
                                </td>
                            </tr>
                            <tr ng-if="jobCirculars<=0">
                                <td class="warning" colspan="7">No job category available</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>
    </div>
@stop