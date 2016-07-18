{{--User: Shreya--}}
{{--Date: 12/3/2015--}}
{{--Time: 12:34 PM--}}
@extends('template.master')
@section('title','Thana Information')
@section('small_title')
    <a style="background: #3c8dbc; color: #FFFFFF;" class="btn btn-primary btn-sm" href="{{URL::to('HRM/thana_form')}}">
        <span class="glyphicon glyphicon-plus"></span> Add New Thana
    </a>

@endsection
@section('breadcrumb')
    {!! Breadcrumbs::render('thana_information_list') !!}
@endsection
@section('content')
    <script>
        GlobalApp.controller('ThanaViewController', function ($scope, $http, $sce, $compile) {
            $scope.total = 0;
            $scope.numOfPage = 0;
            $scope.selectedDistrict = "all";
            $scope.selectedDivision = "all";
            $scope.isLoading = false;
            $scope.districts = [];
            $scope.division = [];
            $scope.thanas = [];
            $scope.itemPerPage = 20;
            $scope.currentPage = 0;
            $scope.pages = [];
            $scope.loadingDistrict = false;
            $scope.loadingDivision = true;
            $scope.loadingPage = [];
            $scope.loadPagination = function () {
                $scope.pages = [];
                for (var i = 0; i < $scope.numOfPage; i++) {
                    $scope.pages.push({
                        pageNum: i,
                        offset: i * $scope.itemPerPage,
                        limit: $scope.itemPerPage
                    })
                    $scope.loadingPage[i] = false;
                }
                if ($scope.numOfPage > 0)$scope.loadPage($scope.pages[0]);
                else $scope.loadPage({pageNum: 0, offset: 0, limit: $scope.itemPerPage, view: 'view'});
            }
            $scope.loadPage = function (page, $event) {
                if ($event != undefined)  $event.preventDefault();
                $scope.currentPage = page.pageNum;
                $scope.loadingPage[page.pageNum] = true;
                $http({
                    url: '{{URL::to('HRM/thana_view_details')}}',
                    method: 'get',
                    params: {
                        offset: page.offset,
                        limit: page.limit,
                        division: $scope.selectedDivision,
                        unit: $scope.selectedDistrict,
                        view: 'view'
                    }
                }).then(function (response) {
                    $scope.thanas = response.data.thanas;
                    //console.log($scope.thanas)
//                    $compile($scope.ansars)
                    $scope.loadingPage[page.pageNum] = false;
                })
            }
            $scope.loadTotal = function () {
                $scope.isLoading = true;
                //alert($scope.selectedDivision)
                $http({

                    url: '{{URL::to('HRM/thana_view_details')}}',
                    method: 'get',
                    params: {
                        division: $scope.selectedDivision,
                        unit: $scope.selectedDistrict,
                        view: 'count'
                    }
                }).then(function (response) {

                    $scope.total = response.data.total;
                    $scope.numOfPage = Math.ceil($scope.total / $scope.itemPerPage);
                    $scope.loadPagination();
                    $scope.isLoading = false;
                    //alert($scope.total)
                })
            }
            $scope.filterMiddlePage = function (value, index, array) {
                var minPage = $scope.currentPage - 3 < 0 ? 0 : ($scope.currentPage > array.length - 4 ? array.length - 8 : $scope.currentPage - 3);
                var maxPage = minPage + 7;
                if (value.pageNum >= minPage && value.pageNum <= maxPage) {
                    return true;
                }
            }
            $http({
                method: 'get',
                url: '{{URL::to('HRM/DivisionName')}}'
//                params: {id: d_id}
            }).then(function (response) {
                $scope.division = response.data;
                $scope.loadingDivision = false;
                $scope.loadingDistrict = true;
            })

            $scope.loadDistrict = function (d_id) {

                $http({
                    method: 'get',
                    url: '{{URL::to('HRM/DistrictName')}}',
                    params: {id: d_id}
                }).then(function (response) {
                    $scope.districts = response.data;
                    $scope.selectedDistrict = "all";
                    $scope.loadingDistrict = false;
                    $scope.loadTotal()
                })
            }
            $scope.loadTotal()

        })
    </script>
    <div ng-controller="ThanaViewController">
        @if(Session::has('success_message'))
            <div style="padding: 10px 20px 0 20px;">
                <div class="alert alert-success">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                    <span class="glyphicon glyphicon-ok"></span> {{Session::get('success_message')}}
                </div>
            </div>
        @endif
        <div class="loading-report animated" ng-class="{fadeInDown:isLoading,fadeOutUp:!isLoading}">
            <img src="{{asset('dist/img/ring-alt.gif')}}" class="center-block">
            <h4>Loading...</h4>
        </div>
        <section class="content">
            <div class="box box-solid">
                <div class="nav-tabs-custom">
                    {{--<ul class="nav nav-tabs">
                        <li class="active">
                            <a> Thana Information</a>
                        </li>
                        <li>
                            <a style="background: #3c8dbc; color: #FFFFFF;" class="btn btn-primary btn-sm"
                               href="{{URL::to('HRM/thana_form')}}">
                                <span class="glyphicon glyphicon-plus"></span> Add New Thana
                            </a>
                            --}}{{--<a data-toggle="tab" href="#pc">Transfer Ansar</a>--}}{{--
                        </li>
                    </ul>--}}
                    <div class="tab-content">
                        <div class="tab-pane active">
                            <div class="row">
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label class="control-label">Select a Division&nbsp;
                                            <img ng-show="loadingDivision" src="{{asset('dist/img/facebook.gif')}}"
                                                 width="16"></label>
                                        <select class="form-control" ng-model="selectedDivision"
                                                ng-change="loadDistrict(selectedDivision)">
                                            <option value="all">All</option>
                                            <option ng-repeat="d in division" value="[[d.id]]">[[d.division_name_eng]]
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label class="control-label">Select a District&nbsp;
                                            <img ng-show="loadingThana" src="{{asset('dist/img/facebook.gif')}}"
                                                 width="16">
                                        </label>
                                        <select class="form-control" ng-model="selectedDistrict"
                                                ng-change="loadTotal(selectedDistrict)">
                                            <option value="all">All</option>
                                            <option ng-repeat="t in districts" value="[[t.id]]">[[t.unit_name_eng]]
                                            </option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <tr>
                                        <th>SL. No</th>
                                        <th>Thana Name</th>
                                        <th>Thana Name in Bangla</th>
                                        <th>Thana Code</th>
                                        <th>Unit Name</th>
                                        <th>Division Name</th>
                                        <th>Action</th>
                                    </tr>
                                    <tbody>
                                    <tr ng-if="thanas.length==0">
                                        <td colspan="8" class="warning no-ansar">
                                            No thana available to see
                                        </td>
                                    </tr>
                                    <tr ng-if="thanas.length>0" ng-repeat="a in thanas">
                                        <td>
                                            [[((currentPage)*itemPerPage)+$index+1]]
                                        </td>
                                        <td>
                                            [[a.thana_name_eng]]
                                        </td>
                                        <td>
                                            [[a.thana_name_bng]]
                                        </td>
                                        <td>
                                            [[a.thana_code]]
                                        </td>
                                        <td>
                                            [[a.unit_name_eng]]
                                        </td>
                                        <td>
                                            [[a.division_name_eng]]
                                        </td>

                                        <td>
                                            <div class="col-xs-1">
                                                <a href="{{URL::to('HRM/thana_edit/'.'[[a.id]]')}}"
                                                   class="btn btn-primary btn-xs" title="Edit"><span
                                                            class="glyphicon glyphicon-edit"></span></a>
                                            </div>
                                            <div class="col-xs-1">
                                                {{--<a href="{{URL::to('HRM/thana_delete/'.'[[a.id]]')}}"
                                                   class="btn btn-primary btn-xs" title="Delete" style="background: #a41a20; border-color: #80181E"><span
                                                            class="glyphicon glyphicon-trash"></span></a>--}}
                                            </div>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                                <div class="table_pagination" ng-if="pages.length>1">
                                    <ul class="pagination">
                                        <li ng-class="{disabled:currentPage == 0}">
                                            <a href="#" ng-click="loadPage(pages[0],$event)">&laquo;&laquo;</a>
                                        </li>
                                        <li ng-class="{disabled:currentPage == 0}">
                                            <a href="#" ng-click="loadPage(pages[currentPage-1],$event)">&laquo;</a>
                                        </li>
                                        <li ng-repeat="page in pages|filter:filterMiddlePage"
                                            ng-class="{active:page.pageNum==currentPage&&!loadingPage[page.pageNum],disabled:!loadingPage[page.pageNum]&&loadingPage[currentPage]}">
                                            <span ng-show="currentPage == page.pageNum&&!loadingPage[page.pageNum]">[[page.pageNum+1]]</span>
                                            <a href="#" ng-click="loadPage(page,$event)"
                                               ng-hide="currentPage == page.pageNum||loadingPage[page.pageNum]">[[page.pageNum+1]]</a>
                                            <span ng-show="loadingPage[page.pageNum]" style="position: relative"><i
                                                        class="fa fa-spinner fa-pulse"
                                                        style="position: absolute;top:10px;left: 50%;margin-left: -9px"></i>[[page.pageNum+1]]</span>
                                        </li>
                                        <li ng-class="{disabled:currentPage==pages.length-1}">
                                            <a href="#" ng-click="loadPage(pages[currentPage+1],$event)">&raquo;</a>
                                        </li>
                                        <li ng-class="{disabled:currentPage==pages.length-1}">
                                            <a href="#"
                                               ng-click="loadPage(pages[pages.length-1],$event)">&raquo;&raquo;</a>
                                        </li>
                                    </ul>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@stop