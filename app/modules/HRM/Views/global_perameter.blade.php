@extends('template.master')
@section('content')
    <script>
        GlobalApp.controller('GlobalParameterController', function ($scope, $http) {
            $scope.editing = [];
            $scope.saving = [];
            $scope.reportType = 'eng';
            $scope.ansarId = ""
            $scope.id = "";
            $scope.ed = "";
            $scope.value = [];
            $scope.unit = [];
            $scope.des = [];
            $scope.priority = [];
            $scope.globalParam = [];
            $scope.submitResult = {}
            @for($i=0;$i<count($gp);$i++)
                $scope.globalParam.push({
                        id:'{{$gp[$i]->id}}',
                        param_name:'{{$gp[$i]->param_name}}',
                        param_value:'{{$gp[$i]->param_value}}',
                        param_unit:'{{$gp[$i]->param_unit}}',
                        param_description:'{{$gp[$i]->param_description}}',
                        param_piority:'{{$gp[$i]->param_piority}}'
                    })
            @endfor
            $scope.updateGlobalParameter = function (id,i) {

                $scope.saving[i] = true;
                $http({
                    url:'{{URL::to('HRM/global_parameter_update')}}',
                    method:'post',
                    data:{
                        id:parseInt(id),
                        pv:$scope.value[i],
                        pd:$scope.des[i],
                        pp:$scope.priority[i],
                        pu:$scope.unit[i]
                    }
                }).then(function (response) {
                    $scope.saving[i] = false;
                    $scope.editing[i] = false;
                    $scope.globalParam[i].param_value = $scope.value[i];
                    $scope.globalParam[i].param_description = $scope.des[i];
                    $scope.globalParam[i].param_unit = $scope.unit[i];
                    $scope.globalParam[i].param_piority = $scope.priority[i];
                    $scope.submitResult = response.data;
                    console.log(response.data)
                })
            }
            $scope.editGlobalParameter = function (i) {

                $scope.editing[i] = true;
            }


        })
        GlobalApp.directive('notify', function () {
            return {
                restrict: 'E',
                link: function (scope, element, attr) {
                    scope.$watch('submitResult', function (n, o) {
                        if (Object.keys(n).length > 0) {
                            if (n.status) {
                                $('body').notifyDialog({type: 'success', message: n.data}).showDialog()
                            }
                            else {
                                $('body').notifyDialog({type: 'error', message: n.data}).showDialog()
                            }
                        }
                    })
                }

            }
        })
    </script>
    <div ng-controller="GlobalParameterController">
        <section class="content">
            <notify></notify>
            <div class="box box-solid">
                <div class="nav-tabs-custom">
                    <ul class="nav nav-tabs">
                        <li class="active">
                            <a>Global Parameter</a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane active">
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <tr>
                                                <th>Parameter Name</th>
                                                <th>Parameter Value</th>
                                                <th>Parameter Unit</th>
                                                <th>Parameter Description</th>
                                                <th>Priority</th>
                                                <th>Action</th>
                                            </tr>
                                                <tr ng-repeat="p in globalParam">
                                                    <td>[[p.param_name]]</td>
                                                    <td ng-show="!editing[$index]">[[p.param_value]]</td>
                                                    <td ng-show="editing[$index]"  ng-init="value[$index]=p.param_value">
                                                        <input ng-disabled="saving[$index]" class="form-control" style="height: auto;padding: 1px 12px" type="text" ng-model="value[$index]">
                                                    </td>
                                                    <td ng-show="!editing[$index]">[[p.param_unit]]</td>
                                                    <td ng-show="editing[$index]" ng-init="unit[$index]=p.param_unit">
                                                        <select ng-disabled="saving[$index]" class="form-control" style="height: auto;padding: 1px 12px" ng-model="unit[$index]">
                                                            <option value="Day">Day</option>
                                                            <option value="Month">Month</option>
                                                            <option value="Year">year</option>
                                                        </select>
                                                    </td>
                                                    <td ng-show="!editing[$index]">[[p.param_description]]</td>
                                                    <td ng-show="editing[$index]" ng-init="des[$index]=p.param_description">
                                                        <input ng-disabled="saving[$index]" class="form-control" style="height: auto;padding: 1px 12px" type="text" ng-model="des[$index]">
                                                    </td>
                                                    <td ng-show="!editing[$index]">[[p.param_piority]]</td>
                                                    <td ng-show="editing[$index]"  ng-init="priority[$index]=p.param_piority">
                                                        <input ng-disabled="saving[$index]" class="form-control" style="height: auto;padding: 1px 12px" type="text" ng-model="priority[$index]">
                                                    </td>
                                                    <td>
                                                        <button ng-show="!editing[$index]" ng-click="editGlobalParameter($index)" class="btn btn-xs btn-primary"><i class="fa fa-edit"></i>&nbsp;Edit</button>
                                                        <button ng-show="editing[$index]" ng-disabled="saving[$index]" ng-click="updateGlobalParameter([[p.id]],$index)" class="btn btn-xs btn-primary">
                                                            <i ng-show="!saving[$index]" class="fa fa-save"></i><i ng-show="saving[$index]" class="fa fa-spinner fa-pulse"></i>&nbsp;Save</button>
                                                        <button ng-show="editing[$index]" ng-click="editing[$index]=false" class="btn btn-xs btn-danger"><i class="fa fa-close"></i>&nbsp;Cancel</button>
                                                    </td>
                                                </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@stop