@extends('template.master')
@section('title','Applicants List')
@section('breadcrumb')
    {!! Breadcrumbs::render('recruitment.applicant.list') !!}
@endsection
@section('content')
    <script>
        GlobalApp.controller('ApplicantsListController',function ($scope, $http, $sce) {
            $scope.applicants = $sce.trustAsHtml("<h3 class='text text-center'>Data loading....</h3>")
            $scope.param = {};

            $scope.allLoading = false;
            $scope.loadPage = function (url) {
                var link = url || window.location.href
                $scope.allLoading = true;
                $http({
                    url:link,
                    params:$scope.param
                }).then(function (response) {
                    $scope.applicants = $sce.trustAsHtml(response.data);
                    $scope.allLoading = false;
                },function (response) {
                    $scope.allLoading = false;
                    $scope.applicants = $sce.trustAsHtml("<h3 class='text text-center'>Error ocur while loading. try again later</h3>")
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
                        range-change="loadPage()"
                        unit-change="loadPage()"
                        thana-change="loadPage()"
                        on-load="loadPage()"
                        data="param"
                        start-load="range"
                        field-width="{range:'col-sm-4',unit:'col-sm-4',thana:'col-sm-4'}"
                >
                </filter-template>
                {{--<div class="row" style="margin-bottom: 20px">
                    <div class="col-sm-6 col-sm-offset-6">
                        <form action="{{URL::route('recruitment.applicant.list',['type'=>$type])}}" method="get">
                            <div class="input-group">
                                <input type="text" name="q" class="form-control"
                                       placeholder="Search here by txID or mobile no">
                                <span class="input-group-btn">
                                            <button class="btn btn-primary">
                                                <i class="fa fa-search"></i>
                                            </button>
                                        </span>
                            </div>
                        </form>
                    </div>
                </div>--}}
                <div ng-bind-html="applicants" compile-html>

                </div>

            </div>
        </div>
    </section>
@endsection
