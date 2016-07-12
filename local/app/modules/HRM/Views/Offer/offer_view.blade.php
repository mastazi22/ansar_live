@extends('template.master')
@section('title','Offer List')
@section('breadcrumb')
    {!! Breadcrumbs::render('offer_information') !!}
@endsection
@section('content')
    <script>

        GlobalApp.controller('OfferController', function ($scope, $http, $interval) {
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
            $scope.data = {offeredDistrict: ""};
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
                    $scope.allDistrict = response.data;
                })
            }
            else {
                $scope.isAdmin = false;
                $scope.negateDistrictId = $scope.districtId;
            }
            $scope.removeDistrict = function () {
                for (var i = 0; i < $scope.removedDistrict.length; i++) {
                    $scope.allDistrict.push($scope.updatedDistrict[$scope.removedDistrict[i] - i])
                    $scope.updatedDistrict.splice($scope.removedDistrict[i] - i, 1)
                }
                $scope.removedDistrict = [];
            }
            $scope.loadAnsar = function () {
                var total = parseInt($scope.kpiPCMale) + parseInt($scope.kpiPCFemale) + parseInt($scope.kpiAPCMale) + parseInt($scope.kpiAPCFemale) + parseInt($scope.kpiAnsarMale) + parseInt($scope.kpiAnsarFemale);
                if (total > $scope.offerQuota) {
                    alert("Your offer limit exit total number of offer you want to send");
                    return;
                }
                $scope.buttonText = "Loading Ansar"
                $scope.showLoadScreen = false;
                var data = {
                    ansar_info: {
                        pc_male: $scope.kpiPCMale,
                        pc_female: $scope.kpiPCFemale,
                        apc_male: $scope.kpiAPCMale,
                        apc_female: $scope.kpiAPCFemale,
                        ansar_male: $scope.kpiAnsarMale,
                        ansar_female: $scope.kpiAnsarFemale,
                        district: $scope.selectedDistrict.filter(function (v) {
                            return v != undefined;
                        }),
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
                    $scope.countDown = $scope.countDown - 1
                }, 1000)
            }
            $scope.$watch('countDown', function (n, o) {
                if (n <= 0) {
                    $interval.cancel(promis);
                    window.location.assign('{{URL::previous()}}')
                }

            })

        })
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
    <div ng-controller="OfferController" close-modal>
        {{--<div class="breadcrumbplace">--}}
        {{--{!! Breadcrumbs::render('offer_information') !!}--}}
        {{--</div>--}}
        <section class="content">
            @if($isFreeze)
                <h3 style="text-align: center">You have <span class="text-warning">{{$isFreeze}}</span> freezed ansar in
                    your district.Unfreeze them then you are eligible to send offer
                    <br>Redirect in ...<span class="text-danger" ng-init="startCountDown()">[[countDown]]</span> Second
                </h3>
            @else
                <div class="box box-solid">
                    <div class="box-body">
                        <h4 ng-if="!isAdmin">You have total <span style="text-decoration: underline"
                                                                  ng-class="{'text-green':offerQuota>50,'text-danger':offerQuota<=10}">[[offerQuota]]</span>
                            offer left</h4>

                        <div class="row">
                            <div class="col-md-4" ng-if="isAdmin">
                                <h4>Select a district</h4>
                                <ul class="offer-district">
                                    <li ng-repeat="unit in allDistrict">
                                        <input ng-change="addDistrict()" type="checkbox" class="check-boxx"
                                               ng-model="selectedDistrict[$index]" ng-true-value="[[unit.id]]"
                                               ng-false-value="" id="id-[[unit.id]]" value="[[unit.id]]" name="units[]">
                                        <label for="id-[[unit.id]]" class="check-label">
                                            <i class="fa" ng-class="{'fa-check':selectedDistrict[$index]}"></i>
                                            [[unit.unit_name_eng]]</label>
                                    </li>
                                </ul>
                            </div>
                            <div class="col-md-8">
                                <div class="form-group">
                                    <h4 style="border-bottom: 1px solid #111111">PC</h4>
                                    <label>Male</label>

                                    <div class="input-group margin-bottom-input">
                                                            <span class="input-group-addon">
                                                                <i class="fa fa-male"></i>
                                                            </span>
                                        <input type="text" ng-model="kpiPCMale"
                                               ng-change="kpiPCMale=kpiPCMale==''?0:getInt(kpiPCMale)"
                                               placeholder="Male"
                                               class="form-control">
                                    </div>
                                    <label>Female</label>

                                    <div class="input-group">
                                                            <span class="input-group-addon">
                                                                <i class="fa fa-female"></i>
                                                            </span>
                                        <input type="text" ng-model="kpiPCFemale"
                                               placeholder="Female"
                                               class="form-control">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <h4 style="border-bottom: 1px solid #111111">APC</h4>
                                    <label>Male</label>

                                    <div class="input-group margin-bottom-input">
                                                            <span class="input-group-addon">
                                                                <i class="fa fa-male"></i>
                                                            </span>
                                        <input type="text" ng-model="kpiAPCMale" placeholder="Male"
                                               class="form-control">
                                    </div>
                                    <label>Female</label>

                                    <div class="input-group">
                                                            <span class="input-group-addon">
                                                                <i class="fa fa-female"></i>
                                                            </span>
                                        <input type="text" ng-model="kpiAPCFemale"
                                               placeholder="Female"
                                               class="form-control">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <h4 style="border-bottom: 1px solid #111111">Ansar</h4>
                                    <label>Male</label>

                                    <div class="input-group margin-bottom-input">
                                                            <span class="input-group-addon">
                                                                <i class="fa fa-male"></i>
                                                            </span>
                                        <input type="text" ng-model="kpiAnsarMale"
                                               placeholder="Male"
                                               class="form-control">
                                    </div>
                                    <label>Female</label>

                                    <div class="input-group">
                                                            <span class="input-group-addon">
                                                                <i class="fa fa-female"></i>
                                                            </span>
                                        <input type="text" ng-model="kpiAnsarFemale"
                                               placeholder="Female"
                                               class="form-control">
                                    </div>
                                </div>
                                <div class="form-group" ng-if="isAdmin">
                                    <div class="row">
                                        <div class="col-sm-3">
                                            <label class="control-label">
                                                District to send offer
                                            </label>
                                        </div>
                                        <div class="col-sm-9">
                                            <select class="form-control" ng-change="checkChange()" ng-model="data.offeredDistrict">
                                                <option value="">--Select a district to send offer--</option>
                                                <option ng-repeat="district in allDistrict" ng-disabled="selectedDistrict.indexOf(district.id)>=0" value="[[district.id]]">[[district.unit_name_eng]]
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
                <button class="btn btn-primary pull-right" ng-click="loadAnsar()" ng-disabled="(isAdmin&&!data.offeredDistrict)">
                    <i ng-show="showLoadScreen" class="fa fa-send"></i><i ng-hide="showLoadScreen" class="fa fa-spinner fa-pulse"></i>
                    [[buttonText]]
                </button>
                <div class="clearfix"></div>
            @endif
        </section>
    </div>
    <script>
        $(function () {
            $("#pc-table").sortTable()
        });
    </script>
@stop