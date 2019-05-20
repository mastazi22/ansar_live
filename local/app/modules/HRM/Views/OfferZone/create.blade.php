@extends('template.master')
@section('title','Offer Zone')
@section('breadcrumb')
    {!! Breadcrumbs::render('recruitment.point.index') !!}
@endsection
@section('content')
    <style>
        .tree-parent{
            position: relative;
        }
        .tree-parent:before{
            content: '';
            position: absolute;
            left: 3px;
            bottom: 11px;
            top: 15px;
            border-left:1px solid #ababab;
        }
        .tree-child{
            list-style: none;
            position: relative;
        }
        .tree-child>li{
            position: relative;
        }
        .tree-child>li:before{
            content: '';
            position: absolute;
            left: -37px;
            width: 37px;
            bottom: 0;
            top: 8px;
            height: 1px;
            border-top: 1px solid #ababab;
        }
    </style>
    <script>
        GlobalApp.controller('circularPoint',function ($scope, httpService,$http,$q) {
            $scope.form={
                rangeId:'',
                unitIds:[],
                offerZoneRangeIds:[]
            }
            $scope.toggleClick = [];
            var requests = [
                httpService.range(),
                httpService.unit()
            ]
            $q.all(requests).then(function (response) {
                $scope.ranges = response[0];
                $scope.units = response[1].reduce(function (u, d) {
                    u[d.division_id] = u[d.division_id]||{division:'',units:[]};
                    u[d.division_id].division = $scope.ranges.find(function(r){
                        return r.id==d.division_id
                    }).division_name_bng
                    u[d.division_id].units.push(d);
                    return u;
                },{});
                console.log($scope.units)

            },function (response) {

            })
            $scope.loadUnit = function () {
                httpService.unit($scope.form.rangeId).then(function (response) {
                    $scope.dcUnits = response;
                },function (response) {
                    $scope.dcUnits = [];
                })
            }
            var filterEmptyOrFalseValue = function (value) {
                return value!==undefined&&value;
            }
            $scope.$watch('form.offerZoneRangeIds',function(n,o){

                console.log($scope.form.offerZoneRangeIds)
            },true)
            $scope.submitForm = function () {
                var form = angular.copy($scope.form)
                form.unitIds = $scope.form.unitIds.filter(filterEmptyOrFalseValue)
                form.offerZoneRangeIds = $scope.form.offerZoneRangeIds.filter(function(v){
                    return v.offerZoneRangeId!==false&&v.offerZoneRangeId!==undefined&&v.offerZoneRangeId&&v.offerZoneRangeUnits.length>0
                })
                form.offerZoneRangeIds.forEach(function (v,i) {
                    form.offerZoneRangeIds[i].offerZoneRangeUnits = form.offerZoneRangeIds[i].offerZoneRangeUnits.filter(filterEmptyOrFalseValue)
                })
                console.log(form)
                $http({
                    method:'post',
                    url:'{{URL::route('HRM.offer_zone.store')}}',
                    data:angular.toJson(form)
                }).then(function (response) {

                },function (response) {

                })
            }
        })
        GlobalApp.directive("checkBox",function(){
            return{
                restrict:'A',
                require:'ngModel',
                link:function (scope, elem, attrs, ngModel) {
//                    console.log(elem)
                    $(elem).on('change',function (e,external) {
                        if(!external){
                            var l = $(this).parents('ul').children('li').children('input[type=checkbox]:checked').length
                            console.log(l+" "+this.checked)
                            if(l<=0&&!this.checked){
                                $(this).parents('ul').parents('li').find('span input[type=checkbox]').prop('checked',false).trigger('change',[true])
                            } else{
                                $(this).parents('ul').parents('li').find('span input[type=checkbox]').prop('checked',true).trigger('change',[true])
                            }
                        }
                        if(this.checked){
                            ngModel.$setViewValue($(elem).val())
                        }
                        else{
                            ngModel.$setViewValue(false)
                        }
//                        scope.$apply();
                    })
                }
            }
        })
        GlobalApp.directive("checkBoxSelectAll",function(){
            return{
                restrict:'A',
                require:'ngModel',
                link:function (scope, elem, attrs, ngModel) {
//                    console.log(elem)
                    $(elem).on('change',function (e,external) {
                        if(this.checked){
                            var i = $(this).parents('li').index();
                            if(!scope.toggleClick[i])scope.toggleClick[i] = true;
//                            console.log(n)
                            if(!external) $(elem).parents('li').find('ul input[type=checkbox]').prop('checked',true)
                            ngModel.$setViewValue($(elem).val())
                        }
                        else{
                            if(!external) $(elem).parents('li').find('ul input[type=checkbox]').prop('checked',false)
                            ngModel.$setViewValue(false)
                        }
                        $(elem).parents('li').find('ul input[type=checkbox]').trigger('change',[true])
                    })
                }
            }
        })
    </script>
    <section class="content" ng-controller="circularPoint">
        <div class="box box-solid">
            @if(Session::has('session_error'))
                <div class="alert alert-danger">
                    <i class="fa fa-warning"></i>&nbsp;{{Session::get('session_error')}}
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                </div>
            @elseif(Session::has('session_success'))
                <div class="alert alert-success">
                    <i class="fa fa-check"></i>&nbsp;{{Session::get('session_success')}}
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                </div>
            @endif

            <div class="box-body">
                <div class="row">
                    <div class="col-sm-6 col-centered">
                        <div class="form-group">
                            <label class="control-label">Select Range</label>
                            <select class="form-control" ng-model="form.rangeId" ng-change="loadUnit()">
                                <option value="">--Select a range</option>
                                <option ng-repeat="r in ranges" value="[[r.id]]">[[r.division_name_bng]]</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="" class="control-label">Select Offer Unit</label>
                            <div class="form-control" style="min-height: 150px;max-height: 200px;overflow-y: auto">
                                <span class="text text-gray" ng-if="!dcUnits||dcUnits.length==0">Please select range to load units</span>
                                <ul style="list-style: none;padding: 0">
                                    <li ng-repeat="d in dcUnits">
                                        <input type="checkbox" ng-model="form.unitIds[$index]" ng-true-value="'[[d.id]]'" ng-init="form.unitIds[$index]=d.id+''">&nbsp[[d.unit_name_bng]]
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="" class="control-label">Select Offer Zone</label>
                            <div class="form-control" style="min-height: 200px;max-height: 250px;overflow-y: auto">
                                <span class="text text-gray" ng-if="!units">Please select range to load units</span>
                                <ul style="list-style: none;padding: 0">
                                    <li class="tree-parent" ng-repeat="(k,d) in units" ng-init="form.offerZoneRangeIds[$index]={offerZoneRangeId:'',offerZoneRangeUnits:[]};form.offerZoneRangeIds[$index].offerZoneRangeUnits.length = d.units.length;i=$index;toggleClick[$index]=false">
                                        <span style="display: flex;align-items: center">
                                            <i ng-click="toggleClick[$index]=!toggleClick[$index]" class="fa fa-plus" style="font-size: 10px;font-weight: normal !important;cursor: pointer"></i>&nbsp;
                                            <input type="checkbox" style="margin: 0 !important;" ng-model="form.offerZoneRangeIds[$index].offerZoneRangeId" check-box-select-all  ng-true-value="'[[k]]'">&nbsp[[d.division]]</span>
                                        <ul class="tree-child" ng-if="toggleClick[$index]">
                                            <li ng-repeat="u in d.units">
                                                <input type="checkbox" check-box style="margin: 0 !important;" value="'[[u.id]]'" ng-model="form.offerZoneRangeIds[i].offerZoneRangeUnits[$index]"  ng-true-value="'[[u.id]]'">&nbsp[[u.unit_name_bng]]</span>
                                            </li>
                                        </ul>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="form-group">
                            <button class="btn btn-primary pull-right" ng-click="submitForm()">Create Offer Zone</button>
                        </div>
                    </div>
                </div>

            </div>
        </div>
        </div>
    </section>
@endsection