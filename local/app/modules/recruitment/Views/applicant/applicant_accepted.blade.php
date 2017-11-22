@extends('template.master')
@section('title','Final Applicant List')
@section('breadcrumb')
    {!! Breadcrumbs::render('recruitment.applicant.search') !!}
@endsection
@section('content')
    <script>
        GlobalApp.controller('applicantSearch', function ($scope, $http, $q, httpService, $sce,notificationService) {
            $scope.circulars = [];
            $scope.param = {};
            httpService.circular({status: 'running'}).then(function (response) {
                $scope.circulars = response.data;
            })
            $scope.loadApplicant = function () {
                $scope.allLoading = true;
                $http({
                    method:'post',
                    url:'{{URL::route('recruitment.applicant.final_list_load')}}',
                    data:angular.toJson($scope.param)
                }).then(function (response) {
                    $scope.allLoading = false;
                    $scope.applicants = $sce.trustAsHtml(response.data);
                },function (response) {
                    $scope.allLoading = false;
                })
            }
            $scope.confirmSelectionAsAccepted = function () {
                $scope.allLoading = true;
                $http({
                    method:'post',
                    url:'{{URL::route('recruitment.applicant.confirm_accepted')}}',
                    data:angular.toJson($scope.param)
                }).then(function (response) {
                    $scope.allLoading = false;
                    $scope.applicants = $sce.trustAsHtml('');
                    notificationService.notify(response.data.status,response.data.message)
                },function (response) {
                    $scope.allLoading = false;
                    notificationService.notify('error',response.statusText)
                })
            }

        })
        GlobalApp.directive('compileHtml', function ($compile) {
            return {
                restrict: 'A',
                link: function (scope, elem, attr) {
                    scope.$watch('applicants', function (n) {

                        if (attr.ngBindHtml) {
                            $compile(elem[0].children)(scope)
                        }
                    })

                }
            }
        })
        GlobalApp.directive('confirmDialog', function () {
            return {
                restrict: 'A',
                link: function (scope, elem, attr) {
                    $(element).confirmDialog({
                        message: scope.message||"Are u sure?",
                        ok_button_text: 'Confirm',
                        cancel_button_text: 'Cancel',
                        event: 'click',
                        ok_callback: function (element) {
                            scope.confirmSelectionAsAccepted()
                        },
                        cancel_callback: function (element) {
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
                    <div class="col-sm-6 col-centered">
                        <div class="form-group">
                            <label for="" class="control-label">Job Circular</label>
                            <select name="" ng-model="param.circular"
                                    class="form-control">
                                <option value="">--Select a circular--</option>
                                <option ng-repeat="c in circulars" value="[[c.id]]">[[c.circular_name]]</option>
                            </select>
                        </div>
                        <filter-template
                                show-item="['range','unit']"
                                type="single"
                                data="param"
                                start-load="range"

                                unit-field-disabled="!param.circular"
                                range-field-disabled="!param.circular"
                                field-width="{unit:'col-sm-12',range:'col-sm-12'}"
                        >
                        </filter-template>
                        <div class="form-group">
                            <button ng-click="loadApplicant()" ng-disabled="!(param.circular&&param.unit)" class="btn btn-primary btn-block">
                                Load short listed applicant
                            </button>
                        </div>
                    </div>
                </div>
                <div ng-bind-html="applicants" compile-html>

                </div>
            </div>
        </div>
    </section>

@endsection