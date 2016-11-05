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
            $scope.rank = 'all'
            var p = $scope.ansarType.split('_');
            $scope.pageTitle = '';
            for(var i=0;i< p.length;i++){
                $scope.pageTitle += capitalizeLetter(p[i]);
                if(i< p.length-1)$scope.pageTitle += " ";
            }
            $scope.total = 0
            $scope.param = {};
            $scope.numOfPage = 0
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
                        unit:$scope.param.unit==undefined?'all':$scope.param.unit,
                        thana:$scope.param.thana==undefined?'all':$scope.param.thana,
                        division:$scope.param.range==undefined?'all':$scope.param.range,
                        view:'view',
                        rank:$scope.rank,
                    }
                }).then(function (response) {
                    console.log(response.data);
                    $scope.ansars = response.data;
                    $scope.loadingPage[page.pageNum]=false;
                    $scope.allLoading = false;
                })
            }
            $scope.loadTotal = function (param) {
                $scope.param = param;
                $scope.allLoading = true;
                $http({
                    url: '{{URL::to('HRM/get_ansar_list')}}',
                    method: 'get',
                    params: {
                        type: $scope.ansarType,
                        unit:param.unit,
                        thana:param.thana,
                        division:param.range,
                        view:'count',
                        rank:$scope.rank,
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
            $scope.changeRank = function (i) {
                $scope.rank = i;
                $scope.loadTotal($scope.param)
            }
//            $scope.loadTotal()
            function capitalizeLetter(s){
                return s.charAt(0).toUpperCase()+ s.slice(1);
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
                    <filter-template
                            show-item="['range','unit','thana']"
                            type="all"
                            range-change="loadTotal(param)"
                            unit-change="loadTotal(param)"
                            thana-change="loadTotal(param)"
                            range-load="loadTotal(param)"
                            start-load="range"
                            field-width="{range:'col-sm-4',unit:'col-sm-4',thana:'col-sm-4'}"
                    >

                    </filter-template>
                    <h4 class="text text-bold"><a href="#" ng-click="changeRank('all')" style="color:black">Total Ansars</a> :PC(<a href="#" ng-click="changeRank(3)">[[gCount.PC!=undefined?gCount.PC.toLocaleString():0]]</a>)&nbsp;APC(<a href="#" ng-click="changeRank(2)">[[gCount.APC!=undefined?gCount.APC.toLocaleString():0]]</a>)&nbsp;Ansar(<a href="#" ng-click="changeRank(1)">[[gCount.ANSAR!=undefined?gCount.ANSAR.toLocaleString():0]]</a>)</h4>
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