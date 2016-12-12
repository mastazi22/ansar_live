@extends('template/master')
@section('title','Entry Information')
@section('small_title')
    <a href="{{URL::to('HRM/entryform')}}" class="btn btn-info btn-sm"><span
                class="glyphicon glyphicon-user"></span> Add New</a>
@endsection
@section('breadcrumb')
    {!! Breadcrumbs::render('entry_list') !!}
@endsection
@section('content')
    <script>
        GlobalApp.controller('AnsarController', function ($scope, $http, notificationService) {
            $scope.AllAnsar = [];
            $scope.loadType = 0;
            $scope.param = {

            }
            $scope.sort = 'desc'
            $scope.userType = parseInt('{{Auth::user()->type}}');
            $scope.notVerified = parseInt("{{$notVerified}}");
            $scope.Verified = parseInt("{{$Verified}}");
            $scope.numOfPage = 0
            $scope.itemPerPage = parseInt("{{config('app.item_per_page')}}");
            $scope.currentPage = 0;
            $scope.pages = [];
            $scope.isSearching = false;
            $scope.searchAnsarId = '';
            $scope.loading = false;
            $scope.rejecting = false;
            $scope.noFound = false;
            $scope.loadingPage = [];
            $scope.loadPagination = function () {
               // alert($scope.totalPages)
                $scope.pages = [];
                for (var i = 0; i < $scope.totalPages; i++) {
                    $scope.pages.push({
                        pageNum: i,
                        offset: i * $scope.itemPerPage,
                        limit: $scope.itemPerPage
                    })
                    $scope.loadingPage[i] = false;
                }
                if ($scope.numOfPage > 0)$scope.loadAnsar($scope.pages[0]);
                else $scope.loadAnsar({pageNum: 0, offset: 0, limit: $scope.itemPerPage});

            }
            //alert($scope.Verified + " " + $scope.notVerified);
            $scope.loadAnsar = function (page, $event) {
                if ($event != undefined)  $event.preventDefault();
                $scope.currentPage = page.pageNum;
                $scope.loadingPage[page.pageNum] = true
                $scope.searchedAnsar = "";
                $scope.isSearching = false;
                $scope.loading = true;
                $scope.noFound = false;
                $scope.AllAnsar = [];
                $http({
                    url: $scope.loadType == 0 ? "{{URL::to('HRM/getnotverifiedansar')}}" : "{{URL::to('HRM/getverifiedansar')}}",
                    method: 'get',
                    params: {
                        limit: page.limit,
                        offset: page.offset,
                        sort:$scope.sort,
                        division:$scope.param.range,
                        unit:$scope.param.unit,
                        thana:$scope.param.thana,
                        type:'view'
                    },

                }).then(function (response) {
//                alert(JSON.stringify(response.data));
                    $scope.loading = false;
                    $scope.AllAnsar = response.data;
                    console.log($scope.AllAnsar)
                    $scope.loadingPage[page.pageNum] = false
                    if ($scope.AllAnsar.length == 0)
                        $scope.noFound = true;
                })
            }
            $scope.loadTotal = function (page, $event) {
                if ($event != undefined)  $event.preventDefault();
                $scope.loading = true;
                $http({
                    url: $scope.loadType == 0 ? "{{URL::to('HRM/getnotverifiedansar')}}" : "{{URL::to('HRM/getverifiedansar')}}",
                    method: 'get',
                    params: {
                        division:$scope.param.range,
                        unit:$scope.param.unit,
                        thana:$scope.param.thana,
                        type:'count'
                    },

                }).then(function (response) {
                    $scope.total = response.data.total;
                    $scope.gCount = response.data.total
                    //alert($scope.total)
                    $scope.totalPages = Math.ceil($scope.total/$scope.itemPerPage);
                    $scope.loadPagination();
                })
            }
            $scope.$watch(function (scope) {
                return scope.loadType;
            }, function (newValue, oldValue) {
                $scope.verifying = [];
                $scope.verified = [];
                $scope.rejecting = [];
                $scope.pages = [];
                $scope.loadTotal()
                $scope.currentPage = 0;
            })

            $scope.verify = function (id, i) {
//                alert(id+"  "+i);return;
                $scope.noFound = false;
                $scope.verifying[i] = true;
                $http({
                    url: "{{URL::to('HRM/entryVerify/')}}",
                    data: {verified_id: id},
                    method: 'post'
                }).then(function (response) {
                    console.log(JSON.stringify(response.data));
                    if (response.data.status != undefined && response.data.status == false) {
                        $scope.verifying[i] = false;
                        notificationService.notify('error', "<p class='text text-bold' style='font-size: 1.2em'><i  class='fa fa-warning'></i>&nbsp"+response.data.message+"</p>");
                        return;
                    }
                    $scope.loadType = 0;
                    //$scope.loadAnsar();

                    notificationService.notify('success', "<p class='text text-bold' style='font-size: 1.2em'><i  class='fa fa-check'></i>&nbspAnsar verification complete</p>");
                    $scope.verifying[i] = false;
                    $scope.verified[i] = true;
                    $scope.notVerified--;
                    $scope.Verified++;
                    $scope.totalPages = Math.ceil($scope.notVerified / $scope.Item);
                    $scope.loadTotal();
//                    $scope.Verified++;
                }, function () {
                    $scope.verifying[i] = false;
                    $scope.verified[i] = false;
                })
            }

            $scope.reject = function (id, i) {
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
                    $scope.loadType = 0;
                    $scope.rejecting[i] = false;
                    $scope.notVerified--;
                    $scope.totalPages = Math.ceil($scope.notVerified / $scope.Item);
                    $scope.loadTotal();
//                    $scope.verified[i] = true;

//                    alert($scope.verified[i]);
                }, function () {
                    $scope.rejecting[i] = false;
//                    alert($scope.verified[i]);
                })
            }

            $scope.filterMiddlePage = function (value, index, array) {
                var minPage = $scope.currentPage - 3 < 0 ? 0 : ($scope.currentPage > array.length - 4 ? array.length - 8 : $scope.currentPage - 3);
                var maxPage = minPage + 7;
                if (value.pageNum >= minPage && value.pageNum <= maxPage) {
                    return true;
                }
            }
            $scope.changeSort = function () {
                if($scope.sort=='desc') $scope.sort='asc';
                else $scope.sort='desc'
                $scope.loadAnsar($scope.pages[$scope.currentPage]);
            }
            $scope.searchId = function () {
                if (!$scope.searchAnsarId) {
                    $scope.isSearching = false;
                    $scope.loadTotal();
                    return;
                }
                $scope.noFound = false;
                $scope.loading = true;
                $scope.isSearching = true;
                $http({
                    url: "{{URL::to('HRM/entrysearch')}}",
                    method: 'post',
                    data: {ansarId: $scope.searchAnsarId, type: $scope.loadType}
                }).then(function (response) {

                    $scope.loading = false;
                    $scope.AllAnsar = response.data;
                    $scope.noFound = $scope.AllAnsar.length <= 0
                    console.log($scope.searchedAnsar);
                })
            }
            $scope.clearSearch = function () {
                $scope.searchedAnsar = "";
                $scope.Id = "";
                $scope.isSearching = false;
                $scope.loadTotal()();
            }

        })
        $(document).ready(function (e) {
            $("#show-search-dialog").on('click', function () {
                $("#search-dialog").slideToggle(200)
            })
        })
    </script>
    <style>
        .radio-label {
            padding: 10px 25px;
            position: relative;
            cursor: pointer;
        }

        .radio-label::before {
            content: '';
            display: block;
            position: absolute;
            width: 20px;
            height: 20px;
            top: 10px;
            left: 0;
            border: 1px solid #166f16;
        }

        .radio-inline:checked + .radio-label::before {
            background: #003a37;
        }

        .search-field {
            display: block;
            padding: 5px 30px 5px 10px;
            border: 1px solid #111111;
            border-radius: 25px;
            outline: none;
            width: 100%;
        }

        .clear-search {
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
            <div style="padding: 20px 10px">
                <div class="alert alert-success">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                    <span class="glyphicon glyphicon-ok"></span>Ansar with ID: {{Session::get('edit_success')}} Edited
                    successfully
                </div>
            </div>
        @endif
        @if (Session::has('add_success'))
            <div style="padding: 20px 10px">
                <div class="alert alert-success">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                    <span class="glyphicon glyphicon-ok"></span> Ansar with ID: {{Session::get('add_success')}} Added
                    Successfully
                </div>
            </div>
        @endif
        <section class="content">

            <div class="box box-solid">
                <div class="overlay" ng-if="loading">
                    <span class="fa">
                        <i class="fa fa-refresh fa-spin"></i> <b>Loading...</b>
                    </span>
                </div>
                <div class="row" style="margin: 0">
                    <div class="col-sm-9" style="padding-top: 10px">
                        <input type="radio" id="not_submitted" ng-model="loadType" class="radio-inline"
                               checked="checked"
                               ng-value=0 style="display: none"/>
                        <label class="radio-label" for="not_submitted">
                            Not Verified
                        </label>
                        <input type="radio" id="submitted" ng-model="loadType" class="radio-inline"
                               ng-value=1 style="display: none"/>
                        <label class="radio-label" for="submitted">
                            Verified
                        </label>
                    </div>
                    <div class="col-sm-3">
                        <form ng-submit="searchId()" class="sidebar-form">
                            <div class="input-group">
                                <input type="text" name="q" autocomplete="off" class="form-control"
                                       ng-model="searchAnsarId" placeholder="Search by Ansar ID...">
                                <span class="input-group-btn">
                                    <button type="submit" name="search" id="search-btn" class="btn btn-flat"><i
                                                class="fa fa-search"></i></button>
                                 </span>
                            </div>
                        </form>
                    </div>

                </div>
                <div class="box-header" ng-if="!isSearching">
                    <h4 style="margin-top: 0" ng-if="loadType==0">Total unverified Ansars :
                        [[total.toLocaleString()]]</h4>
                    <h4 style="margin-top: 0" ng-if="loadType==1">Total verified Ansars :
                        [[total.toLocaleString()]]</h4>
                </div>
                <div class="box-body" id="change-body">
                    <filter-template
                            show-item="['range','unit','thana']"
                            type="all"
                            range-change="loadTotal()"
                            unit-change="loadTotal()"
                            thana-change="loadTotal()"
                            start-load="range"
                            field-width="{range:'col-sm-4',unit:'col-sm-4',thana:'col-sm-4'}"
                            data = "param"
                    >

                    </filter-template>

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="ansar-table">

                            <tr>
                                <th><a  ng-click="changeSort()" style="cursor: pointer">ID No</a></th>
                                <th>Name</th>
                                <th>Father Name</th>
                                <th>Unit</th>
                                <th>Thana</th>
                                <th>Date Of Birth</th>
                                <th>Gender</th>
                                <th>Rank</th>
                                <th>Mobile</th>
                                <th>Action</th>
                            </tr>
                            <tr ng-if="noFound">
                                <td colspan="10">No data or not have permission</td>
                            </tr>
                            <tr ng-repeat="ansar in AllAnsar" ng-if="AllAnsar.length>0">

                                <td>
                                    <a href="{{ URL::to('HRM/entryreport/') }}/[[ansar.ansar_id]]">[[ansar.ansar_id]]</a>
                                </td>
                                <td>[[ansar.ansar_name_eng]]</td>
                                <td>[[ansar.father_name_eng]]</td>
                                <td>[[ ansar.unit_name_eng ]]</td>
                                <td>[[ ansar.thana_name_eng ]]</td>
                                <td>[[ansar.data_of_birth|dateformat:'DD-MMM-YYYY' ]]</td>
                                <td>[[ ansar.sex ]]</td>
                                <td>[[ ansar.name_eng ]]</td>
                                <td>[[ ansar.mobile_no_self ]]</td>
                                <td style="padding-right: 1px;padding-left: 1px">
                                    <div style="position:relative;margin: 0 auto;display: table">
                                        {{--data entry edit--}}
                                        <a ng-if="userType == 55 && ansar.verified == 0" class="btn btn-primary btn-xs"
                                           title="edit" href="{{ url('HRM/editEntry/')}}/[[ansar.ansar_id]]"><span
                                                    class="glyphicon glyphicon-edit"></span></a>
                                        <a ng-if="userType == 55 && ansar.verified == 1"
                                           class="btn btn-primary btn-xs disabled" title="Edit"><span
                                                    class="glyphicon glyphicon-edit"></span></a>
                                        {{--data entry edit end--}}
                                        {{--data entry verify--}}
                                        <a style="margin-left: 2px" ng-if="userType == 55 && ansar.verified == 0"
                                           class="btn btn-success btn-xs verification" title="verify"
                                           confirm event="click" message="Are you sure want to verify this ansar" callback="verify(id,i)" data="{id:ansar.ansar_id, i:$index}"><span
                                                    class="fa fa-check"
                                                    ng-hide="verifying[$index]"></span><i
                                                    class="fa fa-spinner fa-pulse" ng-show="verifying[$index]"></i></a>
                                        {{--checker edit --}}
                                        <a ng-if="userType == 44 && ansar.verified == 1" class="btn btn-primary btn-xs"
                                           title="Edit"
                                           href="{{ URL::to('HRM/editEntry/')}}/[[ansar.ansar_id]]"><span
                                                    class="glyphicon glyphicon-edit"></span></a>
                                        <a ng-if="userType == 44 && ansar.verified == 2" href="{{ URL::to('HRM/editVerifiedEntry/')}}/[[ansar.ansar_id]]" class="btn btn-primary btn-xs" title="edit">
                                            <span class="glyphicon glyphicon-edit"></span>
                                        </a>
                                        {{--checker edit end--}}
                                        {{--checker verify--}}
                                        <a style="margin-left: 2px" ng-if="userType == 44 && ansar.verified == 1"
                                           class="btn btn-success btn-xs verification" title="verify"
                                           confirm event="click" message="Are you sure want to verify this ansar" callback="verify(id,i)" data="{id:ansar.ansar_id, i:$index}"
                                        ><span
                                                    class="fa fa-check" ng-hide="verifying[$index]"></span>
                                            <i class="fa fa-spinner fa-pulse" ng-show="verifying[$index]"></i>
                                        </a>
                                        {{--checker verify end--}}
                                        {{--checker reject--}}
                                        <a ng-if="userType == 44 && ansar.verified == 1"
                                           class="btn btn-success btn-xs verification"
                                           ng-click="reject(ansar.ansar_id, $index)" title="Reject">
                                            <span
                                                    class="fa fa-retweet" ng-hide="rejecting[$index]"></span>
                                            <i class="fa fa-spinner fa-pulse" ng-show="rejecting[$index]"></i>

                                        </a>
                                        {{--checker reject end--}}

                                        {{--admin,dc,rc,dg edit--}}
                                        <a ng-if="(userType == 11 ||userType == 77|| userType == 22 || userType == 33 || userType == 66) && (ansar.verified == 0 || ansar.verified == 1)"
                                           class="btn btn-primary btn-xs" title="Edit"
                                           href="{{ URL::to('HRM/editEntry/')}}/[[ansar.ansar_id]]"><span
                                                    class="glyphicon glyphicon-edit"></span></a>
                                        <a ng-if="(userType == 11||userType == 77 || userType == 22 || userType == 33 || userType == 66) && (ansar.verified == 2)" class="btn btn-primary btn-xs"
                                           href="{{ URL::to('HRM/editVerifiedEntry/')}}/[[ansar.ansar_id]]" title="Edit">
                                            <span class="glyphicon glyphicon-edit"></span>
                                        </a>

                                        {{--admin,dc,rc,dg edit end--}}
                                        {{--admin,dc,rc,dg verify--}}
                                        <a style="margin-left: 2px"
                                           ng-if="(userType == 11 || userType == 22||userType == 77 || userType == 33 || userType == 66) && (ansar.verified == 0 || ansar.verified == 1)"
                                           class="btn btn-success btn-xs verification" title="verify"
                                           confirm event="click" message="Are you sure want to verify this ansar" callback="verify(id,i)" data="{id:ansar.ansar_id, i:$index}"
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
                                <a href="#" ng-click="loadAnsar(page,$event)"
                                   ng-hide="currentPage == page.pageNum||loadingPage[page.pageNum]">[[page.pageNum+1]]</a>
                                <span ng-show="loadingPage[page.pageNum]" style="position: relative"><i
                                            class="fa fa-spinner fa-pulse"
                                            style="position: absolute;top:10px;left: 50%;margin-left: -9px"></i>[[page.pageNum+1]]</span>
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
    </div>
@stop