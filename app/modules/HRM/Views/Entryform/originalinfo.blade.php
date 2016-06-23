@extends('template/master')
@section('content')
<script>
    GlobalApp.controller('originalInfo',function($scope,$http){
        $scope.isSearching = false;
        $scope.fullInfo = function(keyEvent,id){
                if (keyEvent.which === 13)
                {
                    $scope.ID = id;
                    $scope.isSearching = true;
                    $http({
                        url : "{{URL::to('HRM/idsearch')}}",
                        method: 'post',
                        data: {ansarId : id}
                    }).then(function(response){
//                        alert(JSON.stringify(response.data));
                        $scope.searchedAnsar = response.data;
                        console.log($scope.searchedAnsar);
                    })
                }
            }
    })
</script>

<div class="content-wrapper" style="min-height: 590px;" ng-controller="originalInfo">
    <section class="content" style="width: 90%;">
        <h2 style="text-align: center">Original Information</h2><br>
        <form method="post">
            <div class="center-search">
                Search: <input ng-keypress="fullInfo($event,Id)" ng-model="Id" type="text" placeholder="Enter ansar ID">
            </div><br>
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
    </section>
</div>

@stop