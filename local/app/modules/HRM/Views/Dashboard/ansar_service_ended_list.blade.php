{{--User: Shreya--}}
{{--Date: 12/24/2015--}}
{{--Time: 12:52 PM--}}

@extends('template.master')
@section('title','Total number of Ansars who will complete 3 years of service within the next 2 months')
{{--@section('small_title','Total number of Ansars who will complete 3 years of service within the next 2 months')--}}
@section('breadcrumb')
    {!! Breadcrumbs::render('dashboard_menu_service_ended_2_month',$total) !!}
@endsection
@section('content')
    <script>
        GlobalApp.controller('AnsarServiceEndedListController', function ($scope, $http,$sce,httpService) {
            $scope.total = 0;
            $scope.numOfPage = 0;
            $scope.selectedDistrict = "all";
            $scope.user_type = parseInt("{{auth()->user()->type}}")
            $scope.selectedThana = "all"
            $scope.interval = "2";
            $scope.districts = [];
            $scope.thanas = [];
            $scope.itemPerPage = parseInt('{{config('app.item_per_page')}}')
            $scope.currentPage = 0;
            $scope.ansars = $sce.trustAsHtml("");
            $scope.pages = [];
            $scope.loadingDistrict = false;
            $scope.loadingThana = false;
            $scope.loadingPage = [];
            $scope.selectedDivision = 'all'
            $scope.allLoading = true;
            $scope.loadPagination = function(){
                $scope.pages = [];
                for (var i = 0; i < $scope.numOfPage; i++) {
                    $scope.pages.push({
                        pageNum: i,
                        offset: i * $scope.itemPerPage,
                        limit: $scope.itemPerPage
                    })
                    $scope.loadingPage[i]=false;
                }
                if($scope.numOfPage>0)$scope.loadPage($scope.pages[0]);
                else $scope.loadPage({pageNum:0,offset:0,limit:$scope.itemPerPage,view:'view'});
            }
            $scope.loadPage = function (page,$event) {
                if($event!=undefined)  $event.preventDefault();
                $scope.currentPage = page.pageNum;
                $scope.loadingPage[page.pageNum]=true;
                $scope.allLoading = true;
                $http({
                    url: '{{URL::route('service_ended_info_details')}}',
                    method: 'get',
                    params: {
                        offset: page.offset,
                        limit: page.limit,
                        unit:$scope.selectedDistrict,
                        thana:$scope.selectedThana,
                        interval:$scope.interval,
                        division:$scope.selectedDivision,
                        view:'view'
                    }
                }).then(function (response) {
                    $scope.ansars = response.data

                    $scope.loadingPage[page.pageNum]=false;
                    $scope.allLoading = false;
                })
            }
            $scope.loadTotal = function () {
                $scope.allLoading = true;
                $http({
                    url: '{{URL::route('service_ended_info_details')}}',
                    method: 'get',
                    params: {
                        unit:$scope.selectedDistrict,
                        thana:$scope.selectedThana,
                        interval:$scope.interval,
                        division:$scope.selectedDivision,
                        view:'count'
                    }
                }).then(function (response) {
                    $scope.total = sum(response.data.total);
                    $scope.gCount = response.data.total;
                    $scope.numOfPage = Math.ceil($scope.total/$scope.itemPerPage);
                    $scope.loadPagination();
                }, function (response) {
                    $scope.total = 0;
                    $scope.ansars = $sce.trustAsHtml("<tr class='warning'><td colspan='"+$('.table').find('tr').find('th').length+"'>"+response.data+"</td></tr>");
                    $scope.allLoading = false;
                    $scope.pages = [];
                })
            }
            $scope.filterMiddlePage = function (value, index, array) {
                var minPage = $scope.currentPage-3<0?0:($scope.currentPage>array.length-4?array.length-8:$scope.currentPage-3);
                var maxPage = minPage+7;
                if (value.pageNum >= minPage && value.pageNum <= maxPage) {
                    return true;
                }
            }
            $scope.loadDivision = function () {
                httpService.range().then(function (result) {
                    $scope.divisions = result;
                })
            }
            $scope.loadUnit = function (id) {
                $scope.loadingDistrict = true;
                httpService.unit(id).then(function (data) {
                    $scope.districts = data;
                    $scope.selectedDistrict = 'all'
                    $scope.thanas = [];
                    $scope.loadingDistrict = false;
                    $scope.loadTotal();
                })
            }
            $scope.loadThana = function (d_id) {
                $scope.loadingThana = true;
                $scope.allLoading = true;
                httpService.thana(d_id).then(function (data) {
                    $scope.thanas = data;
                    $scope.selectedThana = "all";
                    $scope.loadingThana = false;
                    $scope.loadTotal()
                })
            }
            if($scope.user_type==11||$scope.user_type==33){
                $scope.loadDivision();
            }
            else if($scope.user_type==66){
                $scope.loadUnit(parseInt('{{Auth::user()->division_id}}'))
            }
            else if($scope.user_type==22){
                $scope.loadThana(parseInt('{{Auth::user()->district_id}}'))
            }
            $scope.loadTotal()
            function sum(t){
                var s = 0;
                for(var i in t){
                    s += t[i]
                }
                return s;
            }
        })
    </script>
    <div ng-controller="AnsarServiceEndedListController">
        <section class="content">
            <div class="box box-solid">
                <div class="overlay" ng-if="allLoading">
                    <span class="fa">
                        <i class="fa fa-refresh fa-spin"></i> <b>Loading...</b>
                    </span>
                </div>
                <div class="box-body">
                    <div class="row">
                        <div class="col-sm-3" ng-show="user_type==11||user_type==33">
                            <div class="form-group">
                                <label class="control-label">@lang('title.range')&nbsp;
                                    <img ng-show="loadingDivision" src="{{asset('dist/img/facebook.gif')}}"
                                         width="16"></label>
                                <select class="form-control" ng-model="selectedDivision" ng-change="loadUnit(selectedDivision)">
                                    <option value="all">All</option>
                                    <option ng-repeat="d in divisions" value="[[d.id]]">[[d.division_name_bng]]</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-3" ng-show="user_type==11||user_type==33||user_type==66">
                            <div class="form-group">
                                <label class="control-label">@lang('title.unit')&nbsp;
                                    <img ng-show="loadingDistrict" src="{{asset('dist/img/facebook.gif')}}"
                                         width="16"></label>
                                <select class="form-control" ng-model="selectedDistrict" ng-change="loadThana(selectedDistrict)">
                                    <option value="all">All</option>
                                    <option ng-repeat="d in districts" value="[[d.id]]">[[d.unit_name_bng]]</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label class="control-label">@lang('title.thana')&nbsp;
                                    <img ng-show="loadingThana" src="{{asset('dist/img/facebook.gif')}}"
                                         width="16">
                                </label>
                                <select class="form-control" ng-model="selectedThana" ng-change="loadTotal()">
                                    <option value="all">All</option>
                                    <option ng-repeat="t in thanas" value="[[t.id]]">[[t.thana_name_bng]]</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label class="control-label">Select Month&nbsp;
                                </label>
                                <select class="form-control" ng-model="interval" ng-change="loadTotal()">
                                    <option value="1">1 Months</option>
                                    <option value="2">2 Months</option>
                                    <option value="3">3 Months</option>
                                    <option value="4">4 Months</option>
                                    <option value="5">5 Months</option>
                                    <option value="6">6 Months</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <h4 class="text text-bold">Total Ansars :PC([[gCount.PC!=undefined?gCount.PC.toLocaleString():0]])&nbsp;APC([[gCount.APC!=undefined?gCount.APC.toLocaleString():0]])&nbsp;Ansar([[gCount.ANSAR!=undefined?gCount.ANSAR.toLocaleString():0]])</h4>
                    <div class="table-responsive">
                        <template-list data="ansars" key="selected_service_ended_ansar"></template-list>
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
                                    <a href="#" ng-click="loadPage(page,$event)" ng-hide="currentPage == page.pageNum||loadingPage[page.pageNum]">[[page.pageNum+1]]</a>
                                    <span ng-show="loadingPage[page.pageNum]"  style="position: relative"><i class="fa fa-spinner fa-pulse" style="position: absolute;top:10px;left: 50%;margin-left: -9px"></i>[[page.pageNum+1]]</span>
                                </li>
                                <li ng-class="{disabled:currentPage==pages.length-1}">
                                    <a href="#" ng-click="loadPage(pages[currentPage+1],$event)">&raquo;</a>
                                </li>
                                <li ng-class="{disabled:currentPage==pages.length-1}">
                                    <a href="#" ng-click="loadPage(pages[pages.length-1],$event)">&raquo;&raquo;</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@stop