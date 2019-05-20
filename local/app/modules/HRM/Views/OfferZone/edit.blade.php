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
                unitId:'',
                offerZoneRangeIds:[]
            }
            $scope.data = {};
            $scope.res = {};
            $scope.toggleClick = [];
            var requests = [
                httpService.range(),
                httpService.unit(),
                $http({
                    method:'get',
                    url:'{{URL::route('HRM.offer_zone.edit',['id'=>$id])}}'
                }).then(function(response){
                    return response.data;
                },function(response){
                    return response;
                })
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
                var data = response[2];
                $scope.data = data;
                $scope.form.rangeId = data.range_id;
                $scope.form.unitId = data.unit_id;
                $scope.form.rangeName = data.range;
                $scope.form.unitName = data.unit;
//                $scope.form.offerZoneRangeIds = data.offerZoneRangeIds;
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
            $scope.showAlert = false;
            $scope.submitForm = function () {
                var form = angular.copy($scope.form)
                form['_method'] = 'patch';
                form.offerZoneRangeIds = $scope.form.offerZoneRangeIds.filter(function(v){
                    return v.offerZoneRangeId!==false&&v.offerZoneRangeId!==undefined&&v.offerZoneRangeId&&v.offerZoneRangeUnits.length>0
                })
                form.offerZoneRangeIds.forEach(function (v,i) {
                    form.offerZoneRangeIds[i].offerZoneRangeUnits = form.offerZoneRangeIds[i].offerZoneRangeUnits.filter(filterEmptyOrFalseValue)
                })
                console.log(form)
                $http({
                    method:'post',
                    url:'{{URL::route('HRM.offer_zone.update',compact('id'))}}',
                    data:angular.toJson(form)
                }).then(function (response) {
                    $scope.res = response.data;
                },function (response) {
                    $scope.res = {status:true,message:'server error'}
                })
            }
            $scope.onClose = function(){
                window.location.href = '{{URL::route('HRM.offer_zone.index')}}'
            }
            $scope.initOfferZone = function(index,l,did){
                console.log("saddsdsaasadsad");
                var f = $scope.data.offerZoneRangeIds.find(function(e){
                    return e&&e.offerZoneRangeId==did;
                })
                if(f){
                    $scope.form.offerZoneRangeIds[index]={offerZoneRangeId:f.offerZoneRangeId,offerZoneRangeUnits:f.offerZoneRangeUnits};
                    $scope.toggleClick[index]=true;
                }
                else {
                    $scope.form.offerZoneRangeIds[index]={offerZoneRangeId:'',offerZoneRangeUnits:[]};
                    $scope.form.offerZoneRangeIds[index].offerZoneRangeUnits.length = l;

                }

            }
        })
        GlobalApp.directive("checkBox",function($timeout){
            return{
                restrict:'A',
                require:'ngModel',
                link:function (scope, elem, attrs, ngModel) {
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
                        console.log(ngModel)
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
            <div style="padding: 20px">
                <show-alert alerts="res" close="onClose()"></show-alert>
            </div>
            <div class="box-body">
                <div class="row">
                    <div class="col-sm-6 col-centered">
                        <div class="form-group">
                            <label class="control-label">Select Range</label>
                            <div class="form-control">
                                [[form.rangeName]]
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="" class="control-label">Select Offer Unit</label>
                            <div class="form-control">
                                [[form.unitName]]
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="" class="control-label">Select Offer Zone</label>
                            <div class="form-control" style="min-height: 200px;max-height: 250px;overflow-y: auto">
                                <span class="text text-gray" ng-if="!units">Please select range to load units</span>
                                <ul style="list-style: none;padding: 0">
                                    <li class="tree-parent" ng-repeat="(k,d) in units" ng-init="initOfferZone($index,d.units.length,k);i=$index">
                                        <span style="display: flex;align-items: center">
                                            <i ng-click="toggleClick[$index]=!toggleClick[$index]" class="fa fa-plus" style="font-size: 10px;font-weight: normal !important;cursor: pointer"></i>&nbsp;
                                            <input type="checkbox" style="margin: 0 !important;" ng-model="form.offerZoneRangeIds[$index].offerZoneRangeId" check-box-select-all  ng-true-value="'[[k]]'">&nbsp[[d.division]]</span>
                                        <ul class="tree-child" ng-show="toggleClick[$index]">
                                            <li ng-repeat="u in d.units">
                                                <input type="checkbox" check-box style="margin: 0 !important;" value="[[u.id]]" ng-model="form.offerZoneRangeIds[i].offerZoneRangeUnits[$index]"  ng-true-value="'[[u.id]]'">&nbsp[[u.unit_name_bng]]</span>
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