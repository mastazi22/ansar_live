@extends('template.master')
@section('title','Applicant SMS Panel')
{{--@section('small_title','Add new ansar')--}}
@section('breadcrumb')
    {!! Breadcrumbs::render('entryform') !!}
@endsection
@section('content')



    <script>


        GlobalApp.controller('SMSController', function ($scope, $q, $http, httpService, notificationService) {
            $scope.param = {
                circular:''
            };
            $scope.circulars = [];
            $scope.allLoading = true;
            $q.all([
                httpService.circular({status: 'running'}),
                httpService.range(),
                httpService.unit()
            ]).then(function (response) {
                $scope.circulars = response[0].data;
                $scope.divisions = response[1];
                $scope.param['divisions'] = new Array(response[1].length);
                $scope.units = response[2];
                $scope.param['units'] = new Array(response[2].length);
                $scope.allLoading = false;
            },function (res) {
                $scope.allLoading = false;
            })

            $scope.$watch('param.message',function (newVal) {
                /*if(newVal!==undefined){
                    $scope.param['message']=newVal.length>160?newVal.substr(0,160):newVal;
                }*/
            })
            $scope.submitData = function () {
                $scope.allLoading = true;
                $http({
                    url:'{{URL::route('recruitment.applicant.sms_send')}}',
                    method:'post',
                    data:angular.toJson($scope.param)
                }).then(function (response) {
                    $scope.allLoading = false;
                    notificationService.notify(response.data.status,response.data.message);
                },function (response) {
                    $scope.allLoading = false;
                    notificationService.notify('error',response.statusText);
                })
            }
            $scope.changeUnits = function (id,i) {
		console.log(+$scope.param.divisions[i]+" "+id+" "+(+$scope.param.divisions[i]===id))
                if(+$scope.param.divisions[i]===id){
                    $scope.units.forEach(function (u,i) {
			console.log((+u.division_id===+id)+" "+u.division_id+" "+id)
                        if(+u.division_id===+id){
                            $scope.param.units[i] = u.id+''
                        }
                    })
                } else{
                    $scope.units.forEach(function (u,i) {
                        if(u.division_id===id){
                            $scope.param.units[i] = false;
                        }
                    })
                }
                console.log($scope.param.units)
            }
            $scope.changeDivision = function (id,d_id,i) {
                if(+$scope.param.units[i]===+id){
                    $scope.divisions.forEach(function (d,i) {
                        if(+d.id===+d_id){
                            $scope.param.divisions[i] = d.id+''
                        }
                    })
                } else{
                    var f = $scope.units.find(function (u,i) {
                        return +u.division_id===+d_id&&$scope.param.units[i];
                    })
                    if(f===undefined){
                        $scope.divisions.forEach(function (d,i) {
                            if(+d.id===+d_id){
                                $scope.param.divisions[i] = false;
                            }
                        })
                    }
                }
                console.log($scope.param.units)
            }
        });
    </script>
    <section class="content" ng-controller="SMSController">
        <div class="box box-info">
            <div class="overlay" ng-if="allLoading">
                <span class="fa">
                    <i class="fa fa-refresh fa-spin"></i> <b>Loading...</b>
                </span>
            </div>
            <div class="box-body">
                <div class="row">
                    <div class="col-sm-6 col-centered">
                        <div class="form-group">
                            <label for="" class="control-label">
                                Select a circular
                            </label>
                            <select name="" id="" class="form-control" ng-model="param.circular">
                                <option value="">--Select a circular--</option>
                                <option ng-repeat="c in circulars" value="[[c.id]]">[[c.circular_name]]</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="" class="control-label">
                                Select a applicant status
                            </label>
                            <select name="" id="" class="form-control" ng-model="param.status">
                                <option value="">--Select a status--</option>
                                <option value="sel">Selected</option>
                                <option value="acc">Accepted</option>
                                <option value="app">Applied</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="" class="control-label">
                                Select division
                            </label>
                            <div style="height: 200px;width: 100%;border: 1px solid #ababab;overflow-y: scroll;overflow-x:hidden;padding: 5px 10px">
                                <label style="display: block" ng-repeat="d in divisions">
                                    <input class="division" type="checkbox"  ng-true-value="'[[d.id]]'" ng-model="param.divisions[$index]" ng-change="changeUnits(d.id,$index)">&nbsp;[[d.division_name_bng]]
                                </label>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="" class="control-label">
                                Select district
                            </label>
                            <div style="height: 200px;width: 100%;border: 1px solid #ababab;overflow-y: scroll;overflow-x:hidden;padding: 5px 10px">

                                <label ng-repeat="d in units" style="display: block">
                                    <input class="unit" type="checkbox" data-division="[[d.division_id]]" ng-true-value="'[[d.id]]'" ng-change="changeDivision(d.id,d.division_id,$index)" ng-model="param.units[$index]">&nbsp;[[d.unit_name_bng]]
                                </label>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="" class="control-label">
                                Enter your message([[param.message?param.message.length:0]]/160):
                            </label>
                            <textarea ng-model="param.message" name="" id="" cols="30" rows="10" class="form-control" placeholder="Type your message(max 160 character)"></textarea>
                        </div>
                        <div class="form-group">
                            <button ng-disabled="!(param.circular&&param.status&&param.message)" ng-click="submitData()" class="btn btn-primary btn-block">Send SMS</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@stop
