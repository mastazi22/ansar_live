@extends('template.master')
@section('title','Original Info')
@section('breadcrumb')
    {!! Breadcrumbs::render('orginal_info') !!}
    @endsection
@section('content')
<script>
    GlobalApp.controller('originalInfo',function($scope,$http){
        $scope.isSearching = false;
        $scope.fullInfo = function(keyEvent,id){
            if(keyEvent.type=='keypress') {
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
            else if(keyEvent.type=='click'){
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
    })
</script>

<div ng-controller="originalInfo">
    <section class="content">
        <div class="box box-solid">
            <div class="box-body">
                <form method="post">
                    <div class="center-search">
                        <input ng-keypress="fullInfo($event,Id)" ng-model="Id" type="text" placeholder="Enter Ansar ID to see Original Information">
                        <button ng-click="fullInfo($event,Id)" class="btn btn-success btn-lg" style="display: block;margin: 20px auto;">View Original Information</button>
                    </div>
                </form>
                <div ng-show="searchedAnsar.yes == 'yes'" class="fullinfo">
                    <div class="info-front-side">
                        <img class="img-responsive" src="{{asset('/data/originalinfo/frontside')}}/[[ searchedAnsar.value ]].jpg">
                    </div><br>
                    <div class="info-back-side">
                        <img class="img-responsive" src="{{asset('/data/originalinfo/backside')}}/[[ searchedAnsar.value ]].jpg">
                    </div>
                </div>
                <div ng-show="searchedAnsar.no == 'no'" class="noinfo">
                    <h4 style="text-align: center;color:red">No original information or wrong Ansar ID</h4><br>
                </div>
            </div>
        </div>
    </section>
</div>

@stop