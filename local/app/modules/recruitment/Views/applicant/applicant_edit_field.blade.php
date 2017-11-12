@extends('template.master')
@section('title','Applicant Editable fields')
@section('breadcrumb')
    {!! Breadcrumbs::render('recruitment.point.index') !!}
@endsection
@section('content')
    <script>
        GlobalApp.controller('applicantQuota', function ($scope, $http, $q, httpService,notificationService) {
            $scope.pointFields = [];
            $scope.rows = [];
            $scope.exFields = ''
            $q.all([
                $http({method: 'get', url: '{{URL::route('recruitment.applicant.getfieldstore')}}'}),
                $http({
                    url:'{{URL::route('recruitment.point.fields')}}',
                    method:'post'
                })

            ]).then(function (response) {
                $scope.pointFields = response[1].data;
                $scope.exFields = response[0].data.field_value.split(',')
                $scope.rows = new Array($scope.pointFields.length);
                for(var i=0;i<$scope.pointFields.length;i++){
                    if($scope.exFields.indexOf($scope.pointFields[i])>=0){
                        $scope.rows[i] = $scope.pointFields[i];
                    }
                    else{
                        $scope.rows[i] = false;
                    }
                }
            })
            $scope.saveField = function () {
                $scope.allLoading = true;
                $http({
                    url:'{{URL::route('recruitment.applicant.editfieldstore')}}',
                    data:{
                        fields:$scope.rows
                    },
                    method:'post'
                }).then(function (response) {
                    $scope.allLoading = false;
                    notificationService.notify(response.data.status,response.data.message);
                },function (response) {
                    $scope.allLoading = false;
                })
                console.log($scope.rows);
            }
        })
    </script>
    <section class="content" ng-controller="applicantQuota">
        <div class="box box-solid">
            <div class="overlay" ng-if="allLoading">
                    <span class="fa">
                        <i class="fa fa-refresh fa-spin"></i> <b>Loading...</b>
                    </span>
            </div>
            <div class="box-body">
                <div>
                    <div style="display: inline-block;margin-right: 50px" ng-repeat="k in pointFields">
                        <input type="checkbox" id="[[k]]" ng-model="rows[$index]" ng-true-value="'[[k]]'"
                               class="fancy-checkbox">
                        <label for="[[k]]" class="control-label">[[k]]</label>
                    </div>
                </div>
                <div class="row" style="margin-top: 20px">
                    <div class="col-sm-12">
                        <button ng-click="saveField()" class="bt btn-primary pull-right"><i class="fa fa-save"></i>&nbsp;Save</button>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection