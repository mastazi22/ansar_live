@extends('template.master')
@section('title','Applicants Mark Entry')
@section('breadcrumb')
    {!! Breadcrumbs::render('recruitment.applicant.list') !!}
@endsection
@section('content')
    <script>
        GlobalApp.controller('ApplicantsListController',function ($scope, $http, $sce,httpService) {
            $scope.applicants = $sce.trustAsHtml("<h3 class='text text-center'>Data loading....</h3>")
            $scope.param = {};
            $scope.param['limit'] = '50'
//            $scope.limitList = p = '50';
            $scope.markForm = $sce.trustAsHtml('');
            $scope.q = '';
            var v = '<div class="text-center" style="margin-top: 20px"><i class="fa fa-spinner fa-pulse"></i></div>'
            $scope.allLoading = true;
            httpService.circular({status:'running'}).then(function (circulars) {
                $scope.circulars = circulars.data;
                $scope.allLoading = false;
            },function (error) {
                $scope.allLoading = false;
                $scope.circulars=[];
            })
            $scope.loadApplicant = function (url) {
                if($scope.param.limit===undefined){
                    $scope.param['limit'] = '50'
                }
                var link = url || '{{URL::route('recruitment.marks.index')}}'
                $scope.allLoading = true;
                $http({
                    url:link,
                    params:$scope.param,
                }).then(function (response) {
                    $scope.applicants = $sce.trustAsHtml(response.data);
                    $scope.allLoading = false;
                },function (response) {
                    $scope.allLoading = false;
                    $scope.applicants = $sce.trustAsHtml("<h3 class='text text-center'>Error ocur while loading. try again later</h3>")
                })
            }
            $scope.editMark = function (id) {
                $scope.markForm = $sce.trustAsHtml(v);
                $('#mark-form').modal('show')
                var link = '{{URL::to('recruitment/marks')}}/'+id+"/edit"
                $http({
                    url:link,
                }).then(function (response) {
                    $scope.markForm = $sce.trustAsHtml(response.data);

                },function (response) {
                    $scope.markForm = $sce.trustAsHtml("<h3 class='text text-center'>Error ocur while loading. try again later</h3>")

                })
            }
        })
        GlobalApp.directive('compileHtml',function ($compile) {
            return {
                restrict:'A',
                link:function (scope,elem,attr) {
                    var newScope;
                    scope.$watch('applicants', function (n) {

                        if (attr.ngBindHtml) {
                            if(newScope) newScope.$destroy();
                            newScope = scope.$new();
                            $compile(elem[0].children)(newScope)
                        }
                    })

                }
            }
        })
        GlobalApp.directive('compileHtmll',function ($compile) {
            return {
                restrict:'A',
                link:function (scope,elem,attr) {
                    var newScope;
                    scope.$watch('markForm',function(n){

                        if(attr.ngBindHtml) {
                            if(newScope) newScope.$destroy();
                            newScope = scope.$new();
                            $compile(elem[0].children)(scope)
                        }
                    })

                }
            }
        })
    </script>
    <section class="content" ng-controller="ApplicantsListController">
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
                            <label for="" class="control-label">Select a circular</label>
                            <select ng-model="param.circular" ng-change="loadApplicant()" class="form-control">
                                <option value="">--Select a circular--</option>
                                <option ng-repeat="c in circulars" value="[[c.id]]">[[c.circular_name]]</option>
                            </select>
                        </div>
                    </div>
                </div>
                <filter-template
                        show-item="['range','unit','thana']"
                        type="all"
                        range-change="loadApplicant()"
                        unit-change="loadApplicant()"
                        thana-change="loadApplicant()"
                        range-field-disabled="!param.circular"
                        unit-field-disabled="!param.circular"
                        thana-field-disabled="!param.circular"
                        data="param"
                        start-load="range"
                        field-width="{range:'col-sm-4',unit:'col-sm-4',thana:'col-sm-4'}"
                >
                </filter-template>
                <div ng-bind-html="applicants" compile-html>

                </div>

            </div>
        </div>
        <div class="modal fade" id="mark-form">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Edit mark</h4>
                    </div>
                    <div class="modal-body">
                        <div ng-bind-html="markForm" compile-htmll>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
