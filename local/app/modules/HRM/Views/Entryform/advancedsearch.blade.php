@extends('template.master')
@section('title','Advanced Search')
@section('breadcrumb')
    {!! Breadcrumbs::render('entryadvancedsearch') !!}
@endsection
@section('content')
    <script>
        $(document).ready(function () {
            $('#birth_from_name').datePicker(false);
        })
        GlobalApp.controller('advancedEntrySearch', function ($scope, $http, getNameService, getBloodService) {
            $scope.pages = [];
            $scope.name_type = "LIKE";
            $scope.father_name_type = "LIKE";
            $scope.blood_type = "LIKE";
            $scope.division_type = "=";
            $scope.district_type = "=";
            $scope.thana_type = "=";
            $scope.height_type = "=";
            $scope.birth_type = "=";
            $scope.loading = false;
            $scope.mobile_no_self_type = "=";
            $scope.mobile_no_req_type = "=";
            $scope.nid_type = "=";
            var sd = "";
            $scope.advancedSearchSubmit = function () {
                $scope.loading = true;
                if ($scope.birth_from_name) {
                    var date = new Date($scope.birth_from_name);
                    sd = date.getFullYear() + "-" + (date.getMonth() + 1) + "-" + (date.getDate() - 1);
                }
                else {
                    sd = "";
                }
                $http({
                    url: "{{URL::to('HRM/advancedentrysearchsubmit')}}",
                    method: 'get',
                    params: {
                        name_type: $scope.name_type,
                        search_name: $scope.search_name,
                        father_name_type: $scope.father_name_type,
                        search_father_name: $scope.search_father_name,
                        blood_type: $scope.blood_type,
                        blood_name: $scope.blood_name,
                        height_type: $scope.height_type,
                        height_name: $scope.height_name,
                        inch_name: $scope.inch_name,
                        birth_type: $scope.birth_type,
                        birth_from_name: sd,
                        division_type: $scope.division_type,
                        division_name: $scope.division_name,
                        district_type: $scope.district_type,
                        district_name: $scope.district_name,
                        thana_type: $scope.thana_type,
                        thana_name: $scope.thana_name,
                        mobile_no_self_type: $scope.mobile_no_self_type,
                        mobile_no_self: $scope.mobile_no_self,
                        mobile_no_req_type: $scope.mobile_no_req_type,
                        mobile_no_request: $scope.mobile_no_request,
                        nid: $scope.nid,
                        nid_type: $scope.nid_type
                    }
                }).then(function (response) {

                    $scope.loading = false;
                    $scope.nowdata = JSON.stringify(response);
                    $scope.alldata = response.data.data;
                    makePagination(response.data.last_page, response.data.next_page_url)
//                alert($scope.nowdata);
                })

            }
            $scope.dateConvert = function (date) {
                return (moment(date).format('DD-MMM-Y'));
            }
            $scope.advancedSearchPage = function (p, $event) {
//            alert(p.pageNum);
                $scope.loading = true;
                $scope.currentPage = p.pageNum;
//            $scope.currentPage = parseInt(url);
//            alert($scope.currentPage);
                console.log($scope.currentPage);
                $event.preventDefault();
                $http({
                    url: p.url,
                    method: 'get',
                    params: {
                        name_type: $scope.name_type,
                        search_name: $scope.search_name,
                        father_name_type: $scope.father_name_type,
                        search_father_name: $scope.search_father_name,
                        blood_type: $scope.blood_type,
                        blood_name: $scope.blood_name,
                        height_type: $scope.height_type,
                        height_name: $scope.height_name,
                        inch_name: $scope.inch_name,
                        birth_type: $scope.birth_type,
                        birth_from_name: sd,
                        division_type: $scope.division_type,
                        division_name: $scope.division_name,
                        district_type: $scope.district_type,
                        district_name: $scope.district_name,
                        thana_type: $scope.thana_type,
                        thana_name: $scope.thana_name,
                        mobile_no_self_type: $scope.mobile_no_self_type,
                        mobile_no_self: $scope.mobile_no_self,
                        mobile_no_req_type: $scope.mobile_no_req_type,
                        mobile_no_request: $scope.mobile_no_request,
                        nid: $scope.nid,
                        nid_type: $scope.nid_type
                    }
                }).then(function (response) {
                    $scope.loading = false;
                    $scope.nowdata = JSON.stringify(response);
                    $scope.alldata = response.data.data;
                    console.log(response.data);
                })

            }
            function makePagination(lp, pageUrl) {
                $scope.pages = [];
                if (!pageUrl) return;
                var baseUrl = pageUrl.substring(0, pageUrl.indexOf('?'));
                $scope.pages[0] = {pageNum: 0, url: baseUrl + "?page=" + 1}
                for (var i = 1; i < lp; i++) {
                    $scope.pages[i] = {pageNum: i, url: baseUrl + "?page=" + (i + 1)}
                }
                $scope.currentPage = 0;
                //alert(baseUrl)
            }

            getNameService.getDivision().then(function (response) {
                $scope.division = response.data;
            });
            $scope.SelectedItemChanged = function () {
                getNameService.getDistric($scope.division_name).then(function (response) {
                    $scope.district = response.data;
                })
            };
            $scope.SelectedDistrictChanged = function () {
//            alert($scope.SelectedDistrict);
                getNameService.getThana($scope.district_name).then(function (response) {
                    $scope.thana = response.data;
                })
            };
            getBloodService.getAllBloodName().then(function (response) {
                $scope.blood = response.data;
            });
            $scope.filterMiddlePage = function (value, index, array) {
                var minPage = $scope.currentPage - 3 < 0 ? 0 : ($scope.currentPage > array.length - 4 ? array.length - 8 : $scope.currentPage - 3);
                var maxPage = minPage + 7;
                if (value.pageNum >= minPage && value.pageNum <= maxPage) {
                    return true;
                }
            }
        })


        GlobalApp.factory('getNameService', function ($http) {
            return {
                getDivision: function () {
                    return $http.get("{{URL::to('HRM/DivisionName')}}");
                },
                getDistric: function (data) {

                    return $http.get("{{URL::to('HRM/DistrictName')}}", {params: {id: data}});
                },
                getThana: function (data) {
                    return $http.get("{{URL::to('HRM/ThanaName')}}", {params: {id: data}});
                }
            }
        })

        GlobalApp.factory('getBloodService', function ($http) {
            return {
                getAllBloodName: function () {
                    return $http.get("{{URL::to('HRM/getBloodName')}}")
                }
            }
        });
    </script>
    <?php
    $user = Auth::user();
    $userType = $user->type;
    $allData = [];
    ?>

    <div ng-controller="advancedEntrySearch">
        {{--<div class="breadcrumbplace">--}}
        {{--{!! Breadcrumbs::render('entryadvancedsearch') !!}--}}
        {{--</div>--}}
        <div class="loading-report animated" ng-show="loading">
            <img src="{{asset('dist/img/ring-alt.gif')}}" class="center-block">
            <h4>Loading...</h4>
        </div>
        <section class="content">
            <div class="box box-solid">
                <div class="box-body" id="change-body">
                    <div class="loading-data"><i class="fa fa-4x fa-refresh fa-spin loading-icon"></i>
                    </div>
                    <form method="post">
                        <div class="table-responsive">
                            <table class="table table-condensed table-sm">

                                <thead class="thead-inverse">
                                <tr>
                                    <th style="width:16%;">Search Name</th>
                                    <th style="width:44%;"> Search Type</th>
                                    <th style="width:40%;">Search Value</th>
                                </tr>
                                </thead>
                                <tr>
                                    <td>Division</td>
                                    <td>
                                        <select name="division_type" class="ansaradvancedselect"
                                                ng-model="division_type">
                                            <option value="=">EQUAL</option>
                                        </select>
                                    </td>
                                    <td>
                                        <select name="division_name" class="ansaradvancedname" ng-model="division_name"
                                                ng-change="SelectedItemChanged()">
                                            <option value="">--Select an option--</option>
                                            <option ng-repeat="d in division" value=[[d.id]]>[[d.division_name_eng]]
                                            </option>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td>District</td>
                                    <td>
                                        <select name="district_type" class="ansaradvancedselect"
                                                ng-model="district_type">
                                            <option value="=">EQUAL</option>
                                        </select>
                                    </td>
                                    <td>
                                        <select name="district_name" class="ansaradvancedname" ng-model="district_name"
                                                ng-change="SelectedDistrictChanged()">
                                            <option value="">--Select an option--</option>
                                            <option ng-repeat="x in district" value=[[x.id]]>[[x.unit_name_eng]]
                                            </option>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Thana</td>
                                    <td>
                                        <select name="thana_type" class="ansaradvancedselect" ng-model="thana_type">
                                            <option value="=">EQUAL</option>
                                        </select>
                                    </td>
                                    <td>
                                        <select name="thana_name" class="ansaradvancedname" ng-model="thana_name">
                                            <option value="">--Select an option--</option>
                                            <option ng-repeat="x in thana" value=[[x.id]]>[[x.thana_name_eng]]</option>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Name</td>
                                    <td>
                                        <select class="ansaradvancedselect" name="name_type" ng-model="name_type">
                                            <option value="LIKE">LIKE</option>
                                        </select>
                                    </td>
                                    <td>
                                        <input class="ansaradvancedname" name="search_name" type="text"
                                               ng-model="search_name"/>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Father Name</td>
                                    <td>
                                        <select class="ansaradvancedselect" name="name_type"
                                                ng-model="father_name_type">
                                            <option value="LIKE">LIKE</option>
                                        </select>
                                    </td>
                                    <td>
                                        <input class="ansaradvancedname" name="search_name" type="text"
                                               ng-model="search_father_name"/>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Blood Group</td>
                                    <td>
                                        <select class="ansaradvancedselect" name="blood_type" ng-model="blood_type">
                                            <option value="">--Select an option--</option>
                                            <option value="LIKE">EQUAL</option>
                                        </select>
                                    </td>
                                    <td>
                                        <select name="blood_name" class="ansaradvancedname" ng-model="blood_name">
                                            <option value="">Select an option</option>
                                            <option ng-repeat="x in blood" value=[[x.id]]>[[x.blood_group_name_eng]]
                                            </option>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Height</td>
                                    <td>
                                        <select name="height_type" class="ansaradvancedselect" ng-model="height_type">
                                            <option value="">Select an option</option>
                                            <option value="=">EQUAL</option>
                                            <option value=">">GREATER THAN</option>
                                            <option value="<">SMALLER THAN</option>
                                        </select>
                                    </td>
                                    <td>
                                        <input class="height_search" name="height_name" type="text"
                                               ng-model="height_name" placeholder="Feet"/>
                                        <input class="height_search" name="inch_name" type="text" ng-model="inch_name"
                                               placeholder="Inch"/>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Date Of Birth</td>
                                    <td>
                                        <select name="birth_type" class="ansaradvancedselect" ng-model="birth_type">

                                            <option value="=">EQUAL</option>
                                            <option value="<">BEFORE</option>
                                            <option value=">">AFTER</option>
                                        </select>
                                    </td>
                                    <td>
                                        <div style="width:100%;">
                                            <input class="ansaradvancedname" name="birth_from_name" id="birth_from_name"
                                                   type="text"
                                                   ng-model="birth_from_name" style="height:25px"/>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Mobile No. Self</td>
                                    <td>
                                        <select class="ansaradvancedselect" name="mobile_no_self"
                                                ng-model="mobile_no_self_type">
                                            <option value="=">EQUAL</option>
                                        </select>
                                    </td>
                                    <td>
                                        <input class="ansaradvancedname" name="mobile_no_self" type="text"
                                               ng-model="mobile_no_self"
                                               placeholder="Enter mobile number; Example: 01710000000"/>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Mobile No. Request</td>
                                    <td>
                                        <select class="ansaradvancedselect" name="mobile_no_request"
                                                ng-model="mobile_no_req_type">
                                            <option value="=">EQUAL</option>
                                        </select>
                                    </td>
                                    <td>
                                        <input class="ansaradvancedname" name="mobile_no_request" type="text"
                                               ng-model="mobile_no_request"
                                               placeholder="Enter mobile number; Example: 01710000000"/>
                                    </td>
                                </tr>
                                <tr>
                                    <td>National ID</td>
                                    <td>
                                        <select class="ansaradvancedselect" name="nid"
                                                ng-model="nid_type">
                                            <option value="=">EQUAL</option>
                                        </select>
                                    </td>
                                    <td>
                                        <input class="ansaradvancedname" name="nid" type="text"
                                               ng-model="nid" placeholder="Enter NID number"/>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <button ng-click="advancedSearchSubmit()" class="default pull-right" style="margin-right:6px;">
                            submit
                        </button>
                        <!--<button  class="default pull-right" style="margin-right:6px;">submit</button>-->

                    </form>
                </div>
            </div>
            <div class="box box-solid">

                <div class="box-header"><h3>Search Result</h3></div>

                <div class="box-body" id="change-body">
                    <div class="loading-data"><i class="fa fa-4x fa-refresh fa-spin loading-icon"></i>
                    </div>
                    <table class="table table-responsive table-bordered table-striped" id="ansar-table">

                        <tr>
                            <th>SL. No</th>
                            <th>ID No</th>
                            <th>Rank</th>
                            <th>Name</th>
                            <th>Father Name</th>
                            <th>Sex</th>
                            <th>District</th>
                            <th>Date of birth</th>
                            <th>Mobile No.(Self)</th>
                        </tr>

                        <tr ng-repeat="ansar in alldata">
                            <td>[[currentPage?((currentPage*10)+$index+1):$index+1]]</td>
                            <td><a href="{{ URL::to('HRM/entryreport/') }}/[[ansar.ansar_id]]">[[ansar.ansar_id]]</a>
                            </td>
                            <td>[[ansar.name_eng]]</td>
                            <td>[[ansar.ansar_name_eng]]</td>
                            <td>[[ansar.father_name_eng]]</td>
                            <td>[[ansar.sex]]</td>
                            <td>[[ansar.unit_name_eng]]</td>
                            <td>[[dateConvert(ansar.data_of_birth)]]</td>
                            <td>[[ansar.mobile_no_self]]</td>
                        </tr>
                    </table>
                    <!--                    <div ng-show="pages.length>0" class="table_pagination" >
                                            <ul class="pagination">
                                                <li ng-repeat="p in pages"  >
                                                    <span href="#" ng-show="currentPage==p.pageNum" ng-class="{active:currentPage==p.pageNum}">[[p.pageNum]]</span>
                                                    <a href="#" ng-hide="currentPage==p.pageNum"  ng-click="advancedSearchPage(p.url,$event)">[[p.pageNum]]</a>
                                                </li>
                                            </ul>
                                        </div>-->
                    <div class="table_pagination" ng-if="pages.length>1">
                        <ul class="pagination">
                            <li ng-class="{disabled:currentPage == 0}">
                                <a href="#" ng-click="advancedSearchPage(pages[0],$event)">&laquo;&laquo;</a>
                            </li>
                            <li ng-class="{disabled:currentPage == 0}">
                                <a href="#" ng-click="advancedSearchPage(pages[currentPage-1],$event)">&laquo;</a>
                            </li>
                            <li ng-repeat="page in pages|filter:filterMiddlePage"
                                ng-class="{active:page.pageNum==currentPage}">
                                <span ng-show="currentPage == page.pageNum">[[page.pageNum+1]]</span>
                                <a href="#" ng-click="advancedSearchPage(page,$event)"
                                   ng-hide="currentPage == page.pageNum">[[page.pageNum+1]]</a>

                            </li>
                            <li ng-class="{disabled:currentPage==pages.length-1}">
                                <a href="#" ng-click="advancedSearchPage(pages[currentPage+1],$event)">&raquo;</a>
                            </li>
                            <li ng-class="{disabled:currentPage==pages.length-1}">
                                <a href="#"
                                   ng-click="advancedSearchPage(pages[pages.length-1],$event)">&raquo;&raquo;</a>
                            </li>
                        </ul>
                    </div>

                </div>

            </div>


        </section>
    </div>
@stop