@extends('template.master')
@section('title','Ansar History')
@section('breadcrumb')
    {!! Breadcrumbs::render('orginal_info') !!}
@endsection
@section('content')
    <script>
        GlobalApp.controller('originalInfo', function ($scope, $http) {
            $scope.isSearching = false;
            $scope.searchedAnsar = [];
            $scope.viewHistory = function (keyEvent, id) {
                if (keyEvent.type == 'keypress') {
                    if (keyEvent.which === 13) {
                        $scope.ID = id;
                        $scope.isSearching = true;
                        $http({
                            url: "{{URL::route('get_ansar_history')}}",
                            method: 'post',
                            data: {ansar_id: id}
                        }).then(function (response) {
//                        alert(JSON.stringify(response.data));
                            //$scope.searchedAnsar = response.data;
                            console.log($scope.searchedAnsar);
                        })
                    }
                }
                else if (keyEvent.type == 'click') {
                    $scope.ID = id;
                    $scope.isSearching = true;
                    $http({
                        url: "{{URL::route('get_ansar_history')}}",
                        method: 'post',
                        data: {ansar_id: id}
                    }).then(function (response) {
                        $scope.searchedAnsar = response.data;
                        console.log($scope.searchedAnsar);
//                        $scope.fontURL = $scope.searchedAnsar.url.font
//                        $scope.backURL = $scope.searchedAnsar.url.back
//                        console.log($scope.searchedAnsar);
                    }, function (response) {
                        $scope.searchedAnsar = [];
                    })
                }
            }
        })
    </script>

    <div ng-controller="originalInfo">
        <section class="content">
            <div class="box box-solid">
                <div class="box-body">
                    <div class="row">
                        <div class="col-sm-8 col-md-8 col-xs-12 col-lg-6 col-centered">
                            <form method="post">
                                <div class="center-search">
                                    <input ng-keypress="viewHistory($event,ansar_id)" ng-model="ansar_id" type="text"
                                           placeholder="Enter Ansar ID to see History">
                                    <button ng-click="viewHistory($event,ansar_id)" class="btn btn-success btn-md"
                                            style="display: block;margin: 20px auto;">View Ansar History
                                    </button>
                                </div>
                            </form>
                        </div>
                        <div class="col-sm-8 col-md-8 col-xs-12 col-lg-6 col-centered">
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <caption>
                                        History of <span style="font-style: italic;color: #111111">[[searchedAnsar.ansarInfo.ansar_name_bng]]</span>
                                    </caption>
                                    <tr>
                                        <th>#</th>
                                        <th>Action Type</th>
                                        <th>Action User</th>
                                        <th>Action Date</th>
                                    </tr>
                                    <tr ng-if="searchedAnsar.length<=0">
                                        <td class="warning" colspan="4">No History Found</td>
                                    </tr>
                                    <tr ng-repeat="a in searchedAnsar.logs">
                                        <td>[[$index+1]]</td>
                                        <td>[[a.action_type]]</td>
                                        <td>[[a.user.user_name]]</td>
                                        <td>[[a.created_at|dateformat:"DD-MMM-YYYY"]]</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
    <script>
        $(".view-image").viewer({
            navbar: false,
            toolbar: false
        })
    </script>
@stop