@extends('template.master')
@section('title','Exam Center')
@section('small_title')
    <a href="{{URL::route('recruitment.exam-center.create')}}" class="btn btn-primary btn-sm"><i class="fa fa-clipboard"></i>&nbsp;Add New Exam Center</a>
    @endsection
@section('breadcrumb')
    {!! Breadcrumbs::render('recruitment.setting.exam_center') !!}
@endsection
@section('content')
    <script>
        GlobalApp.controller('JobApplicantExamCenterController',function ($scope, $http,httpService,$sce) {

            $scope.examCenters = $sce.trustAsHtml(
                `<table class="table table-bordered">
                                <tr>
                                    <th>SL. No</th>
                                    <th>Job Circular Name</th>
                                    <th>Selection Date</th>
                                    <th>Selection Place</th>
                                    <th>Selection Units</th>
                                    <th>Written Viva Date</th>
                                    <th>Written Viva Place</th>
                                    <th>Written Viva Units</th>
                                    <th>Action</th>
                                </tr>
                                <tr>
                                    <td class="warning" colspan="9">No job category available</td>
                                </tr>
                                </tbody>
                            </table>`
            );
            $scope.queue = [];
            $scope.dataErrors = {
                status:false,
                message:''
            }
            $scope.params={page:1};
            httpService.circular().then(function (response) {
                $scope.circulars = response.data;
            },function (response) {
                $scope.circulars = [];
                $scope.params['circular'] = '';
            })

            $scope.allLoading = false;
            $scope.loadExamCenter = function () {
                $scope.allLoading = true;
                $http({
                    url:window.location.href,
                    method:'get',
                    params:$scope.params
                }).then(function (response) {

                    $scope.examCenters = $sce.trustAsHtml(response.data);
                    $scope.dataErrors.status = false;
                    $scope.dataErrors.message = '';
                    $scope.allLoading = false;

                },function (response) {

                    $scope.dataErrors.status = true;
                    $scope.dataErrors.message = 'An error occur while data loading. Please try again later';

                    $scope.examCenters = [];
                    $scope.allLoading = false;
//                    alert($scope.dataErrors.status)
                })

            }
            $scope.loadExamCenter()

        })
        GlobalApp.directive('compileHtml', function ($compile) {
            return {
                restrict: 'A',
                link: function (scope, elem, attr) {
                    scope.$watch('examCenters', function (n) {

                        if (attr.ngBindHtml) {
                            $compile(elem[0].children)(scope)
                        }
                    })

                }
            }
        })
    </script>
    <div ng-controller="JobApplicantExamCenterController">
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
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label for="" class="control-label">
                                    Select a circular
                                </label>
                                <select ng-model="params.circular" class="form-control" ng-change="loadExamCenter()">
                                    <option value="">--Select a circular--</option>
                                    <option ng-repeat="c in circulars" value="[[c.id]]">[[c.circular_name]]</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <filter-template
                            show-item="['range','unit']"
                            type="all"
                            range-change="loadExamCenter()"
                            unit-change="loadExamCenter()"
                            range-field-disabled="!params.circular"
                            unit-field-disabled="!params.circular"
                            data="params"
                            start-load="range"
                            field-width="{range:'col-sm-4',unit:'col-sm-4'}"
                    >
                    </filter-template>
                    <h4 class="text text-bold">All Exam Center</h4>
                    <div ng-bind-html="examCenters" compile-html>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <tr>
                                    <th>SL. No</th>
                                    <th>Job Circular Name</th>
                                    <th>Selection Date</th>
                                    <th>Selection Place</th>
                                    <th>Selection Units</th>
                                    <th>Written Viva Date</th>
                                    <th>Written Viva Place</th>
                                    <th>Written Viva Units</th>
                                    <th>Action</th>
                                </tr>
                                <tr>
                                    <td class="warning" colspan="9">No job category available</td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@stop