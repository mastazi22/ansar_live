@extends('template.master')
@section('title','KPI List')
@section('breadcrumb')
    {!! Breadcrumbs::render('entry.list') !!}
@endsection
@section('content')
    <script>
        GlobalApp.controller('OfferController', function ($scope, $http, $sce,notificationService) {
            $scope.param = {
                height:{
                    comparator:"="
                },
                age:{
                    comparator:"="
                },
                units:{
                }
            };
            $scope.selected = [];
            $scope.submitData = {};
            $scope.searchedVDP = $sce.trustAsHtml(`<table class="table table-bordered table-condensed">
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
                            <th>Height</th>
                            <th>Age</th>
                            <th>Action</th>
                        </tr>
                        <tr >
                            <td class="bg-warning" colspan="12">
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
                    url: url || '{{URL::route('AVURP.offer_info.index')}}',
                    method: 'get',
                    params: $scope.param
                }).then(function (response) {
                    $scope.allLoading = false;
                    $scope.searchedVDP = $sce.trustAsHtml(response.data);
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
                    url: '{{URL::route('AVURP.offer_info.select_all')}}',
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
            $scope.sendOffer = function () {
                $("#offerModel").modal("show")
            }
            $scope.confirmOffer = function () {
                $scope.allLoading = true;
                $scope.submitData["ids"] = $scope.selected.filter(function (v) {
                    return !!v;
                })
                console.log($scope.submitData);
                $http({
                    url:'{{URL::route("AVURP.offer_info.store")}}',
                    method:'post',
                    data:angular.toJson($scope.submitData)
                }).then(function (response) {
                    notificationService.notify(response.data.status,response.data.message)
                    $scope.allLoading = false;
                    $scope.loadPage();
                },function (response) {
                    notificationService.notify("error","An error occur while send offer. Error code: "+response.status);
                    $scope.allLoading = false;
                })
            }

        })
        GlobalApp.directive('compileHtml',function ($compile) {
            return {
                restrict:'A',
                link:function (scope,elem,attr) {
                    var newScope;
                    scope.$watch('searchedVDP', function (n) {

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
        <div class="box box-solid" ng-controller="OfferController">
            <div class="overlay" ng-if="allLoading">
                    <span class="fa">
                        <i class="fa fa-refresh fa-spin"></i> <b>Loading...</b>
                    </span>
            </div>
            <div class="box-header">
                {{--<filter-template
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

                </filter-template>--}}
                <div class="filter" data-visible="1">
                    <div class="header">
                        <span>Filter</span>
                        <button class="btn btn-primary pull-right" id="toggle-button">
                            <i class="fa fa-angle-up"></i>
                        </button>
                    </div>
                    <div class="body">
                        <div class="form-group">
                            <div class="row">
                                <div class="col-sm-2">
                                    <label class="control-label">Height : </label>
                                </div>
                                <div class="col-sm-3" style="padding-left: 0">
                                    <select class="form-control" ng-model="param.height.comparator">
                                        <option value="=">Equal</option>
                                        <option value=">">Greater Then</option>
                                        <option value=">=">Greater Then & Equal</option>
                                        <option value="<">Less Then</option>
                                        <option value="=<">Less Then & Equal</option>
                                    </select>
                                </div>
                                <div class="col-sm-4" style="padding-left: 0">
                                    <div class="row">
                                        <div class="col-sm-6" style="padding-right: 0" >
                                            <input type="text" class="form-control" placeholder="Feet" ng-model="param.height.value.feet">
                                        </div>
                                        <div class="col-sm-6">
                                            <input type="text"  ng-model="param.height.value.inch" class="form-control" placeholder="Inch">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-sm-2">
                                    <label class="control-label">Age : </label>
                                </div>
                                <div class="col-sm-3" style="padding-left: 0">
                                    <select class="form-control" ng-model="param.age.comparator">
                                        <option value="=">Equal</option>
                                        <option value=">">Greater Then</option>
                                        <option value=">=">Greater Then & Equal</option>
                                        <option value="<">Less Then</option>
                                        <option value="=<">Less Then & Equal</option>
                                    </select>
                                </div>
                                <div class="col-sm-4" style="padding-left: 0">
                                    <input type="text" class="form-control" ng-model="param.age.value" placeholder="age">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-sm-2">
                                    <label class="control-label">Select District : </label>
                                </div>
                                <div class="col-sm-4" style="padding-left: 0">
                                    <div style="height: 200px;width: 100%;border: 1px solid #ababab;overflow-y: scroll;overflow-x:hidden;padding: 5px 10px">
                                        <?php $i=0; ?>
                                        @foreach($units as $u)
                                            <span style="display: block">
                                                <input class="unit" ng-model="param.units.value[{{$i++}}]" type="checkbox" ng-true-value="'{{$u->id}}'"
                                                       ng-true-value="{{$u->id}}">&nbsp;{{$u->unit_name_bng}}
                                            </span>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group" style="overflow: hidden">
                            <button class="btn btn-primary pull-right" ng-click="loadPage()">
                                <i class="fa fa-search"></i>&nbsp;Search
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="box-body">


                <div class="container-fluid">
                    <div ng-bind-html="searchedVDP" compile-html>

                    </div>
                </div>
            </div>
            <div id="offerModel" class="modal fade" role="dialog">
                <div class="modal-dialog">

                    <!-- Modal content-->
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h4 class="modal-title">Modal Header</h4>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="message_box" class="control-label">Message(optional):</label>
                                <textarea ng-model="submitData.message" class="form-control" name="" id="" cols="30" rows="10"></textarea>
                            </div>
                            <div class="form-group">
                                <filter-template
                                        show-item="['range','unit']"
                                        type="single"
                                        data="submitData"
                                        layout-vertical="1"
                                        start-load="range"
                                >

                                </filter-template>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" ng-click="confirmOffer()" class="btn btn-primary" data-dismiss="modal">Send Offer</button>
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </section>
    <style>
        .filter {
            box-shadow: 1px 1px 1px 0 #cccccc;
        }

        .filter > .header {
            padding: 10px;
            overflow: hidden;
            font-weight: bold;
            border-bottom: 1px solid #cccccc6e;

        }

        .filter > .header > span {
            font-size: 18px;
            line-height: 34px;
        }

        .filter > .body {
            padding: 10px;
        }

        .filter > .body label {
            font-size: 15px;
            line-height: 1.9;
        }
    </style>
    <script>
        $(document).ready(function () {
            $("#toggle-button").on('click',function () {
                var filter = $(this).parents('.filter');
                var t = this;
//                alert(filter.attr('data-visible'))
                if(filter.attr('data-visible')==1){
                    filter.find(".body").slideUp(200,function () {
                        $(t).children('i').addClass("fa-angle-down").removeClass("fa-angle-up")
                        filter.attr('data-visible',0)
                    });

                } else{
                    filter.find(".body").slideDown(200,function () {
                        $(t).children('i').addClass("fa-angle-up").removeClass("fa-angle-down")
                        filter.attr('data-visible',1)
                    });
                }
            })
        })
    </script>
@endsection