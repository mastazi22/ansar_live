@extends('template.master')
@section('title','Applicant Point')
@section('breadcrumb')
    {!! Breadcrumbs::render('recruitment.point.index') !!}
@endsection
@section('content')
    <script>
        GlobalApp.controller('applicantQuota', function ($scope, $http, $q, httpService,notificationService) {
            $scope.pointFields = [];
            $scope.rows = [];
            $http({
                url:'{{URL::route('recruitment.point.fields')}}',
                method:'post'
            }).then(function (response) {
                $scope.pointFields = response.data;
            })
            $scope.addNewPoint = function () {
                $scope.rows.push({
                    id:'',
                    field_name:'',
                    min_value:'',
                    min_point:'',
                    per_unit_point:'',
                    type:0
                });
            }
            $scope.savePoint = function (i) {
                $scope.allLoading = true
                $http({
                    method:'post',
                    url:'{{URL::route('recruitment.point.store')}}',
                    data:$scope.rows[i]
                }).then(function (response) {
                    $scope.allLoading = false;
                    if(response.data.status){
                        $scope.rows[i] = response.data.data;
                        console.log($scope.rows);
                        notificationService.notify('success',response.data.message)
                    }
                    else{
                        notificationService.notify('error',response.data.message)
                    }
                },function (response) {
                    $scope.allLoading = false;
                })
            }
            $scope.deletePoint = function (i) {
                $scope.allLoading = true
                $http({
                    method:'post',
                    url:'{{URL::to('/recruitment/settings/applicant_point/delete')}}/'+$scope.rows[i].id,
                    data:$scope.rows[i]
                }).then(function (response) {
                    $scope.allLoading = false;
                    if(response.data.status){
                        $scope.rows.splice(i,1);
                        notificationService.notify('success',response.data.message)
                    }
                    else{
                        notificationService.notify('error',response.data.message)
                    }
                },function (response) {
                    $scope.allLoading = false;
                })
            }
            $scope.removePoint = function (i) {
                $scope.rows.splice(i,1);
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
                <table class="table table-bordered table-condensed">
                    <caption style="font-size: 20px">Point table&nbsp;<button ng-click="addNewPoint()" class="btn btn-primary btn-xs">Add new field</button></caption>
                    <tr>
                        <th>Field name</th>
                        <th>Min value</th>
                        <th>Min point</th>
                        <th>Next per unit point</th>
                        <th>Action</th>
                    </tr>
                    <tr ng-repeat="row in rows">
                        <td>
                            <select class="form-control" name="field_name" id="" ng-model="row.field_name">
                                <option value="">--Select a field--</option>
                                <option ng-repeat="p in pointFields" value="[[p]]">[[p]]</option>
                            </select>
                        </td>
                        <td>
                            <input type="text" class="form-control" ng-model="row.min_value" placeholder="Min value">
                        </td>
                        <td>
                            <input type="text" class="form-control" ng-model="row.min_point" placeholder="Min point">
                        </td>
                        <td>
                            <input type="text" class="form-control" ng-model="row.per_unit_point" placeholder="Per unit point">
                        </td>
                        <td>
                            <div ng-if="!row.id">
                                <button class="btn btn-primary" ng-click="savePoint($index)">Save</button>
                                <button class="btn btn-danger" ng-click="removePoint($index)">Remove</button>
                            </div>
                            <div ng-if="row.id">
                                <button class="btn btn-primary">update</button>
                                <button class="btn btn-danger"  ng-click="deletePoint($index)">Delete</button>
                            </div>
                        </td>
                    </tr>
                    <tr ng-if="rows.length<=0">
                        <td colspan="5" class="bg-warning">No point list available</td>
                    </tr>
                </table>
            </div>
        </div>
    </section>
@endsection