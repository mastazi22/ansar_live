{{--User: Shreya--}}
{{--Date: 12/3/2015--}}
{{--Time: 12:34 PM--}}

@extends('template.master')
@section('title','Retire Ansar Information')
@section('breadcrumb')
    {!! Breadcrumbs::render('unit_information_list') !!}
@endsection
@section('content')
    <script>
        GlobalApp.controller('UnitViewController', function ($scope, $http, $sce, $compile) {
            $scope.retireAnsars = [];
            $scope.param = {};
            $scope.loadPage = function (url) {
                if ($event != undefined)  $event.preventDefault();
                $scope.currentPage = page==undefined?0:page.pageNum;
                $scope.loadingPage[$scope.currentPage] = true;
                $http({
                    url: url||'{{URL::to('HRM/unit/all-units')}}',
                    method: 'get',
                    params: $scope.param
                }).then(function (response) {
                    $scope.retireAnsars = response.data;
                })
            }
        })
    </script>
    <div ng-controller="UnitViewController">
        <section class="content">
            <div class="box box-solid">
                <div class="box-header">
                    <filter-template
                            show-item="['range','unit','thana']"
                            type="all"
                            range-change="loadPage()"
                            unit-change="loadPage()"
                            thana-change="loadPage()"
                            data="param"
                            start-load="range"
                            on-load="loadPage()"
                            field-width="{range:'col-sm-4',unit:'col-sm-4',thana:'col-sm-4'}"
                    >

                    </filter-template>
                    <h3 class="box-title">Total Retire Ansar : [[total]]</h3>
                </div>
                <div class="box-body">
                    <div class="overlay" ng-if="allLoading">
                    <span class="fa">
                        <i class="fa fa-refresh fa-spin"></i> <b>Loading...</b>
                    </span>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <tr>
                                <th>#</th>
                                <th>Unit Name</th>
                                <th>Unit Name in Bangla</th>
                                <th>Unit Code</th>
                                <th>Division</th>
                                <th>Division Code</th>
                                <th>Action</th>
                            </tr>
                            <tbody>
                            <tr ng-if="units.units.length==0||units.units==undefined">
                                <td colspan="8" class="warning no-ansar">
                                    No unit available to see
                                </td>
                            </tr>
                            <tr ng-repeat="a in units.units">
                                <td>
                                    [[parseInt(units.index)+$index+1]]
                                </td>
                                {{--<td>--}}
                                {{--<a href="{{URL::to('/entryreport')}}/[[a.ansar_id]]">[[a.ansar_id]]</a>--}}
                                {{--</td>--}}
                                <td>
                                    [[a.unit_name_eng]]
                                </td>
                                <td>
                                    [[a.unit_name_bng]]
                                </td>
                                <td>
                                    [[a.unit_code]]
                                </td>
                                <td>
                                    [[a.division.division_name_eng]]
                                </td>
                                <td>
                                    [[a.division.division_code]]
                                </td>
                                <td>
                                    <div class="col-xs-1">
                                        <a href="{{URL::to('HRM/unit/'.'[[a.id]]/edit')}}"
                                           class="btn btn-primary btn-xs" title="Edit"><span
                                                    class="glyphicon glyphicon-edit"></span></a>
                                    </div>
                                    <div class="col-xs-1">
                                        {{--<a href="{{URL::to('HRM/unit_delete/'.'[[a.id]]')}}"
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

        </section>
    </div>
@stop
