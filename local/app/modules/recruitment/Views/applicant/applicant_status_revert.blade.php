@extends('template.master')
@section('title','Revert Applicant Status')
@section('breadcrumb')
    {!! Breadcrumbs::render('recruitment.applicant.revert_application_status') !!}
@endsection
@section('content')
    <script>
        GlobalApp.controller('applicantSearch', function ($scope, $http, $q, httpService, $sce, notificationService) {
            var p = '50'
            $scope.categories = [];
            $scope.q = '';
            $scope.selectMessage = '';
            $scope.educations = [];
            $scope.circulars = [];
            $scope.applicants = $sce.trustAsHtml(`<div class="table-responsive">
                        <table class="table table-bordered table-condensed">
                            <caption style="font-size: 20px;color:#111111">All applicants</caption>
                            <tr>
                                <th>Sl. No</th>
                                <th>Applicant ID</th>
                                <th>Applicant Name</th>
                                <th>Father Name</th>
                                <th>Height</th>
                                <th>Gender</th>
                                <th>Birth Date</th>
                                <th>Division</th>
                                <th>District</th>
                                <th>Thana</th>
                                <th>Status</th>
                                <th>Revert Status to</th>
                                <th>Action</th>
                            </tr>
                            <tr>
                                <td colspan="15" class="bg-warning">No applicant available
                                .Select <strong>Circular</strong> and <strong>status</strong> to load applicant
                                </td>
                            </tr>
                        </table>
                    </div>`);
            $scope.allStatus = {
                '': '--Select a status',
                'applied': 'Applied',
                'selected': 'Selected',
                'accepted': 'Accepted'
            }
            $scope.param = {};
            $scope.limitList = '50';

            httpService.circular({status: 'running'}).then(function (response) {
                $scope.circulars = response.data;
                $scope.allLoading = false;
            }, function (response) {
                $scope.circulars = [];
                $scope.allLoading = false;
            })
            $scope.loadApplicant = function (url) {
                //alert($scope.limitList)
                $scope.allLoading = true;
                if ($scope.param['limit'] === undefined) {
                    $scope.param['limit'] = '50'
                }
                $http({
                    url: url || '{{URL::route('recruitment.applicant.revert')}}',
                    method: 'post',
                    data: $scope.param
                }).then(function (response) {
                    $scope.applicants = $sce.trustAsHtml(response.data);
                    $scope.allLoading = false;
                }, function (response) {
                    $scope.applicants = $sce.trustAsHtml('loading error.....');
                    $scope.allLoading = false;
                })
            }
            $scope.revertStatus = function (data) {
                $scope.allLoading = true;
                $http({
                    url: '{{URL::route('recruitment.applicant.revert_status')}}',
                    method: 'post',
                    data: data
                }).then(function (res) {
                    $scope.allLoading = false;
                    notificationService.notify(res.data.status, res.data.message)
                }, function (res) {
                    $scope.allLoading = false;
                    notificationService.notify('error', res.statusText)
                })
            }


        })
        GlobalApp.directive('compileHtml', function ($compile) {
            return {
                restrict: 'A',
                link: function (scope, elem, attr) {
                    var newScope;
                    scope.$watch('applicants', function (n) {

                        if (attr.ngBindHtml) {
                            if (newScope) newScope.$destroy();
                            newScope = scope.$new();
                            $compile(elem[0].children)(newScope)
                        }
                    })

                }
            }
        })
    </script>
    <section class="content" ng-controller="applicantSearch">
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
                            <label for="" class="control-label">Job Circular</label>
                            <select name="" ng-model="param.circular"
                                    class="form-control">
                                <option value="">--Select a circular</option>
                                <option ng-repeat="c in circulars" value="[[c.id]]">[[c.circular_name]]</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label for="" class="control-label">Applicant status</label>
                            <select name="" ng-model="param.status"
                                    class="form-control">
                                <option ng-repeat="(k,v) in allStatus" value="[[k]]">[[v]]</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label for="" class="control-label " style="display: block;">&nbsp;</label>
                            <button ng-disabled="!(param.status&&param.circular)" class="btn btn-primary"
                                    ng-click="loadApplicant()">Load Applicant
                            </button>
                        </div>
                    </div>
                </div>
                <filter-template
                        show-item="['range','unit','thana']"
                        type="all"
                        data="param"
                        start-load="range"
                        field-name="{unit:'unit'}"
                        range-change="loadApplicant()"
                        unit-change="loadApplicant()"
                        thana-change="loadApplicant()"
                        unit-field-disabled="!(param.circular&&param.status)"
                        range-field-disabled="!(param.circular&&param.status)"
                        thana-field-disabled="!(param.circular&&param.status)"
                        field-width="{unit:'col-sm-4',range:'col-sm-4',thana:'col-sm-4'}"
                >
                </filter-template>
                <div ng-bind-html="applicants" compile-html>

                </div>
            </div>
        </div>
    </section>

@endsection
