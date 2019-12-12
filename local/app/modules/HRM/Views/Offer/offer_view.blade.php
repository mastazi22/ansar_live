@extends('template.master')
@section('title','Offer')
@section('breadcrumb')
    {!! Breadcrumbs::render('offer_information') !!}
@endsection
@section('content')
    <style>
        #offer-view * {
            font-size: 20px !important;
        }

        ul.nav-tabs li.active a {
            background: #01655d !important;
            color: white !important;
        }
    </style>
    <script>
        GlobalApp.controller('OfferController', function ($scope, $http, $interval, notificationService) {
            $scope.kpiPCMale = '';
            $scope.kpiPCFemale = '';
            $scope.kpiAPCMale = '';
            $scope.kpiAPCFemale = '';
            $scope.kpiAnsarMale = '';
            $scope.kpiAnsarFemale = '';
            $scope.alerts = [];
            $scope.noAnsar = true;
            $scope.offerAnsarId = [];
            $scope.showLoadScreen = true;
            $scope.showLoadingAnsar = false;
            $scope.modalStyle = {};
            $scope.selectedDistrict = [];
            $scope.updatedDistrict = [];
            $scope.removedDistrict = [];
            $scope.allDistrict = [];
            $scope.quotaLoading = true;
            $scope.data = {offeredDistrict: ""};
            $scope.selectedAnsar = [];
            $scope.result = {};
            $scope.countDown = 10;
            $scope.buttonText = "Send Offer";
            $scope.offerQuota = 0;
            $scope.negateDistrictId = null;
            var promis;
            $scope.districtId = '{{Auth::user()->district_id}}';
            var userType = '{{Auth::user()->type}}';
            if (parseInt(userType) == 11 || parseInt(userType) == 33 || parseInt(userType) == 77) {
                $scope.isAdmin = true;
                $http({
                    url: '{{URL::to('HRM/DistrictName')}}',
                    type: 'get'
                }).then(function (response) {
                    $scope.allDistrict = response.data;
                })
            } else {
                $scope.isAdmin = false;
                $scope.negateDistrictId = $scope.districtId;
            }
            $scope.removeDistrict = function () {
                for (var i = 0; i < $scope.removedDistrict.length; i++) {
                    $scope.allDistrict.push($scope.updatedDistrict[$scope.removedDistrict[i] - i]);
                    $scope.updatedDistrict.splice($scope.removedDistrict[i] - i, 1)
                }
                $scope.removedDistrict = [];
            };
            $scope.loadAnsar = function () {
                var total = parseInt($scope.kpiPCMale) + parseInt($scope.kpiPCFemale) + parseInt($scope.kpiAPCMale) + parseInt($scope.kpiAPCFemale) + parseInt($scope.kpiAnsarMale) + parseInt($scope.kpiAnsarFemale);
                if (total > $scope.offerQuota) {
                    alert("Your offer limit exit total number of offer you want to send");
                    return;
                }
                $scope.buttonText = "Loading Ansar";
                $scope.showLoadScreen = false;
                var data = {
                    pc_male: $scope.kpiPCMale || 0,
                    pc_female: $scope.kpiPCFemale || 0,
                    apc_male: $scope.kpiAPCMale || 0,
                    apc_female: $scope.kpiAPCFemale || 0,
                    ansar_male: $scope.kpiAnsarMale || 0,
                    ansar_female: $scope.kpiAnsarFemale || 0,
                    district: $scope.selectedDistrict.filter(function (v) {
                        return v != undefined;
                    }),
                    exclude_district: (parseInt(userType) == 11 ? null : $scope.districtId)
                };
                $scope.showLoadingAnsar = true;
                $scope.modalStyle = {'display': 'block'};
                $http({
                    url: '{{URL::to('HRM/kpi_list')}}',
                    method: 'post',
                    data: angular.toJson(data)
                }).then(function (response) {
                    console.log(response.data);
                    //alert(JSON.stringify(response.data));
//                    if (response.data.length > 0) {
//                        $scope.selectedAnsar = response.data;
//                        $scope.noAnsar = false;
//                        $scope.sendOffer();
//                    }
//                    else {
//                        $scope.noAnsar = true;
//                        $scope.showLoadScreen = true;
//                        alert("No ansar Available")
//                        $scope.buttonText = "Send Offer"
//                    }

                }, function (response) {
                    if (response.status == 400) {
                        $scope.alerts = [];
                        $scope.alerts.push(response.data);
                        window.scrollTo(0, 0)
                    }
                    $scope.showLoadingAnsar = false;
                    $scope.buttonText = "Send Offer"
                })
            };
            $scope.sendOffer = function () {
                $scope.showLoadingAnsar = true;
                $scope.buttonText = "Sending Offer...";
                $http({
                    url: '{{URL::to('HRM/send_offer')}}',
                    data: angular.toJson({
                        pc_male: $scope.kpiPCMale || 0,
                        pc_female: $scope.kpiPCFemale || 0,
                        apc_male: $scope.kpiAPCMale || 0,
                        apc_female: $scope.kpiAPCFemale || 0,
                        ansar_male: $scope.kpiAnsarMale || 0,
                        ansar_female: $scope.kpiAnsarFemale || 0,
                        district: $scope.selectedDistrict.filter(function (v) {
                            return v != undefined;
                        }),
                        exclude_district: (parseInt(userType) == 11 ? null : $scope.districtId),
                        district_id: $scope.isAdmin ? $scope.data.offeredDistrict : $scope.districtId,
                        type: 'panel',
                        offer_limit: $scope.offerQuota
                    }),
                    method: 'post'
                }).then(
                    function (response) {
                        $scope.showLoadScreen = true;
                        $scope.alerts = [];
                        $scope.alerts.push(response.data);
                        $scope.buttonText = "Send Offer";
                        notificationService.notify(response.data.type, response.data.message);
                        $scope.kpiPCMale = '';
                        $scope.kpiPCFemale = '';
                        $scope.kpiAPCMale = '';
                        $scope.kpiAPCFemale = '';
                        $scope.kpiAnsarMale = '';
                        $scope.kpiAnsarFemale = '';
                        $scope.getOfferCount();
                    },
                    function (response) {
                        $scope.alerts = [];
                        notificationService.notify(response.data.type, response.data.message);
                        $scope.showLoadScreen = true;
                        $scope.buttonText = "Send Offer"
                    }
                )
            };
            $scope.getOfferCount = function () {
                $scope.quotaLoading = true;
                $http({
                    url: "{{URL::to('HRM/get_offer_count')}}",
                    method: 'get'
                }).then(function (response) {
                    $scope.offerQuota = response.data.total_offer;
                    $scope.quotaLoading = false;
                }, function (response) {

                })
            };
            $scope.checkDistrict = function (a, b) {
                var s = false;
                a.forEach(function (a) {
                    if (a.id == b.id) {
                        s = true;
                    }
                });
                return s;
            };
            $scope.getOfferCount();
            $scope.getInt = function (a) {
                if (isNaN(a)) {
                    return '';
                } else {
                    return a;
                }
            };
            $scope.startCountDown = function () {
                promis = $interval(function () {
                    $scope.countDown = $scope.countDown - 1
                }, 1000)
            };
            $scope.$watch('countDown', function (n, o) {
                if (n <= 0) {
                    $interval.cancel(promis);
                    window.location.assign('{{URL::previous()}}')
                }
            });
            $scope.closeAlert = function () {
                $scope.alerts = [];
            };
            $scope.getMessageString = function () {
                var message = "Are you sure to send offer?<br/><span style='color: red;font-size: 20px;display: block;'>";
                if ($scope.kpiPCMale) message += "PC(Male):" + $scope.kpiPCMale + "<br>";
                if ($scope.kpiPCFemale) message += "PC(Female):" + $scope.kpiPCFemale + "<br>";
                if ($scope.kpiAPCMale) message += "APC(Male):" + $scope.kpiAPCMale + "<br>";
                if ($scope.kpiAPCFemale) message += "APC(Female):" + $scope.kpiAPCFemale + "<br>";
                if ($scope.kpiAnsarMale) message += "ANSAR(Male):" + $scope.kpiAnsarMale + "<br>";
                if ($scope.kpiAnsarFemale) message += "ANSAR(Female):" + $scope.kpiAnsarFemale + "<br>";
                return message += "</span>";
            };
            $scope.message = $scope.getMessageString();
            $scope.$watch("[kpiPCMale,kpiPCFemale,kpiAPCMale,kpiAPCFemale,kpiAnsarMale,kpiAnsarFemale]", function (n, o) {
                $scope.message = $scope.getMessageString();
            }, true)

            //checkbox watch
            $scope.$watch("kpiPCMaleStatus", function (n, o) {
                if (!n) $scope.kpiPCMale = '';
            });
            $scope.$watch("kpiPCFemaleStatus", function (n, o) {
                if (!n) $scope.kpiPCFemale = '';
            });
            $scope.$watch("kpiAPCMaleStatus", function (n, o) {
                if (!n) $scope.kpiAPCMale = '';
            });
            $scope.$watch("kpiAPCFemaleStatus", function (n, o) {
                if (!n) $scope.kpiAPCFemale = '';
            });
            $scope.$watch("kpiAnsarMaleStatus", function (n, o) {
                if (!n) $scope.kpiAnsarMale = '';
            });
            $scope.$watch("kpiAnsarFemaleStatus", function (n, o) {
                if (!n) $scope.kpiAnsarFemale = '';
            });
        })

    </script>
    <div ng-controller="OfferController" id="offer-view">
        <section class="content">
            @if($isFreeze)
                <h3 style="text-align: center">You have <span class="text-warning">{{$isFreeze}}</span> freezed ansar in
                    your district.Unfreeze them then you are eligible to send offer
                    <br>Redirect in ...<span class="text-danger" ng-init="startCountDown()">[[countDown]]</span> Second
                </h3>
            @else
                <div class="row">
                    <div ng-class="{'col-md-10 col-centered':isAdmin,'col-md-8 col-centered':!isAdmin}">
                        <div class="box box-solid">
                            <div class="box-body">
                                <h4 ng-if="!isAdmin" style="text-align: right;">You have total
                                    <span ng-hide="quotaLoading" style="text-decoration: underline"
                                          ng-class="{'text-green':offerQuota>50,'text-danger':offerQuota<=10}">[[offerQuota]]</span>
                                    <i ng-show="quotaLoading" class="fa fa-pulse fa-spinner"></i>
                                    offer left
                                </h4>

                                <div class="row">
                                    <div class="col-md-4" ng-if="isAdmin">
                                        <h4>@lang('title.unit')</h4>
                                        <ul class="offer-district" style="padding-left:0;">
                                            <li ng-repeat="unit in allDistrict">
                                                <input ng-change="addDistrict()" type="checkbox" class="check-boxx"
                                                       ng-model="selectedDistrict[$index]" ng-true-value="[[unit.id]]"
                                                       ng-false-value="" id="id-[[unit.id]]" value="[[unit.id]]"
                                                       name="units[]">
                                                <label for="id-[[unit.id]]" class="check-label"><i class="fa"
                                                                                                   ng-class="{'fa-check':selectedDistrict[$index]}"></i>[[unit.unit_name_eng]]</label>
                                            </li>
                                        </ul>
                                    </div>
                                    <div ng-class="{'col-md-8':isAdmin,'col-md-12':!isAdmin}">
                                        <ul class="nav mb-3 nav-tabs" id="pills-tab" role="tablist">
                                            <li class="nav-item active">
                                                <a class="nav-link" id="pills-ansar-tab" data-toggle="pill"
                                                   href="#pills-ansar" role="tab" aria-controls="pills-ansar"
                                                   aria-selected="false">Ansar</a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link" id="pills-pc-tab" data-toggle="pill"
                                                   href="#pills-pc" role="tab" aria-controls="pills-pc"
                                                   aria-selected="true">PC</a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link" id="pills-apc-tab" data-toggle="pill"
                                                   href="#pills-apc" role="tab" aria-controls="pills-apc"
                                                   aria-selected="false">APC</a>
                                            </li>
                                        </ul>
                                        <div class="tab-content" id="pills-tabContent">
                                            <div class="tab-pane fade" id="pills-pc" role="tabpanel"
                                                 aria-labelledby="pills-pc-tab" style="padding: 3% 0 0 0">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="kpiPCMaleStatus">
                                                                <input id="kpiPCMaleStatus" type="checkbox" value="true"
                                                                       ng-model="kpiPCMaleStatus">&nbsp;<i
                                                                        class="fa fa-male male"
                                                                        style="color: black;"></i>&nbsp;Male/পুরুষ
                                                            </label>
                                                            <input type="number" ng-model="kpiPCMale" min="0"
                                                                   placeholder="Put your number here"
                                                                   max="[[offerQuota]]" class="form-control"
                                                                   ng-disabled="!kpiPCMaleStatus" pattern="[0-9]+"
                                                                   onkeydown="return event.keyCode !== 69 && event.keyCode !== 101"
                                                                   ng-change="kpiPCMale=kpiPCMale==''?'':getInt(kpiPCMale)">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="kpiPCFemaleStatus">
                                                                <input id="kpiPCFemaleStatus" type="checkbox"
                                                                       value="true"
                                                                       ng-model="kpiPCFemaleStatus">&nbsp;<i
                                                                        class="fa fa-female female"
                                                                        style="color: black;"></i>&nbsp;Female/মহিলা
                                                            </label>
                                                            <input type="number" ng-model="kpiPCFemale" min="0"
                                                                   placeholder="Put your number here"
                                                                   max="[[offerQuota]]" class="form-control"
                                                                   ng-disabled="!kpiPCFemaleStatus"
                                                                   onkeydown="return event.keyCode !== 69 && event.keyCode !== 101"
                                                                   ng-change="kpiPCFemale=kpiPCFemale==''?'':getInt(kpiPCFemale)">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="tab-pane fade" id="pills-apc" role="tabpanel"
                                                 aria-labelledby="pills-apc-tab" style="padding: 3% 0 0 0">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="kpiAPCMaleStatus">
                                                                <input id="kpiAPCMaleStatus" type="checkbox"
                                                                       value="true"
                                                                       ng-model="kpiAPCMaleStatus">&nbsp;<i
                                                                        class="fa fa-male male"
                                                                        style="color: black;"></i>&nbsp;Male/পুরুষ
                                                            </label>
                                                            <input type="number" ng-model="kpiAPCMale" min="0"
                                                                   placeholder="Put your number here"
                                                                   max="[[offerQuota]]" class="form-control"
                                                                   ng-disabled="!kpiAPCMaleStatus"
                                                                   onkeydown="return event.keyCode !== 69 && event.keyCode !== 101"
                                                                   ng-change="kpiAPCMale=kpiAPCMale==''?0:getInt(kpiAPCMale)">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="kpiAPCFemaleStatus">
                                                                <input id="kpiAPCFemaleStatus" type="checkbox"
                                                                       value="true" ng-model="kpiAPCFemaleStatus">&nbsp;<i
                                                                        class="fa fa-female female"
                                                                        style="color: black;"></i>&nbsp;Female/মহিলা
                                                            </label>
                                                            <input type="number" ng-model="kpiAPCFemale" min="0"
                                                                   placeholder="Put your number here"
                                                                   max="[[offerQuota]]" class="form-control"
                                                                   onkeydown="return event.keyCode !== 69 && event.keyCode !== 101"
                                                                   ng-change="kpiAPCFemale=kpiAPCFemale==''?0:getInt(kpiAPCFemale)"
                                                                   ng-disabled="!kpiAPCFemaleStatus">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="tab-pane fade active in" id="pills-ansar" role="tabpanel"
                                                 aria-labelledby="pills-ansar-tab" style="padding: 3% 0 0 0">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="kpiAnsarMaleStatus">
                                                                <input id="kpiAnsarMaleStatus" type="checkbox"
                                                                       value="true"
                                                                       ng-model="kpiAnsarMaleStatus">&nbsp;<i
                                                                        class="fa fa-male male"
                                                                        style="color: black;"></i>&nbsp;Male/পুরুষ
                                                            </label>
                                                            <input type="number" ng-model="kpiAnsarMale" min="0"
                                                                   placeholder="Put your number here"
                                                                   max="[[offerQuota]]" class="form-control"
                                                                   onkeydown="return event.keyCode !== 69 && event.keyCode !== 101"
                                                                   ng-change="kpiAnsarMale=kpiAnsarMale==''?0:getInt(kpiAnsarMale)"
                                                                   ng-disabled="!kpiAnsarMaleStatus">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="kpiAnsarFemaleStatus">
                                                                <input id="kpiAnsarFemaleStatus" type="checkbox"
                                                                       value="true" ng-model="kpiAnsarFemaleStatus">&nbsp;<i
                                                                        class="fa fa-female female"
                                                                        style="color: black;"></i>&nbsp;Female/মহিলা
                                                            </label>
                                                            <input type="number" ng-model="kpiAnsarFemale" min="0"
                                                                   placeholder="Put your number here"
                                                                   max="[[offerQuota]]" class="form-control"
                                                                   onkeydown="return event.keyCode !== 69 && event.keyCode !== 101"
                                                                   ng-change="kpiAnsarFemale=kpiAnsarFemale==''?0:getInt(kpiAnsarFemale)"
                                                                   ng-disabled="!kpiAnsarFemaleStatus">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group" ng-if="isAdmin" style="margin-top: 5%">
                                            <div class="row">
                                                <div class="col-sm-5">
                                                    <label class="control-label">
                                                        District to send offer
                                                    </label>
                                                </div>
                                                <div class="col-sm-7">
                                                    <select class="form-control" ng-change="checkChange()"
                                                            style="font-size: 16px!important;"
                                                            ng-model="data.offeredDistrict">
                                                        <option value="">--@lang('title.unit') to send offer--</option>
                                                        <option ng-repeat="district in allDistrict"
                                                                ng-disabled="selectedDistrict.indexOf(district.id)>=0"
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
                        <button class="btn btn-primary pull-right" confirm callback="sendOffer()" sp-message="message"
                                ng-disabled="(isAdmin&&!data.offeredDistrict)||quotaLoading"><i ng-show="showLoadScreen"
                                                                                                class="fa fa-send"></i><i
                                    ng-hide="showLoadScreen" class="fa fa-spinner fa-pulse"></i>[[buttonText]]
                        </button>
                        <div class="clearfix"></div>
                    </div>
                </div>
            @endif
        </section>
    </div>
    <script>
        $(function () {
            $("#pc-table").sortTable()
        });
    </script>
@stop