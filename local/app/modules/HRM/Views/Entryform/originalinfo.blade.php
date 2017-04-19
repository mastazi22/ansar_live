@extends('template.master')
@section('title','Original Info')
@section('breadcrumb')
    {!! Breadcrumbs::render('orginal_info') !!}
@endsection
@section('content')
    <script>
        GlobalApp.controller('originalInfo', function ($scope, $http) {
            $scope.isSearching = false;
            $scope.fullInfo = function (keyEvent, id) {
                if (keyEvent.type == 'keypress') {
                    if (keyEvent.which === 13) {
                        $scope.ID = id;
                        $scope.isSearching = true;
                        $http({
                            url: "{{URL::to('HRM/idsearch')}}",
                            method: 'post',
                            data: {ansarId: id}
                        }).then(function (response) {
//                        alert(JSON.stringify(response.data));
                            $scope.searchedAnsar = response.data;
                            console.log($scope.searchedAnsar);
                        })
                    }
                }
                else if (keyEvent.type == 'click') {
                    $scope.ID = id;
                    $scope.isSearching = true;
                    $http({
                        url: "{{URL::to('HRM/idsearch')}}",
                        method: 'post',
                        data: {ansarId: id}
                    }).then(function (response) {
                        $scope.searchedAnsar = response.data;
                        $scope.fontURL = $scope.searchedAnsar.url.font
                        $scope.backURL = $scope.searchedAnsar.url.back
                        console.log($scope.searchedAnsar);
                    }, function (response) {
                        $scope.searchedAnsar = {status: false}
                    })
                }
            }
        })
        $(document).ready(function () {
            $("#print-report").on('click', function (e) {
                e.preventDefault();

                var html = "";
                $("#print-data").find("img").each(function () {
                    html+=this.outerHTML;
                })

                $('body').append('<div id="print-area">' + html + '</div>')
                window.print();
                $("#print-area").remove()
            })
        })
    </script>
    <style>
        @media print{
            img{
                width: 100% !important;
            }
        }
    </style>
    <div ng-controller="originalInfo">
        <section class="content">
            <div class="box box-solid">
                <div class="box-body">
                    <div class="row">
                        <div class="col-sm-8 col-md-8 col-xs-12 col-lg-6 col-centered">
                            <form method="post">
                                <div class="center-search">
                                    <input ng-keypress="fullInfo($event,Id)" ng-model="Id" type="text"
                                           placeholder="Enter Ansar ID to see Original Information">
                                    <button ng-click="fullInfo($event,Id)" class="btn btn-success btn-md"
                                            style="display: block;margin: 20px auto;">View Original Information
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                    <button id="print-report" ng-show="searchedAnsar.status" class="btn btn-primary" style="margin:5px auto;display:block"><i class="fa fa-print"></i>&nbsp;Print</button>
                    <div ng-show="searchedAnsar.status" class="row" id="print-data">
                        <div class="col-md-6">
                            <img class="img-responsive img-thumbnail view-image" ng-src="[[fontURL]]">
                        </div>

                        <div class="col-md-6">
                            <img class="img-responsive img-thumbnail view-image" ng-src="[[backURL]]">
                        </div>
                    </div>
                    <div ng-show="!searchedAnsar.status&&searchedAnsar.status!=undefined" class="noinfo">
                        <h4 style="text-align: center;color:red">No Ansar Found With Ansar ID: [[Id]]</h4><br>
                    </div>
                </div>
            </div>
        </section>
    </div>
    <script>
        $(".view-image").viewer({
            navbar:false,
            toolbar:false
        })
    </script>
@stop