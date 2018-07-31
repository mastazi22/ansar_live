@extends('template.master')
@section('title','Training Course')
@section('small_title')
    <a href="{{URL::route('recruitment.training.courses.create')}}" class="btn btn-primary btn-sm"><i class="fa fa-list"></i>&nbsp;Add New Course</a>
    @endsection
@section('breadcrumb')
    {!! Breadcrumbs::render('job_category') !!}
@endsection
@section('content')
    <script>
        GlobalApp.controller('TrainingCourseController',function ($scope, $http,$filter) {

            $scope.trainingCourses = [];
            $scope.queue = [];
            $scope.dataErrors = {
                status:false,
                message:''
            }
            $scope.allLoading = false;
            $scope.loadTrainingCourses = function () {
                $scope.allLoading = true;
                $http({
                    url:window.location.href,
                    method:'get',
                    params:{
                        q:$scope.q
                    }
                }).then(function (response) {
                    $scope.queue.shift();
                    if ($scope.queue.length > 1) $scope.loadTrainingCourses();

                    $scope.trainingCourses = response.data;
                    $scope.dataErrors.status = false;
                    $scope.dataErrors.message = '';
                    $scope.allLoading = false;

                },function (response) {

                    $scope.dataErrors.status = true;
                    $scope.dataErrors.message = 'An error occur while data loading. Please try again later';

                    $scope.trainingCourses = [];
                    $scope.allLoading = false;
                    alert($scope.dataErrors.status)
                })

            }
            $scope.loadTrainingCourses()
            $scope.categoryType = function (v) {
                if(!v) return '';
                v = v.split('_');
                if(v.length===1) return $filter('ucfirst')(v[0]);
                else return $filter('ucfirst')(v[0])+" "+$filter('ucfirst')(v[1]);
            }

        })
    </script>
    <div ng-controller="TrainingCourseController">
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
                            <h4 class="text text-bold">Training Course</h4>
                        </div>
                        <div class="col-md-4">
                            <database-search q="q" place-holder="Search Here" queue="queue" on-change="loadTrainingCourses()"></database-search>

                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <tr>
                                <th>SL. No</th>
                                <th>Course Title</th>
                                <th>Course Category</th>
                                <th>Course Center</th>
                                <th>Number of applicant</th>
                                <th>Action</th>
                            </tr>
                            <tr ng-repeat="jc in trainingCourses">
                                <td>[[$index+1]]</td>
                                <td>[[jc.course_name]]</td>
                                <td>[[jc.category.training_category_name_bng]]</td>
                                <td>
                                    <span ng-repeat="tc in jc.center">
                                        ssss
                                    </span>
                                </td>
                                <td>[[jc.number_of_applicants]]</td>
                                <td>
                                    <a href="{{URL::to('/recruitment.training/courses')}}/[[jc.id]]/edit" class="btn btn-primary btn-sm">
                                        <i class="fa fa-edit"></i>&nbsp;Edit
                                    </a>
                                </td>
                            </tr>
                            <tr ng-if="trainingCourses<=0">
                                <td class="warning" colspan="5">No training category available</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>
    </div>
@stop