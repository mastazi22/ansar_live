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
            var p = $scope.ansarType.split('_');
            $scope.pageTitle = '';
            for(var i=0;i< p.length;i++){
                $scope.pageTitle += capitalizeLetter(p[i]);
                if(i< p.length-1)$scope.pageTitle += " ";
            }
            $scope.total = 0
            $scope.numOfPage = 0
            $scope.queue = [];
            $scope.gCount = {};
            $scope.itemPerPage = parseInt("{{config('app.item_per_page')}}");
            $scope.currentPage = 0;
            $scope.ansars = $sce.trustAsHtml("");
            $scope.pages = [];
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
                        unit:$scope.param.unit,
                        thana:$scope.param.thana,
                        division:$scope.param.range,
                        rank:$scope.param.rank,
                        sex:$scope.param.gender
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
            $scope.filterMiddlePage = function (value, index, array) {
                var minPage = $scope.currentPage-3<0?0:($scope.currentPage>array.length-4?array.length-8:$scope.currentPage-3);
                var maxPage = minPage+7;
                if (value.pageNum >= minPage && value.pageNum <= maxPage) {
                        return true;
                }
            }
            function capitalizeLetter(s){
                return s.charAt(0).toUpperCase()+ s.slice(1);
            }
            function sum(t){
                $scope.gCount = [];
                var s = 0;
                for(var i in t){
                    $scope.gCount[i] = 0;
                    for(var j =0 ;j<t[i].length;j++){
                        s += parseInt(t[i][j].total);
                        $scope.gCount[i] += parseInt(t[i][j].total);
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
                    <filter-template
                            show-item="['range','unit','thana','gender','rank']"
                            type="all"
                            range-change="loadPage()"
                            unit-change="loadPage()"
                            thana-change="loadPage()"
                            rank-change="loadPage()"
                            gender-change="loadPage()"
                            on-load="loadPage()"
                            data="param"
                            start-load="range"
                            field-width="{range:'col-md-3 col-sm-4 col-xs-12',unit:'col-md-3 col-sm-4 col-xs-12',thana:'col-md-2 col-sm-4 col-xs-12',rank:'col-md-2 col-sm-4 col-xs-12',gender:'col-md-2 col-sm-4 col-xs-12'}"
                    >

                    </filter-template>

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
                    </div>
                    <div class="row">
                        <div class="col-sm-4">
                            <label for="item_par_page">Show :</label>
                            <select name="item_per_page" ng-change="loadPage()" id="item_par_page"
                                    ng-model="itemPerPage">
                                <option value="20" ng-selected="true">20</option>
                                <option value="40">40</option>
                                <option value="60">60</option>
                                <option value="80">80</option>
                                <option value="100">100</option>
                                <option value="150">150</option>
                                <option value="200">200</option>
                            </select>
                        </div>
                        <div class="col-sm-8">
                            <div class="table_pagination" ng-if="pages.length>1">
                                <ul class="pagination" style="margin: 0">
                                    <li ng-class="{disabled:currentPage == 0}">
                                        <a href="#" ng-click="loadPage(pages[0],$event)">&laquo;&laquo;</a>
                                    </li>
                                    <li ng-class="{disabled:currentPage == 0}">
                                        <a href="#"
                                           ng-click="loadPage(pages[currentPage-1],$event)">&laquo;</a>
                                    </li>
                                    <li ng-repeat="page in pages|filter:filterMiddlePage"
                                        ng-class="{active:page.pageNum==currentPage&&!loadingPage[page.pageNum],disabled:!loadingPage[page.pageNum]&&loadingPage[currentPage]}">
                                        <span ng-show="currentPage == page.pageNum&&!loadingPage[page.pageNum]">[[page.pageNum+1]]</span>
                                        <a href="#" ng-click="loadPage(page,$event)"
                                           ng-hide="currentPage == page.pageNum||loadingPage[page.pageNum]">[[page.pageNum+1]]</a>
                                                    <span ng-show="loadingPage[page.pageNum]"
                                                          style="position: relative"><i class="fa fa-spinner fa-pulse"
                                                                                        style="position: absolute;top:10px;left: 50%;margin-left: -9px"></i>[[page.pageNum+1]]</span>
                                    </li>
                                    <li ng-class="{disabled:currentPage==pages.length-1}">
                                        <a href="#"
                                           ng-click="loadPage(pages[currentPage+1],$event)">&raquo;</a>
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
        </section>
    </div>
@stop