@extends('template/master')
@section('title','Entry List')
@section('small_title')
    <a href="{{URL::to('HRM/entryform')}}" class="btn btn-info btn-sm"><span
                class="glyphicon glyphicon-user"></span> Add New</a>
    @endsection
@section('breadcrumb')
    {!! Breadcrumbs::render('entry_list') !!}
    @endsection
@section('content')
    <script>
        GlobalApp.controller('AnsarController', function ($scope, $http) {
            $scope.AllAnsar = [];
            $scope.loadType = 0;
            $scope.userType = parseInt('{{Auth::user()->type}}');
            $scope.notVerified = parseInt("{{$notVerified}}");
            $scope.Verified = parseInt("{{$Verified}}");
            $scope.numOfPage = 0
            $scope.Item = 10;
            $scope.currentPage = 0;
            $scope.pages = [];
            $scope.isSearching = false;
            $scope.loading = false;
            $scope.rejecting = false;
            $scope.noFound = false;
            $scope.loadingPage = [];
            $scope.loadPagination = function() {
                $scope.pages = [];
                for (var i = 0; i < $scope.totalPages; i++) {
                    $scope.pages.push({
                        pageNum: i,
                        offset: i * $scope.Item,
                        limit: $scope.Item
                    })
                    $scope.loadingPage[i] = false;
                }
                if ($scope.numOfPage > 0)$scope.loadAnsar($scope.pages[0]);
                else $scope.loadAnsar({pageNum: 0, offset: 0, limit: $scope.Item});

            }
            //alert($scope.Verified + " " + $scope.notVerified);
            $scope.loadAnsar = function (page,$event) {
                if($event!=undefined)  $event.preventDefault();
                $scope.currentPage = page.pageNum;
                $scope.loadingPage[page.pageNum]=true
                console.log($scope.currentPage==page.pageNum)
                $scope.searchedAnsar = "";
                $scope.isSearching = false;
                $scope.loading = true;
                $scope.noFound = false;
                $http({
                    url: $scope.loadType == 0 ? "{{URL::to('HRM/getnotverifiedansar')}}" : "{{URL::to('HRM/getverifiedansar')}}",
                    method: 'get',
                    params: {limit: page.limit, offset: page.offset},
                  
                }).then(function (response) {
//                alert(JSON.stringify(response.data));
                    $scope.loading = false;
                    $scope.AllAnsar = response.data;
                    console.log($scope.AllAnsar)
                    $scope.loadingPage[page.pageNum]=false
                    if($scope.AllAnsar.length == 0)
                      $scope.noFound = true;
                })
            }
            $scope.$watch(function (scope) {
                return scope.loadType;
            }, function (newValue, oldValue) {
                $scope.verifying = [];
                $scope.verified = [];
                $scope.rejecting = [];
                $scope.pages = [];
                if (newValue == 0) {
                    $scope.totalPages = Math.ceil($scope.notVerified / $scope.Item);
                }
                else if (newValue == 1) {
                    $scope.totalPages = Math.ceil($scope.Verified / $scope.Item);
                }
                $scope.loadPagination();
                $scope.currentPage = 0;
            })

            $scope.verify = function (id, i) {
                $scope.noFound = false;
                $scope.verifying[i] = true;
                $http({
                    url: "{{URL::to('HRM/entryVerify/')}}",
                    data: {verified_id: id},
                    method: 'post'
                }).then(function (response) {
                    console.log(JSON.stringify(response.data));
                    if(response.data.status!=undefined&&response.data.status==false){
//                        alert(response.data.message);
                        $('body').notifyDialog({
                            type:'error',
                            message:response.data.message
                        }).showDialog()
                        $scope.verifying[i] = false;
                        return;
                    }
                    $scope.loadType =0;
                    //$scope.loadAnsar();
                    $scope.verifying[i] = false;
                    $scope.verified[i] = true;
                    $scope.notVerified--;
                    $scope.Verified++;
                    $scope.totalPages = Math.ceil($scope.notVerified / $scope.Item);
                    $scope.loadPagination();
//                    $scope.Verified++;
                },function () {
                    $scope.verifying[i] = false;
                    $scope.verified[i] = false;
                })
            }
            
            $scope.reject = function(id, i){
                $scope.noFound = false;
                $scope.rejecting[i] = true;
//                $scope.verified[i] = true;
                
//                return;
                $http({
                    url: "{{URL::to('HRM/reject')}}",
                    data: {reject_id: id},
                    method: 'post'
                }).then(function (response) {
                    //alert(JSON.stringify(response.data));
                    $scope.loadType =0;
                    $scope.rejecting[i] = false;
                    $scope.notVerified--;
                    $scope.totalPages = Math.ceil($scope.notVerified / $scope.Item);
                    $scope.loadPagination();
//                    $scope.verified[i] = true;
                    
//                    alert($scope.verified[i]);
                },function () {
                    $scope.rejecting[i] = false;
//                    alert($scope.verified[i]);
                })
            }

            $scope.filterMiddlePage = function (value, index, array) {
                var minPage = $scope.currentPage-3<0?0:($scope.currentPage>array.length-4?array.length-8:$scope.currentPage-3);
                var maxPage = minPage+7;
                if (value.pageNum >= minPage && value.pageNum <= maxPage) {
                    return true;
                }
            }
            $scope.searchId = function(keyEvent,id){
                $scope.noFound = false;
                if(keyEvent==null){
                    $scope.loading = true;
                    $scope.isSearching = true;
                    $http({
                        url : "{{URL::to('HRM/entrysearch')}}",
                        method: 'post',
                        data: {ansarId : id,type:$scope.loadType}
                    }).then(function(response){

                        $scope.loading = false;
                        $scope.AllAnsar = response.data;
                        $scope.noFound = $scope.AllAnsar.length<=0
                        //alert($scope.noFound)
                        console.log($scope.searchedAnsar);
                    })
                }
                else if (keyEvent.which === 13)
                {
                    $scope.loading = true;
                    $scope.isSearching = true;
                    $http({
                        url : "{{URL::to('HRM/entrysearch')}}",
                        method: 'post',
                        data: {ansarId : id,type:$scope.loadType}
                    }).then(function(response){

                        $scope.loading = false;
                        $scope.AllAnsar = response.data;
                        $scope.noFound = $scope.AllAnsar.length<=0
                       console.log($scope.searchedAnsar);
                    })
                }
            }
            $scope.clearSearch = function(){
                $scope.searchedAnsar = "";
                $scope.Id = "";
                $scope.isSearching = false;
                $scope.loadPagination();
            }
            $scope.changeDateFormat = function(d){
                return moment(d).format('D-MMM-YYYY')
            }
            
        })
        $(document).ready(function (e) {
            $("#show-search-dialog").on('click', function () {
                $("#search-dialog").slideToggle(200)
            })
        })
    </script>
    <style>
        .radio-label{
            padding: 10px 25px;
            position: relative;
            cursor: pointer;
        }
        .radio-label::before{
            content: '';
            display: block;
            position: absolute;
            width: 20px;
            height: 20px;
            top: 10px;
            left: 0;
            border: 1px solid #111111;
        }
        .radio-inline:checked+.radio-label::before{
            background: #028cab;
        }
        .search-field{
            display: block;
            padding: 5px 30px 5px 10px;
            border: 1px solid #111111;
            border-radius: 25px;
            outline: none;
            width: 100%;
        }
        .clear-search{
            position: absolute;
            right: 15px;
            border-radius: 15px;
            top: 15px;
        }
    </style>

    <?php
    $user = Auth::user();
    $userType = $user->type;
    ?>
    <div ng-controller="AnsarController">
        {{--<div class="breadcrumbplace">--}}
        {{--{!! Breadcrumbs::render('entry_list') !!}--}}
        {{--</div>--}}
        <div class="loading-report animated" ng-show="loading">
            <img src="{{asset('dist/img/ring-alt.gif')}}" class="center-block">
            <h4>Loading...</h4>
        </div>
        @if (Session::has('edit_success')) 
        <div style="width:87%;margin:0 auto;">
                        <div class="alert alert-success">
                                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                                <span class="glyphicon glyphicon-ok"></span>Ansar with ID: {{Session::get('edit_success')}} Edited successfully
                        </div>
        </div>
        @endif
        @if (Session::has('add_success')) 
        <div style="width:87%;margin:0 auto;">
                        <div class="alert alert-success">
                                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                                <span class="glyphicon glyphicon-ok"></span> Ansar with ID: {{Session::get('add_success')}} Added Successfully
                        </div>
        </div>
        @endif
        <section class="content">

            <div class="box box-solid">
                    <div class="row" style="margin: 0">
                       <div class="col-sm-9">
                           <input type="radio" id="not_submitted" ng-model="loadType" class="radio-inline" checked="checked"
                                  ng-value=0 style="display: none"/>
                           <label class="radio-label" for="not_submitted">
                               Not Verified
                           </label>
                           <input type="radio"  id="submitted" ng-model="loadType" class="radio-inline"
                                  ng-value=1 style="display: none"/>
                           <label class="radio-label" for="submitted">
                               Verified
                           </label>
                       </div>
                        <div class="col-sm-3">
                            <div style="padding: 10px;position: relative">
                                <button ng-click="clearSearch()" class="btn btn-danger btn-xs clear-search"><i class="fa fa-close"></i></button>
                                <input class="search-field"  ng-keypress="searchId($event,Id)" ng-model="Id" type="text"  name="table-search" id="entry-search" placeholder="Search by id" value=""/>
                            </div>
                            <div id="search-dialog">


                                <button ng-click="searchId(null,Id)" class="btn btn-primary btn-sm"><i class="fa fa-search"></i></button>
                            </div>
                        </div>

                    </div>
                <div class="box-body" id="change-body">
                    <div class="loading-data"><i class="fa fa-4x fa-refresh fa-spin loading-icon"></i>
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="ansar-table">

                            <tr>
                                <th>ID No</th>
                                <th>Name</th>
                                <th>Father Name</th>
                                <th>District</th>
                                <th>Thana</th>
                                <th>Date Of Birth</th>
                                <th>Sex</th>
                                <th>Rank</th>
                                <th>Mobile</th>
                                <th>Action</th>
                            </tr>
                            <tr ng-if="noFound">
                                <td colspan="10">No data or not have permission</td>
                            </tr>
                            <tr ng-repeat="ansar in AllAnsar">

                                <td>
                                    <a href="{{ URL::to('HRM/entryreport/') }}/[[ansar.ansar_id]]">[[ansar.ansar_id]]</a>
                                </td>
                                <td>[[ansar.ansar_name_eng]]</td>
                                <td>[[ansar.father_name_eng]]</td>
                                <td>[[ ansar.unit_name_eng ]]</td>
                                <td>[[ ansar.thana_name_eng ]]</td>
                                <td>[[changeDateFormat(ansar.data_of_birth) ]]</td>
                                <td>[[ ansar.sex ]]</td>
                                <td>[[ ansar.name_eng ]]</td>
                                <td>[[ ansar.mobile_no_self ]]</td>
                                <td style="padding-right: 1px;padding-left: 1px">
                                    <div style="position:relative;margin: 0 auto;display: table">
                                        {{--data entry edit--}}
                                        <a ng-if="userType == 55 && ansar.verified == 0" class="btn btn-primary btn-xs" title="edit" href="{{ url('HRM/editEntry/')}}/[[ansar.ansar_id]]"><span class="glyphicon glyphicon-edit"></span></a>
                                        <a ng-if="userType == 55 && ansar.verified == 1" class="btn btn-primary btn-xs disabled" title="edit"><span class="glyphicon glyphicon-edit"></span></a>
                                        {{--data entry edit end--}}
                                        {{--data entry verify--}}
                                        <a style="margin-left: 2px" ng-if="userType == 55 && ansar.verified == 0" class="btn btn-success btn-xs verification" title="verify" ng-click="verify(ansar.ansar_id, $index)"><span class="fa fa-check" ng-hide="verifying[$index]"></span><i class="fa fa-spinner fa-pulse" ng-show="verifying[$index]"></i></a>
                                        {{--checker edit --}}
                                        <a ng-if="userType == 44 && ansar.verified == 1" class="btn btn-primary btn-xs" title="edit"
                                           href="{{ URL::to('HRM/editEntry/')}}/[[ansar.ansar_id]]"><span
                                                    class="glyphicon glyphicon-edit"></span></a>
                                        <a ng-if="userType == 44 && ansar.verified == 2" href="{{ URL::to('HRM/editEntry/')}}/[[ansar.ansar_id]]" class="btn btn-primary btn-xs @if(!auth()->user()->hasEditVerifiedAnsarPermission()) disabled @endif" title="edit"><span
                                                    class="glyphicon glyphicon-edit"></span></a>
                                        {{--checker edit end--}}
                                        {{--checker verify--}}
                                        <a style="margin-left: 2px"  ng-if="userType == 44 && ansar.verified == 1" class="btn btn-success btn-xs verification" title="verify"
                                           ng-click="verify(ansar.ansar_id, $index)"
                                                ><span
                                                    class="fa fa-check" ng-hide="verifying[$index]"></span>
                                            <i class="fa fa-spinner fa-pulse" ng-show="verifying[$index]"></i>
                                        </a>
                                        {{--checker verify end--}}
                                        {{--checker reject--}}
                                        <a ng-if="userType == 44 && ansar.verified == 1" class="btn btn-success btn-xs verification"
                                           ng-click="reject(ansar.ansar_id, $index)" title="Reject">
                                            <span
                                                    class="fa fa-retweet" ng-hide="rejecting[$index]"></span>
                                            <i class="fa fa-spinner fa-pulse" ng-show="rejecting[$index]"></i>

                                        </a>
                                        {{--checker reject end--}}

                                        {{--admin,dc,rc,dg edit--}}
                                        <a ng-if="(userType == 11 || userType == 22 || userType == 33 || userType == 66) && (ansar.verified == 0 || ansar.verified == 1)" class="btn btn-primary btn-xs" title="edit"
                                           href="{{ URL::to('HRM/editEntry/')}}/[[ansar.ansar_id]]"><span
                                                    class="glyphicon glyphicon-edit"></span></a>
                                        <a ng-if="(userType == 11 || userType == 22 || userType == 33 || userType == 66) && (ansar.verified == 2)" class="btn btn-primary btn-xs  @if(!auth()->user()->hasEditVerifiedAnsarPermission()) disabled @endif" href="{{ URL::to('HRM/editEntry/')}}/[[ansar.ansar_id]]" title="edit"><span
                                                    class="glyphicon glyphicon-edit"></span></a>

                                        {{--admin,dc,rc,dg edit end--}}
                                        {{--admin,dc,rc,dg verify--}}
                                        <a style="margin-left: 2px"  ng-if="(userType == 11 || userType == 22 || userType == 33 || userType == 66) && (ansar.verified == 0 || ansar.verified == 1)" class="btn btn-success btn-xs verification" title="verify"
                                           ng-click="verify(ansar.ansar_id, $index)"
                                                ><span
                                                    class="fa fa-check" ng-hide="verifying[$index]"></span>
                                            <i class="fa fa-spinner fa-pulse" ng-show="verifying[$index]"></i>
                                        </a>

                                        {{--admin,dc,rc,dg verify end--}}
                                        {{--<div class="col-xs-1">--}}
                                            {{--<a class="btn btn-danger btn-xs" title="block"><span--}}
                                                        {{--class="glyphicon glyphicon-remove-circle"></span></a>--}}
                                        {{--</div>--}}
                                    </div>
                                </td>
                            </tr>

                        </table>
                    </div>

                    <div class="table_pagination" ng-if="pages.length>1 && !isSearching">
                        <ul class="pagination">
                            <li ng-class="{disabled:currentPage == 0}">
                                <a href="#" ng-click="loadAnsar(pages[0],$event)">&laquo;&laquo;</a>
                            </li>
                            <li ng-class="{disabled:currentPage == 0}">
                                <a href="#" ng-click="loadAnsar(pages[currentPage-1],$event)">&laquo;</a>
                            </li>
                            <li ng-repeat="page in pages|filter:filterMiddlePage"
                                ng-class="{active:page.pageNum==currentPage&&!loadingPage[page.pageNum],disabled:!loadingPage[page.pageNum]&&loadingPage[currentPage]}">
                                <span ng-show="currentPage == page.pageNum&&!loadingPage[page.pageNum]">[[page.pageNum+1]]</span>
                                <a href="#" ng-click="loadAnsar(page,$event)" ng-hide="currentPage == page.pageNum||loadingPage[page.pageNum]">[[page.pageNum+1]]</a>
                                <span ng-show="loadingPage[page.pageNum]"  style="position: relative"><i class="fa fa-spinner fa-pulse" style="position: absolute;top:10px;left: 50%;margin-left: -9px"></i>[[page.pageNum+1]]</span>
                            </li>
                            <li ng-class="{disabled:currentPage==pages.length-1}">
                                <a href="#" ng-click="loadAnsar(pages[currentPage+1],$event)">&raquo;</a>
                            </li>
                            <li ng-class="{disabled:currentPage==pages.length-1}">
                                <a href="#" ng-click="loadAnsar(pages[pages.length-1],$event)">&raquo;&raquo;</a>
                            </li>
                        </ul>
                    </div>
                </div>

            </div>


        </section>
        <script>
            $("#ansar-table").sortTable({
                exclude: 9
            })
        </script>
    </div>
@stop