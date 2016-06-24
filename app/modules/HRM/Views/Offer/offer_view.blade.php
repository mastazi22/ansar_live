@extends('template.master')
@section('content')
    <script>

        GlobalApp.controller('OfferController', function ($scope, $http,$interval) {
            $scope.kpiPCMale = 0;
            $scope.kpiPCFemale = 0;
            $scope.kpiAPCMale = 0;
            $scope.kpiAPCFemale = 0;
            $scope.kpiAnsarMale = 0;
            $scope.kpiAnsarFemale = 0;
            $scope.noAnsar = true;
            $scope.offerAnsarId = [];
            $scope.showLoadScreen = true;
            $scope.showLoadingAnsar = false;
            $scope.modalStyle = {};
            $scope.selectedDistrict = [];
            $scope.updatedDistrict = [];
            $scope.removedDistrict = [];
            $scope.allDistrict = [];
            $scope.allDistrictList = [];
            $scope.data = {offeredDistrict: ""};
            $scope.offeredDistrictList = [];
            $scope.selectedAnsar = [];
            $scope.result = {}
            $scope.countDown = 10
            $scope.buttonText = "Send Offer"
            $scope.offerQuota = 0;
            $scope.negateDistrictId = null;
            var promis;
            $scope.districtId = '{{Auth::user()->district_id}}'
            var userType = '{{Auth::user()->type}}'
            if (parseInt(userType) == 11 || parseInt(userType) == 33) {
                $scope.isAdmin = true;
                $http({
                    url: '{{URL::to('HRM/DistrictName')}}',
                    type: 'get'
                }).then(function (response) {
                    $scope.allDistrictList = response.data;
                    $scope.offeredDistrictList = JSON.parse(JSON.stringify($scope.allDistrictList));
                    $scope.allDistrict = JSON.parse(JSON.stringify($scope.allDistrictList));
                })
            }
            else {
                $scope.isAdmin = false;
                $scope.negateDistrictId = $scope.districtId;
            }
            $scope.addDistrict = function () {
                for (var i = 0; i < $scope.selectedDistrict.length; i++) {
                    $scope.updatedDistrict.push($scope.allDistrict[$scope.selectedDistrict[i] - i])
                    $scope.allDistrict.splice($scope.selectedDistrict[i] - i, 1)
                }
                $scope.selectedDistrict = [];
            }
            $scope.removeDistrict = function () {
                for (var i = 0; i < $scope.removedDistrict.length; i++) {
                    $scope.allDistrict.push($scope.updatedDistrict[$scope.removedDistrict[i] - i])
                    $scope.updatedDistrict.splice($scope.removedDistrict[i] - i, 1)
                }
                $scope.removedDistrict = [];
            }
            $scope.loadAnsar = function () {
//                var total = parseInt($scope.kpiPCMale) + parseInt($scope.kpiPCFemale) + parseInt($scope.kpiAPCMale) + parseInt($scope.kpiAPCFemale) + parseInt($scope.kpiAnsarMale) + parseInt($scope.kpiAnsarFemale);
//                alert(total)
//                if(total>$scope.offerQuota){
//                    alert("Your offer limit exit total number of offer you want to send");
//                    return;
//                }
                $scope.buttonText = "Loading Ansar"
                $scope.showLoadScreen = false;
                var district = [];
                $scope.updatedDistrict.forEach(function (d) {
                    district.push(parseInt(d.id))
                })
                var data = {
                    ansar_info: {
                        pc_male: $scope.kpiPCMale,
                        pc_female: $scope.kpiPCFemale,
                        apc_male: $scope.kpiAPCMale,
                        apc_female: $scope.kpiAPCFemale,
                        ansar_male: $scope.kpiAnsarMale,
                        ansar_female: $scope.kpiAnsarFemale,
                        district: district,
                        exclude_district: (parseInt(userType) == 11 ? null : $scope.districtId)
                    }
                }
                // alert($scope.selectedDistrict);
                $scope.showLoadingAnsar = true;
                $scope.modalStyle = {'display': 'block'}
                $http({
                    url: '{{URL::to('HRM/kpi_list')}}',
                    method: 'get',
                    params: data
                }).then(function (response) {
                    //alert(JSON.stringify(response.data));
                    if (response.data.length > 0) {
                        $scope.selectedAnsar = response.data;
                        $scope.noAnsar = false;
                        $scope.sendOffer();
                    }
                    else {
                        $scope.noAnsar = true;
                        $scope.showLoadingAnsar = true;
                        $scope.buttonText = "Send Offer"
                    }

                }, function (response) {
                    //alert('Error!! ' + response.status)
                    $scope.showLoadingAnsar = false;
                    $scope.buttonText = "Send Offer"
                })
            }
            $scope.sendOffer = function () {

                $scope.offerAnsarId = [];
                $scope.buttonText = "Sending Offer..."
                $scope.selectedAnsar.forEach(function (v) {
                    $scope.offerAnsarId.push(v.ansar_id);
                })
                if ($scope.offerAnsarId.length > 0) {
                    $http({
                        url: '{{URL::to('HRM/send_offer')}}',
                        data: angular.toJson({
                            "offered_ansar": $scope.offerAnsarId,
                            district_id: $scope.isAdmin ? $scope.data.offeredDistrict : $scope.districtId,
                            type: 'panel',
                            offer_limit: $scope.offerQuota
                        }),
                        method: 'post'
                    }).then(
                            function (response) {
                                console.log(response.data)
                                //alert(response.data.success + " Success," + response.data.fail + " Fails");
                                $scope.showLoadScreen = true;
                                $scope.result = response.data;
                                $scope.buttonText = "Send Offer"
                                $scope.getOfferCount();
                            },
                            function (response) {
                                // $scope.error = response.data;
                                //alert(JSON.stringify(response));
                                console.log(response.data);
                                $scope.result = {
                                    status: false,
                                    message: "A Server Error Occur. ERROR CODE : " + response.status
                                };
                                $scope.showLoadScreen = true;
                                $scope.buttonText = "Send Offer"
                            }
                    )
                }
            }
            $scope.getOfferCount = function () {
                $http({
                    url: "{{URL::to('HRM/get_offer_count')}}",
                    method: 'get'
                }).then(function (response) {
                    $scope.offerQuota = response.data.total_offer;
                }, function (response) {

                })
            }

//            $scope.resetModal = function () {
//               // alert("ksajd")
//                $scope.allDistrict = JSON.parse(JSON.stringify($scope.allDistrictList));
//                $scope.selectedDistrict = [];
//                $scope.removedDistrict = [];
//                $scope.updatedDistrict = [];
//            }
            $scope.checkDistrict = function (a, b) {
                var s = false;
                a.forEach(function (a) {
                    if (a.id == b.id) {
                        s = true;
                    }
                })
                //console.log(s)
                return s;

            }
            $scope.getOfferCount();
            $scope.getInt = function (a) {
                return parseInt(a) + '';
            }
            $scope.startCountDown = function () {
                promis = $interval(function () {
                    $scope.countDown = $scope.countDown-1
                },1000)
            }
            $scope.$watch('countDown', function (n,o) {
                if(n<=0) {
                    $interval.cancel(promis);
                    window.location.assign('{{URL::previous()}}')
                }

            })

        })

        //        GlobalApp.directive('ansarId', function () {
        //            return {
        //                link: function ($scope, element, attr) {
        //                    $scope.offerAnsarId[$scope.$index] = attr.ansarId;
        //                }
        //            }
        //        })
        GlobalApp.directive('closeModal', function () {
            return {
                link: function (scope, element, attr) {
                    scope.$watch('showLoadingAnsar', function (n, o) {
                        //alert(o + " " + n)
                        if (o && !n) {
                            $("#offer-option").modal('hide')
                        }
                    })
                    scope.$watch('result', function (newValue, oldValue) {
                        if (Object.keys(newValue).length > 0) {
                            if (newValue.status) {
                                $('body').notifyDialog({
                                    type: 'success',
                                    message: newValue.message
                                }).showDialog()
                                scope.selectedAnsar = [];
                                scope.noAnsar = true;
                            }
                            else {
                                $('body').notifyDialog({
                                    type: 'error',
                                    message: newValue.message
                                }).showDialog()
                            }
                            scope.result = {};
                        }
                    }, true)
                }
            }
        })


    </script>
    <div class="content-wrapper" ng-controller="OfferController" close-modal style="min-height: 590px">
        {{--<div class="breadcrumbplace">--}}
            {{--{!! Breadcrumbs::render('offer_information') !!}--}}
        {{--</div>--}}
        <section class="content">
            @if($isFreeze)
                <h3 style="text-align: center">You have <span class="text-warning">{{$isFreeze}}</span> freezed ansar in your district.Unfreeze them then you are eligible to send offer
                <br>Redirect in ...<span class="text-danger" ng-init="startCountDown()">[[countDown]]</span> Second
                </h3>
            @else
                <div class="box box-solid" style="min-height: 200px;">
                    <div class="nav-tabs-custom">
                        <ul class="nav nav-tabs">
                            <li class="active">
                                <a data-toggle="tab" href="#pc">Offer List</a>
                            </li>
                            {{--<li class="pull-right">--}}
                            {{--<a class="btn btn-primary option" data-toggle="modal" data-target="#offer-option"><i--}}
                            {{--class="fa fa-download"></i> Load Ansar For Offer</a>--}}
                            {{--</li>--}}
                        </ul>
                        <div class="tab-content">
                            <div class="tab-pane active" id="offer-table">
                                <div class="row">
                                    <div class="col-md-8  col-sm-offset-2">
                                        <div class="row" ng-show="isAdmin" style="margin: 0 !important;">
                                            <div class="col-sm-5">
                                                <div class="form-group">
                                                    <label class="control-label"> Select District</label>
                                                    <select name="district" ng-model="selectedDistrict"
                                                            class="form-control" multiple>
                                                        <option ng-repeat="district in allDistrict"
                                                                value=[[$index]]>
                                                            [[district.unit_name_eng]]
                                                        </option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-sm-2" style="margin-right: -15px">
                                                <div class="form-group">
                                                    <label class="control-label"
                                                           style="visibility: hidden">Action</label>
                                                    <ul style="list-style: none;padding: 0;margin: 0 ">
                                                        <li style="padding-top: 6px;padding-left: 10px">
                                                            <button class="btn btn-default" ng-click="addDistrict()">
                                                                <i class="fa fa-long-arrow-right"></i>
                                                            </button>
                                                        </li>
                                                        <li style="padding-top: 6px;padding-left: 10px">
                                                            <button class="btn btn-default" ng-click="removeDistrict()">
                                                                <i class="fa fa-long-arrow-left"></i>
                                                            </button>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                            <div class="col-sm-5">
                                                <div class="form-group">
                                                    <label class="control-label"> Selected District</label>
                                                    <select name="district" ng-model="removedDistrict"
                                                            class="form-control" multiple>
                                                        <option ng-repeat="district in updatedDistrict"
                                                                value=[[$index]]>
                                                            [[district.unit_name_eng]]
                                                        </option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-8 col-sm-offset-2">
                                        <h4 ng-if="!isAdmin">You have total <span style="text-decoration: underline"
                                                                                  ng-class="{'text-green':offerQuota>50,'text-danger':offerQuota<=10}">[[offerQuota]]</span>
                                            offer left</h4>
                                        <ul style="list-style: none;margin-left: -15px !important;padding: 0"
                                            class="row">
                                            <li class="col-md-4">
                                                <fieldset class="fieldset ">
                                                    <legend class="legend">PC</legend>
                                                    <div class="input-group margin-bottom-input">
                                                            <span class="input-group-addon">
                                                                <i class="fa fa-male"></i>
                                                            </span>
                                                        <input type="text" ng-model="kpiPCMale"
                                                               ng-change="kpiPCMale=kpiPCMale==''?0:getInt(kpiPCMale)"
                                                               placeholder="Male"
                                                               class="form-control">
                                                    </div>
                                                    <div class="input-group">
                                                            <span class="input-group-addon">
                                                                <i class="fa fa-female"></i>
                                                            </span>
                                                        <input type="text" ng-model="kpiPCFemale"
                                                               placeholder="Female"
                                                               class="form-control">
                                                    </div>
                                                </fieldset>
                                            </li>
                                            <li class="form-group col-md-4">
                                                <fieldset class="fieldset ">
                                                    <legend class="legend">APC</legend>
                                                    <div class="input-group margin-bottom-input">
                                                            <span class="input-group-addon">
                                                                <i class="fa fa-male"></i>
                                                            </span>
                                                        <input type="text" ng-model="kpiAPCMale" placeholder="Male"
                                                               class="form-control">
                                                    </div>
                                                    <div class="input-group">
                                                            <span class="input-group-addon">
                                                                <i class="fa fa-female"></i>
                                                            </span>
                                                        <input type="text" ng-model="kpiAPCFemale"
                                                               placeholder="Female"
                                                               class="form-control">
                                                    </div>
                                                </fieldset>
                                            </li>
                                            <li class="form-group col-md-4">
                                                <fieldset class="fieldset ">
                                                    <legend class="legend">Ansar</legend>
                                                    <div class="input-group margin-bottom-input">
                                                            <span class="input-group-addon">
                                                                <i class="fa fa-male"></i>
                                                            </span>
                                                        <input type="text" ng-model="kpiAnsarMale"
                                                               placeholder="Male"
                                                               class="form-control">
                                                    </div>
                                                    <div class="input-group">
                                                            <span class="input-group-addon">
                                                                <i class="fa fa-female"></i>
                                                            </span>
                                                        <input type="text" ng-model="kpiAnsarFemale"
                                                               placeholder="Female"
                                                               class="form-control">
                                                    </div>
                                                </fieldset>
                                            </li>
                                        </ul>
                                        <div>
                                            <span> <i class="fa fa-male"></i>-&nbsp;Male(পুরুষ)</span>&nbsp;&nbsp;
                                            <span> <i class="fa fa-female"></i>-&nbsp;Female(মহিলা)</span>
                                        </div>
                                    </div>
                                </div>
                                {{--<div class="table-responsive" style="max-height: 370px">--}}

                                {{--<table ng-show="isAdmin" class="table table-bordered" id="pc-table">--}}
                                {{--<tr class="info">--}}
                                {{--<th>ID</th>--}}
                                {{--<th>Name</th>--}}
                                {{--<th>Division</th>--}}
                                {{--<th>District</th>--}}
                                {{--<th>Thana</th>--}}
                                {{--<th>Sex</th>--}}
                                {{--<th>Designation</th>--}}
                                {{--</tr>--}}
                                {{--<tbody>--}}
                                {{--<tr ng-show="noAnsar" class="warning">--}}
                                {{--<td colspan="7">No Ansar Found to Send Offer</td>--}}
                                {{--</tr>--}}
                                {{--<tr ng-repeat="ansar in selectedAnsar" ng-hide="noAnsar">--}}
                                {{--<td>[[ansar.ansar_id]]</td>--}}
                                {{--<td>[[ansar.ansar_name_bng]]</td>--}}
                                {{--<td>[[ansar.division_name_eng]]</td>--}}
                                {{--<td>[[ansar.unit_name_eng]]</td>--}}
                                {{--<td>[[ansar.thana_name_eng]]</td>--}}
                                {{--<td>[[ansar.sex]]</td>--}}
                                {{--<td>[[ansar.name_bng]]</td>--}}
                                {{--</tr>--}}
                                {{--</tbody>--}}
                                {{--</table>--}}
                                {{--</div>--}}
                                <div class="form-group" ng-if="isAdmin" style="margin-top: 10px">
                                    <div class="row">
                                        <div class="col-sm-2 col-sm-offset-2">
                                            <label class="control-label">
                                                District to send offer
                                            </label>
                                        </div>
                                        <div class="col-sm-4">
                                            <select class="form-control" ng-change="checkChange()"
                                                    ng-model="data.offeredDistrict">
                                                <option value="">--Select a district to send offer--</option>
                                                <option ng-repeat="district in offeredDistrictList"
                                                        ng-disabled="checkDistrict(updatedDistrict,district)"
                                                        value="[[district.id]]">[[district.unit_name_eng]]
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <button class="btn btn-primary pull-right" ng-click="loadAnsar()"
                        ng-disabled="(isAdmin&&!data.offeredDistrict)">
                    <i ng-show="showLoadScreen" class="fa fa-send"></i><i ng-hide="showLoadScreen"
                                                                          class="fa fa-spinner fa-pulse"></i>
                    [[buttonText]]
                </button>
                <div class="clearfix"></div>
            @endif
            {{--<div id="offer-option" class="modal fade" ng-style="modalStyle" role="dialog">--}}
            {{--<div class="modal-dialog">--}}
            {{--<div class="modal-content">--}}
            {{--<div class="modal-header">--}}
            {{--<button type="button" class="close" data-dismiss="modal">&times;</button>--}}
            {{--<h3 class="modal-title">Offer Option</h3>--}}
            {{--</div>--}}
            {{--<div class="modal-body">--}}
            {{--<div class="register-box" style="width: auto;margin: 0">--}}
            {{--<div class="register-box-body">--}}
            {{--<div class="row">--}}
            {{--<div class="row" ng-show="isAdmin" style="margin: 0 !important;">--}}
            {{--<div class="col-md-5">--}}
            {{--<div class="form-group">--}}
            {{--<label class="control-label"> Select District</label>--}}
            {{--<select name="district" ng-model="selectedDistrict"--}}
            {{--class="form-control" multiple>--}}
            {{--<option ng-repeat="district in allDistrict"--}}
            {{--value=[[$index]]>--}}
            {{--[[district.unit_name_eng]]--}}
            {{--</option>--}}
            {{--</select>--}}
            {{--</div>--}}
            {{--</div>--}}
            {{--<div class="col-md-2">--}}
            {{--<div class="form-group">--}}
            {{--<label class="control-label"--}}
            {{--style="visibility: hidden">Action</label>--}}
            {{--<ul style="list-style: none;padding: 0;margin: 0 ">--}}
            {{--<li style="padding-top: 6px;padding-left: 10px">--}}
            {{--<button class="btn btn-default" ng-click="addDistrict()">--}}
            {{--<i class="fa fa-long-arrow-right"></i>--}}
            {{--</button>--}}
            {{--</li>--}}
            {{--<li style="padding-top: 6px;padding-left: 10px">--}}
            {{--<button class="btn btn-default" ng-click="removeDistrict()">--}}
            {{--<i class="fa fa-long-arrow-left"></i>--}}
            {{--</button>--}}
            {{--</li>--}}
            {{--</ul>--}}
            {{--</div>--}}
            {{--</div>--}}
            {{--<div class="col-md-5">--}}
            {{--<div class="form-group">--}}
            {{--<label class="control-label"> Selected District</label>--}}
            {{--<select name="district" ng-model="removedDistrict"--}}
            {{--class="form-control" multiple>--}}
            {{--<option ng-repeat="district in updatedDistrict"--}}
            {{--value=[[$index]]>--}}
            {{--[[district.unit_name_eng]]--}}
            {{--</option>--}}
            {{--</select>--}}
            {{--</div>--}}
            {{--</div>--}}
            {{--</div>--}}
            {{--<div class="col-md-12">--}}
            {{--<ul style="list-style: none;margin-left: -15px !important;padding: 0"--}}
            {{--class="row">--}}
            {{--<li class="col-md-4">--}}
            {{--<fieldset class="fieldset ">--}}
            {{--<legend class="legend">PC</legend>--}}
            {{--<div class="input-group margin-bottom-input">--}}
            {{--<span class="input-group-addon">--}}
            {{--<i class="fa fa-male"></i>--}}
            {{--</span>--}}
            {{--<input type="text" ng-model="kpiPCMale" placeholder="Male"--}}
            {{--class="form-control">--}}
            {{--</div>--}}
            {{--<div class="input-group">--}}
            {{--<span class="input-group-addon">--}}
            {{--<i class="fa fa-female"></i>--}}
            {{--</span>--}}
            {{--<input type="text" ng-model="kpiPCFemale"--}}
            {{--placeholder="Female"--}}
            {{--class="form-control">--}}
            {{--</div>--}}
            {{--</fieldset>--}}
            {{--</li>--}}
            {{--<li class="form-group col-md-4">--}}
            {{--<fieldset class="fieldset ">--}}
            {{--<legend class="legend">APC</legend>--}}
            {{--<div class="input-group margin-bottom-input">--}}
            {{--<span class="input-group-addon">--}}
            {{--<i class="fa fa-male"></i>--}}
            {{--</span>--}}
            {{--<input type="text" ng-model="kpiAPCMale" placeholder="Male"--}}
            {{--class="form-control">--}}
            {{--</div>--}}
            {{--<div class="input-group">--}}
            {{--<span class="input-group-addon">--}}
            {{--<i class="fa fa-female"></i>--}}
            {{--</span>--}}
            {{--<input type="text" ng-model="kpiAPCFemale"--}}
            {{--placeholder="Female"--}}
            {{--class="form-control">--}}
            {{--</div>--}}
            {{--</fieldset>--}}
            {{--</li>--}}
            {{--<li class="form-group col-md-4">--}}
            {{--<fieldset class="fieldset ">--}}
            {{--<legend class="legend">Ansar</legend>--}}
            {{--<div class="input-group margin-bottom-input">--}}
            {{--<span class="input-group-addon">--}}
            {{--<i class="fa fa-male"></i>--}}
            {{--</span>--}}
            {{--<input type="text" ng-model="kpiAnsarMale"--}}
            {{--placeholder="Male"--}}
            {{--class="form-control">--}}
            {{--</div>--}}
            {{--<div class="input-group">--}}
            {{--<span class="input-group-addon">--}}
            {{--<i class="fa fa-female"></i>--}}
            {{--</span>--}}
            {{--<input type="text" ng-model="kpiAnsarFemale"--}}
            {{--placeholder="Female"--}}
            {{--class="form-control">--}}
            {{--</div>--}}
            {{--</fieldset>--}}
            {{--</li>--}}
            {{--</ul>--}}
            {{--<button ng-click="loadAnsar()" ng-disabled="showLoadingAnsar"--}}
            {{--class="btn btn-default pull-right">--}}
            {{--<i class="fa fa-download" ng-hide="showLoadingAnsar"></i>--}}
            {{--<i class="fa fa-spinner fa-pulse" ng-show="showLoadingAnsar"></i>--}}
            {{--Load--}}
            {{--</button>--}}
            {{--</div>--}}
            {{--</div>--}}
            {{--</div>--}}
            {{--</div>--}}
            {{--</div>--}}
            {{--</div>--}}
            {{--</div>--}}
            {{--</div>--}}
            {{--<div>[[error]]</div>--}}
        </section>
    </div>
    <script>
        $(function () {
            $("#pc-table").sortTable()
        });
    </script>
@stop