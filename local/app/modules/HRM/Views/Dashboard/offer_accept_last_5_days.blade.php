@extends('template.master')
@section('title','Total number of Ansars who accept the offer within last 5 days')
{{--@section('small_title',ucfirst(implode(' ',explode('_',$type))))--}}
{{--@section('small_title', $pageTitle)--}}
@section('breadcrumb')
{{--    {!! Breadcrumbs::render('dashboard_menu',ucwords(implode(' ',explode('_',$type))),$type) !!}--}}
    {!! Breadcrumbs::render('toal5') !!}
@endsection
@section('content')
    <script>
        GlobalApp.controller('AnsarListController', function ($scope, $http,$sce,httpService) {
           $scope.ansarType = 'offerred_ansar';
            $scope.genders = [
                {
                    text:'Male',
                    value:'Male'
                },
                {
                    text:'Female',
                    value:'Female'
                },
                {
                    text:'Other',
                    value:'Other'
                }
            ]
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
            $scope.queue = [];
            $scope.selectedDistrict = "all";
            $scope.selectedThana = "all";
            $scope.gender = 'all'
            $scope.gCount = {};
            $scope.rank = 'all'
            $scope.districts = [];
            $scope.thanas = [];
            $scope.itemPerPage = parseInt("{{config('app.item_per_page')}}");
            $scope.currentPage = 0;
            $scope.ansars = $sce.trustAsHtml("");
            $scope.pages = [];
            $scope.loadingDistrict = false;
            $scope.loadingThana = false;
            $scope.loadingPage = []
            $scope.allLoading = true;
            $scope.selectedDivision = 'all'
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
            }
            $scope.loadPage = function (page,$event) {
                if($event!=undefined)  $event.preventDefault();
                $scope.currentPage = page==undefined?0:page.pageNum;
                $scope.loadingPage[$scope.currentPage]=true;
                $scope.allLoading = true;
                $http({
                    url: '{{URL::to('HRM/offer_accept_last_5_day_data')}}',
                    method: 'get',
                    params: {
                        offset: page==undefined?0:page.offset,
                        limit: page==undefined?$scope.itemPerPage:page.limit,
                        q: $scope.q,
                        type:'view',
                        unit:$scope.selectedDistrict,
                        thana:$scope.selectedThana,
                        division:$scope.selectedDivision,
                        rank:$scope.rank,
                        sex:$scope.gender
                    }
                }).then(function (response) {
                    console.log(response.data);
                    $scope.ansars = response.data;
                    $scope.queue.shift()
                    $scope.loadingPage[$scope.currentPage]=false;
                    $scope.allLoading = false;
                    $scope.total = sum(response.data.total);
//                    alert($scope.total)
                    if($scope.queue.length>1) $scope.loadPage();
                    $scope.numOfPage = Math.ceil($scope.total/$scope.itemPerPage);
                    $scope.loadPagination();
                })
            }
            {{--$scope.loadTotal = function () {--}}
{{--//                alert($scope.selectedDistrict)--}}
                {{--$scope.allLoading = true;--}}
                {{--$http({--}}
                    {{--url: '{{URL::to('HRM/offer_accept_last_5_day_data')}}',--}}
                    {{--method: 'get',--}}
                    {{--params: {--}}
                        {{--type: 'count',--}}
                        {{--unit:$scope.selectedDistrict,--}}
                        {{--thana:$scope.selectedThana,--}}
                        {{--division:$scope.selectedDivision,--}}
                        {{--rank:$scope.rank,--}}
                        {{--sex:$scope.gender--}}
                    {{--}--}}
                {{--}).then(function (response) {--}}
                    {{--$scope.total = sum(response.data);--}}
{{--//                    alert($scope.total)--}}
                    {{--$scope.numOfPage = Math.ceil($scope.total/$scope.itemPerPage);--}}
                    {{--$scope.loadPagination();--}}
                {{--}, function (response) {--}}
                    {{--$scope.total = 0;--}}
                    {{--$scope.ansars = $sce.trustAsHtml("<tr class='warning'><td colspan='"+$('.table').find('tr').find('th').length+"'>"+response.data+"</td></tr>");--}}
                    {{--//alert($(".table").html())--}}
                    {{--$scope.allLoading = false;--}}
                    {{--$scope.pages = [];--}}
                {{--})--}}
            {{--}--}}
            $scope.filterMiddlePage = function (value, index, array) {
                var minPage = $scope.currentPage-3<0?0:($scope.currentPage>array.length-4?array.length-8:$scope.currentPage-3);
                var maxPage = minPage+7;
                if (value.pageNum >= minPage && value.pageNum <= maxPage) {
                        return true;
                }
            }
//End pagination
            $scope.loadDivision = function () {
                httpService.range().then(function (result) {
                    $scope.divisions = result;
                })
            }
            $scope.loadUnit = function (id) {
                $scope.loadingDistrict = true;
                httpService.unit(id).then(function (data) {
                    $scope.districts = data;
                    $scope.thanas = [];
                    $scope.loadingDistrict = false;
                    $scope.loadPage();
                })
            }
            $scope.loadThana = function (d_id) {
                $scope.loadingThana = true;
                $scope.allLoading = true;
                httpService.thana(d_id).then(function (data) {
                    $scope.thanas = data;
                    $scope.selectedThana = "all";
                    $scope.loadingThana = false;
                    $scope.loadPage()
                })
            }
            httpService.rank().then(function (ranks) {
                $scope.ranks = ranks;
            })
            if($scope.user_type==11||$scope.user_type==33){
                $scope.loadDivision();
            }
            else if($scope.user_type==66){
                $scope.loadUnit(parseInt('{{Auth::user()->division_id}}'))
            }
            else if($scope.user_type==22){
                $scope.loadThana(parseInt('{{Auth::user()->district_id}}'))
            }
            $scope.loadPage()
            function capitalizeLetter(s){
                return s.charAt(0).toUpperCase()+ s.slice(1);
            }
            $scope.formatDate = function (date) {
                return moment(date).format("Do MM,YYYY");
            }
            function sum(t){
                var s = 0;
                for(var i in t){
                    $scope.gCount[i] = 0;
                    for(var j =0 ;j<t[i].length;j++){
                        s += t[i][j].total;
                        $scope.gCount[i] += t[i][j].total;
                    }
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
                        <div class="col-md-3 col-sm-4 col-xs-12" ng-show="user_type==11||user_type==33">
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
                        <div class="col-md-3 col-sm-4 col-xs-12" ng-show="user_type==11||user_type==66||user_type==33">
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
                        <div class="col-md-2 col-sm-4 col-xs-12">
                            <div class="form-group">
                                <label class="control-label">@lang('title.thana')&nbsp;
                                    <img ng-show="loadingThana" src="{{asset('dist/img/facebook.gif')}}"
                                         width="16">
                                </label>
                                <select class="form-control" ng-model="selectedThana" ng-change="loadPage()">
                                    <option value="all">All</option>
                                    <option ng-repeat="t in thanas" value="[[t.id]]">[[t.thana_name_bng]]</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2 col-sm-4 col-xs-12">
                            <div class="form-group">
                                <label class="control-label">@lang('title.sex')
                                </label>
                                <select class="form-control" ng-model="gender" ng-change="loadPage()">
                                    <option value="all">All</option>
                                    <option ng-repeat="t in genders" value="[[t.value]]">[[t.text]]</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2 col-sm-4 col-xs-12">
                            <div class="form-group">
                                <label class="control-label">@lang('title.rank')
                                </label>
                                <select class="form-control" ng-model="rank" ng-change="loadPage()">
                                    <option value="all">All</option>
                                    <option ng-repeat="t in ranks" value="[[t.id]]">[[t.name_eng]]</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-8">
                            <h4 class="text text-bold">Total Ansars :PC([[gCount.PC!=undefined?gCount.PC.toLocaleString():0]])&nbsp;APC([[gCount.APC!=undefined?gCount.APC.toLocaleString():0]])&nbsp;Ansar([[gCount.ANSAR!=undefined?gCount.ANSAR.toLocaleString():0]])</h4>
                        </div>
                        <div class="col-md-4">
                            <database-search q="q" queue="queue" on-change="loadPage()"></database-search>

                        </div>
                    </div>
                    <div class="table-responsive">
                        <template-list data="ansars" key="offerred_ansar_accept_last_5_days"></template-list>
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