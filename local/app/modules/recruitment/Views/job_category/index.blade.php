@extends('template.master')
@section('title','Job Category')
@section('small_title')
    <a href="{{URL::route('recruitment.category.create')}}" class="btn btn-primary btn-sm"><i class="fa fa-list"></i>&nbsp;Add New Category</a>
    @endsection
@section('breadcrumb')
    {!! Breadcrumbs::render('job_category') !!}
@endsection
@section('content')
    <script>
        GlobalApp.controller('JobCategoryController',function ($scope, $http) {

            $scope.jobCategories = [];
            $scope.queue = [];
            $scope.dataErrors = {
                status:false,
                message:''
            }
            $scope.allLoading = false;
            $scope.loadJobCategories = function () {
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

                    $scope.jobCategories = response.data;
                    $scope.dataErrors.status = false;
                    $scope.dataErrors.message = '';
                    $scope.allLoading = false;

                },function (response) {

                    $scope.dataErrors.status = true;
                    $scope.dataErrors.message = 'An error occur while data loading. Please try again later';

                    $scope.jobCategories = [];
                    $scope.allLoading = false;
                    alert($scope.dataErrors.status)
                })

            }
            $scope.loadJobCategories()

        })
    </script>
    <div ng-controller="JobCategoryController">
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
                            <h4 class="text text-bold">Job Category</h4>
                        </div>
                        <div class="col-md-4">
                            <database-search q="q" place-holder="Search Here" queue="queue" on-change="loadJobCategories()"></database-search>

                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <tr>
                                <th>SL. No</th>
                                <th>Job Category Title</th>
                                <th>Job Category Description</th>
                                <th>Job Category Status</th>
                                <th>Action</th>
                            </tr>
                            <tr ng-repeat="jc in jobCategories">
                                <td>[[$index+1]]</td>
                                <td>[[jc.category_name_bng?jc.category_name_bng:jc.category_name_eng]]</td>
                                <td>[[jc.category_description]]</td>
                                <td>[[jc.status|ucfirst]]</td>
                                <td>
                                    <a href="{{URL::to('/recruitment/category')}}/[[jc.id]]/edit" class="btn btn-primary btn-sm">
                                        <i class="fa fa-edit"></i>&nbsp;Edit
                                    </a>
                                </td>
                            </tr>
                            <tr ng-if="jobCategories<=0">
                                <td class="warning" colspan="5">No job category available</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>
    </div>
@stop