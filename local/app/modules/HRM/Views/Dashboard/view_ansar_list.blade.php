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
        GlobalApp.controller('AnsarListController', function ($scope, $http,$sce) {
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
//                    console.log(response.data);
                    $scope.ansars = $sce.trustAsHtml(response.data);
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
                    $scope.total = response.data.total;
                    $scope.numOfPage = Math.ceil($scope.total/$scope.itemPerPage);
                    $scope.loadPagination();
                }, function (response) {
                    $scope.total = 0;
                    $scope.ansars = $sce.trustAsHtml(response.data);
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
            $http({
                method:'get',
                url:'{{URL::to('HRM/DistrictName')}}'
            }).then(function (response) {
                $scope.districts = response.data;
                $scope.loadingDistrict = false;
            })
            $scope.loadThana = function (d_id) {
                $scope.loadingThana = true;
                $scope.allLoading = true;
                $http({
                    method: 'get',
                    url: '{{URL::to('HRM/ThanaName')}}',
                    params: {id: d_id}
                }).then(function (response) {
                    $scope.thanas = response.data;
                    $scope.selectedThana = "all";
                    $scope.loadingThana = false;
                    $scope.loadTotal()
                })
            }
            $scope.loadTotal()
            function capitalizeLetter(s){
                return s.charAt(0).toUpperCase()+ s.slice(1);
            }
        })
    </script>

    <div ng-controller="AnsarListController" style="position: relative;">
        <div style="position:absolute;width: 100%;height: 100%;background-color: rgba(255, 255, 255, 0.27);z-index: 100" ng-show="allLoading">
            <div style="position: relative;width: 20%;height: auto;margin: 20% auto;text-align: center;background: #FFFFFF">
                <img class="img-responsive" src="{{asset('dist/img/loading-data.gif')}}" style="position: relative;margin: 0 auto">
                <h4>Loading....</h4>
            </div>

        </div>
        <section class="content">
            <div class="box box-solid">
                <div class="box-body">
                    <div class="row">
                        <div class="col-sm-4" ng-show="!isDc">
                            <div class="form-group">
                                <label class="control-label">Select a unit&nbsp;
                                    <img ng-show="loadingDistrict" src="{{asset('dist/img/facebook.gif')}}"
                                         width="16"></label>
                                <select class="form-control" ng-model="selectedDistrict" ng-change="loadThana(selectedDistrict)">
                                    <option value="all">All</option>
                                    <option ng-repeat="d in districts" value="[[d.id]]">[[d.unit_name_bng]]</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-4" ng-show="!isDc">
                            <div class="form-group">
                                <label class="control-label">Select a Thana&nbsp;
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
                    <h4>Total Ansar :[[total]]</h4>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <tr>
                                <th>SL. No</th>
                                <th>Ansar id</th>
                                <th>Rank</th>
                                <th>Name</th>
                                <th>Birth Date</th>
                                <th>Home District</th>
                                <th>Thana</th>
                                <th ng-if="ansarType=='paneled_ansar'">Panel Date & Time</th>
                                <th ng-if="ansarType=='paneled_ansar'">	Panel Id</th>
                                <th ng-if="ansarType=='embodied_ansar'||ansarType=='own_embodied_ansar'||ansarType=='embodied_ansar_in_different_district'">Kpi Name</th>
                                <th ng-if="ansarType=='embodied_ansar'||ansarType=='own_embodied_ansar'||ansarType=='embodied_ansar_in_different_district'">Embodiment Date</th>
                                <th ng-if="ansarType=='embodied_ansar'||ansarType=='own_embodied_ansar'||ansarType=='embodied_ansar_in_different_district'">Embodiment Id</th>
                                <th ng-if="ansarType=='offerred_ansar'">Offer Date & Time</th>
                                <th ng-if="ansarType=='rest_ansar'">Rest Date</th>
                                <th ng-if="ansarType=='freezed_ansar'">Freeze Reason</th>
                                <th ng-if="ansarType=='freezed_ansar'">Freeze Date</th>
                                <th ng-if="ansarType=='blocked_ansar'">Block Reason</th>
                                <th ng-if="ansarType=='blocked_ansar'">Block Date</th>
                                <th ng-if="ansarType=='blacked_ansar'">Black Reason</th>
                                <th ng-if="ansarType=='blacked_ansar'">Black Date</th>

                            </tr>
                            <tbody ng-bind-html="ansars" style="border:none">
                            <tr>
                                <td class="warning" colspan="7">No Ansar Found</td>
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