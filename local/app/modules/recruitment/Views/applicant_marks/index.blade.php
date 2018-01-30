@extends('template.master')
@section('title','Applicants Mark Entry')
@section('breadcrumb')
    {!! Breadcrumbs::render('recruitment.applicant.list') !!}
@endsection
@section('content')
    <script>
        GlobalApp.controller('ApplicantsListController',function ($scope, $http, $sce) {
            $scope.applicants = $sce.trustAsHtml("<h3 class='text text-center'>Data loading....</h3>")
            $scope.param = {};
            $scope.limitList = p = '50';
            $scope.markForm = $sce.trustAsHtml('');
            $scope.q = '';
            var v = '<div class="text-center" style="margin-top: 20px"><i class="fa fa-spinner fa-pulse"></i></div>'
            $scope.allLoading = false;
            $scope.loadApplicant = function (url) {
                $scope.param['limit'] = $scope.limitList;
                $scope.param['q'] = $scope.q;
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
            $scope.$watch('limitList', function (n, o) {
                if (n == null) {
                    $scope.limitList = o;
                }
                else if (p != n && p != null) {
                    p = n;
                    $scope.loadApplicant();
                }
            })
        })
        GlobalApp.directive('compileHtml',function ($compile) {
            return {
                restrict:'A',
                link:function (scope,elem,attr) {
                    var newScope;
                    scope.$watch('applicants', function (n) {
                        if(newScope) newScope.$destroy();
                        newScope = scope.$new();
                        if (attr.ngBindHtml) {
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
                    scope.$watch('markForm',function(n){

                        if(attr.ngBindHtml) {
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
                <filter-template
                        show-item="['range','unit','thana']"
                        type="all"
                        range-change="loadApplicant()"
                        unit-change="loadApplicant()"
                        thana-change="loadApplicant()"
                        on-load="loadApplicant()"
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
