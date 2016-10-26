@extends('template.master')
@section('title',$pageTitle)
{{--@section('small_title',ucfirst(implode(' ',explode('_',$type))))--}}
{{--@section('small_title', $pageTitle)--}}
@section('breadcrumb')
{{--    {!! Breadcrumbs::render('dashboard_menu',ucwords(implode(' ',explode('_',$type))),$type) !!}--}}
    {!! Breadcrumbs::render('dashboard_menu', $pageTitle, $type) !!}
@endsection
@section('content')
    <script>
        GlobalApp.controller('AnsarListController', function ($scope, $http,$sce,httpService) {
           $scope.ansarType = '{{$type}}';
            $scope.user_type = parseInt("{{auth()->user()->type}}")
            $scope.isDc = false;
            if($scope.user_type==22){
                $scope.isDc = true;
                $scope.selectedDistrict = parseInt("{{auth()->user()->district_id}}");
            }
            var p = $scope.ansarType.split('_');
            $scope.pageTitle = '';
            for(var i=0;i< p.length;i++){
                $scope.pageTitle += capitalizeLetter(p[i]);
                if(i< p.length-1)$scope.pageTitle += " ";
            }
            $scope.total = 0
            $scope.numOfPage = 0
            $scope.selectedDistrict = "all";
            $scope.selectedThana = "all";
            $scope.districts = [];
            $scope.thanas = [];
            $scope.itemPerPage = parseInt("{{config('app.item_per_page')}}");
            $scope.currentPage = 0;
            $scope.ansars = $sce.trustAsHtml("");
            $scope.pages = [];
            $scope.loadingDistrict = true;
            $scope.loadingThana = false;
            $scope.loadingPage = []
            $scope.allLoading = true;

//Start pagination
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
                    url: '{{URL::to('HRM/get_ansar_list')}}',
                    method: 'get',
                    params: {
                        type: $scope.ansarType,
                        offset: page.offset,
                        limit: page.limit,
                        unit:$scope.selectedDistrict,
                        thana:$scope.selectedThana,
                        view:'view'
                    }
                }).then(function (response) {
                    console.log(response.data);
                    $scope.ansars = response.data;
                    $scope.loadingPage[page.pageNum]=false;
                    $scope.allLoading = false;
                })
            }
            $scope.loadTotal = function () {
                $scope.allLoading = true;
                $http({
                    url: '{{URL::to('HRM/get_ansar_list')}}',
                    method: 'get',
                    params: {
                        type: $scope.ansarType,
                        unit:$scope.selectedDistrict,
                        thana:$scope.selectedThana,
                        view:'count'
                    }
                }).then(function (response) {
                    $scope.total = sum(response.data.total);
                    $scope.gCount = response.data.total
//                    sum($scope.total)
                    $scope.numOfPage = Math.ceil($scope.total/$scope.itemPerPage);
                    $scope.loadPagination();
                }, function (response) {
                    $scope.total = 0;
                    $scope.ansars = $sce.trustAsHtml("<tr class='warning'><td colspan='"+$('.table').find('tr').find('th').length+"'>"+response.data+"</td></tr>");
                    //alert($(".table").html())
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
//End pagination

            $scope.loadUnit = function () {
                httpService.unit().then(function (data) {
                    $scope.districts = data;
                    $scope.loadingDistrict = false;
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
            if($scope.isDc){
                $scope.loadThana(parseInt('{{Auth::user()->district_id}}'))
            }
            else{
                $scope.loadUnit();
            }
            $scope.loadTotal()
            function capitalizeLetter(s){
                return s.charAt(0).toUpperCase()+ s.slice(1);
            }
            $scope.formatDate = function (date) {
                return moment(date).format("Do MM,YYYY");
            }
            function sum(t){
                var s = 0;
                for(var i in t){
                    s += t[i]
                }
                return s;
            }
        })
    </script>

    <div ng-controller="AnsarListController" style="position: relative;">
        <section class="content">
            <div class="box box-solid">
                <div class="overlay" ng-if="allLoading">
                    <span class="fa">
                        <i class="fa fa-refresh fa-spin"></i> <b>Loading...</b>
                    </span>
                </div>
                <div class="box-body">
                    <div class="row">
                        <div class="col-sm-4" ng-show="!isDc">
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
                        <div class="col-sm-4">
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
                    </div>
                    <h4 class="text text-bold">Total Ansars :PC([[gCount.PC!=undefined?gCount.PC.toLocaleString():0]])&nbsp;APC([[gCount.APC!=undefined?gCount.APC.toLocaleString():0]])&nbsp;ANSAR([[gCount.ANSAR!=undefined?gCount.ANSAR.toLocaleString():0]])</h4>
                    <div class="table-responsive">
                        <template-list data="ansars" key="{{$type}}"></template-list>
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