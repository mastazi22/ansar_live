@extends('template.master')
@section('title','Offer')
@section('breadcrumb')
    {!! Breadcrumbs::render('offer_information') !!}
@endsection
@section('content')
    <script>

        GlobalApp.controller("OfferBlockController",function ($scope, $http, $sce,notificationService) {
            var emptyTemplate = `<div class="table-responsive">
                        <table class="table table-bordered table-condensed">
                            <caption>
                                <div class="form-group">
                                    <input type="text" class="form-control" placeholder="search by Ansar ID" ng-model="param.ansar_id" ng-keypress="$event.keyCode==13?loadData():''">
                                </div>
                            </caption>
                            <tr>
                                <th>Sl. No</th>
                                <th>Ansar ID</th>
                                <th>Name</th>
                                <th>Rank</th>
                                <th>Home Division</th>
                                <th>Home District</th>
                                <th>Last Offer District</th>
                                <th>Block Date</th>
                                <th>Action</th>
                            </tr>
                            <tr>
                                <td colspan="9" class="bg-warning">
                                    No Ansar Available
                                </td>
                            </tr>
                        </table>
                    </div>`
            $scope.param = {}
            $scope.template = $sce.trustAsHtml(emptyTemplate);
            $scope.loadData = function (url) {
                console.log($scope.param);
                $scope.allLoading = true;
                $http({
                    method:'get',
                    url:url||'{{URL::route('HRM.offer_rollback.index')}}',
                    params:$scope.param
                }).then(function (response) {
                    $scope.template = $sce.trustAsHtml(response.data);
                    $scope.allLoading = false;
                },function (response) {
                    $scope.template = $sce.trustAsHtml(emptyTemplate);
                    $scope.allLoading = false;
                })
            }
            $scope.clearSearch = function (event) {
                $scope.param = {};
                $scope.loadData();
            }
            $scope.rollback = function (id) {
                $scope.param['_method'] = 'patch';
                $scope.param['type'] = 'rollback';
                $scope.updateStatus(id);
            }
            $scope.sendToPanel = function (id) {
                $scope.param['_method'] = 'patch';
                $scope.param['type'] = 'sendtopanel';
                $scope.updateStatus(id);
            }
            $scope.updateStatus = function (id) {
                $scope.allLoading = true;
                $http({
                    method:'post',
                    data:$scope.param,
                    url:'{{URL::to('/HRM/offer_rollback')}}/'+id
                }).then(function (response) {
                    if(response.data.status){
                        notificationService.notify("success",response.data.message);
                        $scope.loadData();
                    } else{
                        notificationService.notify("error",response.data.message);
                    }
                    $scope.allLoading = false;
                },function (response) {
                    $scope.allLoading = false;
                })
            }
            $scope.loadData();
        })
        GlobalApp.directive('compileHtml', function ($compile) {
            return {
                restrict: 'A',
                link: function (scope, elem, attr) {
                    var newscope;
                    scope.$watch('template', function (n) {

                        if (attr.ngBindHtml) {
                            if(newscope) newscope.$destroy();
                            newscope = scope.$new();
                            $compile(elem[0].children)(newscope);
                        }
                    })

                }
            }
        })
    </script>
    <div ng-controller="OfferBlockController">
        <section class="content">
            <div class="box box-solid">
                <div class="overlay" ng-if="allLoading">
                    <span class="fa">
                        <i class="fa fa-refresh fa-spin"></i> <b>Loading...</b>
                    </span>
                </div>
                <div class="box-body">
                    <div ng-bind-html="template" compile-html>

                    </div>
                </div>
            </div>
        </section>
    </div>
    <script>
    </script>
@stop