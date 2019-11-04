@extends('template.master')
@section('title','Dashboard')
@section('breadcrumb')
    {!! Breadcrumbs::render('AVURP') !!}
@endsection
@section('content')
    <script>
        GlobalApp.controller('EmbodimentController', function ($scope, $http, $sce,notificationService) {

            $scope.selected = [];
            $scope.submitData = {};
            $scope.offeredVDP = $sce.trustAsHtml(`<table class="table table-bordered table-condensed">
                        <caption style="font-size: 16px">
                            <strong>VDP/Ansar List([[searchedAnsar.length]])</strong>
                        </caption>
                        <tr>
            <th>SL. No</th>
            <th>GEO ID</th>
            <th>Name</th>
            <th>Designation</th>
            <th>Division</th>
            <th>District</th>
            <th>Upazila/Thana</th>
            <th>Union</th>
            <th>Word</th>
            <th>Offer Date</th>
            <th>Action</th>
        </tr>
                        <tr >
                            <td class="bg-warning" colspan="11">
                                No VDP/Ansar found
                            </td>
                        </tr>
                    </table>`);
            $scope.allLoading = false;
            $scope.loadPage = function (url) {
                console.log($scope.param)
//                return;
                $scope.allLoading = true;
                $http({
                    url: url || '{{URL::route('AVURP.embodiment.index')}}',
                    method: 'get',
                    params: $scope.param
                }).then(function (response) {
                    $scope.allLoading = false;
                    $scope.offeredVDP = $sce.trustAsHtml(response.data);
                }, function (response) {
                    $scope.allLoading = false;
                })

            }
            $scope.addToSelection = function (id) {
                $scope.selected.push(id);
            }
            $scope.removeFromSelection = function (id) {
                var index = $scope.selected.indexOf(id);
                $scope.selected.splice(index,1);
            }
            $scope.selectAll = function () {
                $scope.allLoading = true;
                $http({
                    url: '{{URL::route('AVURP.embodiment.select_all')}}',
                    method: 'post',
                    data: $scope.param
                }).then(function (response) {
                    $scope.allLoading = false;
                    $scope.selected = response.data.ids.map(function (v) {
                        return v+"";
                    });
                }, function (response) {
                    $scope.allLoading = false;
                })
            }
            $scope.removeAll = function () {
                $scope.selected = [];
            }
            $scope.openEmbodimentModal = function () {
                $("#embodimentModel").modal("show")
            }
            $scope.confirmEmbodiment = function () {
                $scope.formSubmitting = true;
                $scope.submitData["ids"] = $scope.selected.filter(function (v) {
                    return !!v;
                })
                console.log($scope.submitData);
                $http({
                    url:'{{URL::route("AVURP.embodiment.store")}}',
                    method:'post',
                    data:angular.toJson($scope.submitData)
                }).then(function (response) {
                    notificationService.notify(response.data.status,response.data.message)
                    $scope.formSubmitting = false;
                    $scope.loadPage();
                    $("#embodimentModel").modal('hide')
                },function (response) {
                    if(response.status==422){
                        $scope.submitError = response.data;
                    }
                    else notificationService.notify("error","An error occur while send offer. Error code: "+response.status);
                    $scope.formSubmitting = false;
                })
            }
            @if(auth()->user()->type==22)
                $scope.loadPage();
            @endif

        })
        GlobalApp.directive('compileHtml',function ($compile) {
            return {
                restrict:'A',
                link:function (scope,elem,attr) {
                    var newScope;
                    scope.$watch('offeredVDP', function (n) {

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
    <div class="box box-solid" ng-controller="EmbodimentController">
        <div class="overlay" ng-if="allLoading">
                    <span class="fa">
                        <i class="fa fa-refresh fa-spin"></i> <b>Loading...</b>
                    </span>
        </div>
        <div class="box-header">
            <div class="container-fluid">
                <filter-template
                        show-item="['range','unit']"
                        type="single"
                        unit-change="loadPage()"
                        data="param"
                        start-load="range"
                        field-width="{range:'col-sm-4',unit:'col-sm-4'}"
                >

                </filter-template>
            </div>
        </div>
        <div class="box-body">


            <div class="container-fluid">
                <div ng-bind-html="offeredVDP" compile-html>

                </div>
            </div>
        </div>
        <div id="embodimentModel" class="modal fade" role="dialog">
            <div class="modal-dialog">

                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Embodiment</h4>
                    </div>
                    <div class="modal-body">

                        <div class="form-group">
                            <filter-template
                                    show-item="['range','unit','thana','short_kpi']"
                                    type="single"
                                    data="submitData"
                                    start-load="range"
                                    layout-vertical="1"
                            ></filter-template>
                            <span ng-if="submitError.shortKpi" class="text-danger text-bold">[[submitError.shortKpi[0] ]]</span>
                        </div>
                        <div class="form-group">
                            <label for="join_date" class="control-label">Embodiment Date(<span class="text-danger">required</span>):</label>
                            <input id="join_date" placeholder="embodiment date" type="text" ng-model="submitData.joining_date" class="form-control" date-picker/>
                            <span ng-if="submitError.joining_date" class="text-danger text-bold">[[submitError.joining_date[0] ]]</span>
                        </div>
                        <div class="form-group">
                            <label for="duration" class="control-label">Duration of embodiment(in days)(<span class="text-danger">required</span>):</label>
                            <input id="duration" placeholder="embodiment date" type="text" ng-model="submitData.duration" class="form-control" />
                            <span ng-if="submitError.duration" class="text-danger text-bold">[[submitError.duration[0] ]]</span>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-sm-6">
                                    <label for="mem_id" class="control-label">Memorandum ID(<span class="text-danger">required</span>):</label>
                                    <input id="mem_id" placeholder="Mem ID" type="text" ng-model="submitData.mem.mem_id" class="form-control"/>
                                    <span ng-if="submitError['mem.mem_id']" class="text-danger text-bold">[[submitError['mem.mem_id'][0] ]]</span>
                                </div>
                                <div class="col-sm-6">
                                    <label for="mem_date" class="control-label">Memorandum Date(<span class="text-danger">required</span>):</label>
                                    <input id="mem_date" placeholder="Mem Date" type="text" ng-model="submitData.mem.mem_date" class="form-control" date-picker/>
                                    <span ng-if="submitError['mem.mem_date']" class="text-danger text-bold">[[submitError['mem.mem_date'][0] ]]</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button ng-disabled="formSubmitting" type="button" ng-click="confirmEmbodiment()" class="btn btn-primary" >
                            <i ng-show="formSubmitting" class="fa fa-spinner fa-pulse"></i>&nbsp;Confirm Embodiment</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection