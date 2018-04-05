@extends('template.master')
@section('title','Entry List')
@section('breadcrumb')
    {!! Breadcrumbs::render('entry.list') !!}
@endsection
@section('content')
    <script>
        GlobalApp.controller('VDPController',function ($scope, $http, $sce) {
            $scope.param = {};
            $scope.vdpList = $sce.trustAsHtml(`<div class="table-responsive">
                        <table class="table table-bordered table-condensed">
                            <caption style="font-size: 20px;color:#111111">All VDP Member</caption>
                            <tr>
                                <th>#</th>
                                <th>VDP ID</th>
                                <th>Name(English)</th>
                                <th>Name(Bangla)</th>
                                <th>Date of Birth</th>
                                <th>Division</th>
                                <th>District</th>
                                <th>Thana</th>
                                <th>Union</th>
                                <th>Ward</th>
                                <th>Action</th>

                            </tr>
                            <tr>
                                <td colspan="11" class="bg-warning">No VDP info available
                                </td>
                            </tr>
                        </table>
                    </div>`);
            $scope.allLoading = false;
            $scope.loadPage = function (url) {
                $scope.allLoading = true;
                $http({
                    url:url||'{{URL::route('AVURP.info.index')}}',
                    method:'get',
                    params:$scope.param
                }).then(function (response) {
                    $scope.allLoading = false;
                    $scope.vdpList = $sce.trustAsHtml(response.data);
                },function (response) {
                    $scope.allLoading = false;
                })

            }

        })
        GlobalApp.directive('compileHtml',function ($compile) {
            return {
                restrict:'A',
                link:function (scope,elem,attr) {
                    var newScope;
                    scope.$watch('vdpList', function (n) {

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
    <section class="content">
        @if(Session::has('success_message'))
            <div style="padding: 10px 20px 0 20px;">
                <div class="alert alert-success">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                    <span class="glyphicon glyphicon-ok"></span> {{Session::get('success_message')}}
                </div>
            </div>
        @endif
        @if(Session::has('error_message'))
            <div style="padding: 10px 20px 0 20px;">
                <div class="alert alert-danger">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                    {{Session::get('error_message')}}
                </div>
            </div>
        @endif
        <div class="box box-solid" ng-controller="VDPController">
            <div class="box-header">
                <filter-template
                        show-item="['range','unit','thana']"
                        type="all"
                        range-change="loadPage()"
                        unit-change="loadPage()"
                        thana-change="loadPage()"
                        data="param"
                        start-load="range"
                        on-load="loadPage()"
                        field-width="{range:'col-sm-4',unit:'col-sm-4',thana:'col-sm-4'}"
                >

                </filter-template>
            </div>
            <div class="box-body">
                <div class="overlay" ng-if="allLoading">
                    <span class="fa">
                        <i class="fa fa-refresh fa-spin"></i> <b>Loading...</b>
                    </span>
                </div>

                <div ng-bind-html="vdpList" compile-html>

                </div>
            </div>
        </div>
    </section>

@endsection