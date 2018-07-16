{{--User: Shreya--}}
{{--Date: 11/8/2015--}}
{{--Time: 11:48 AM--}}

@extends('template.master')
@section('title','Embodiment')
@section('breadcrumb')
    {!! Breadcrumbs::render('embodiment_entry') !!}
@endsection
@section('content')
    <script>
        GlobalApp.controller('NewEmbodimentController', function ($scope, $http, notificationService, $timeout, $rootScope) {
            $scope.ansarId = "";
            $scope.errors = {division_name_eng: [], thana_name_eng: [], kpi_id: []};
            $scope.eMessage = {
                division_name_eng: $scope.errors.division_name_eng[0],
                thana_name_eng: $scope.errors.thana_name_eng[0],
                kpi_id: $scope.errors.kpi_id[0]
            };

            $scope.bankDataErrorMessage = "";
            $scope.bank_name = "";
            $scope.prefer_choice = "";
            $scope.mobile_bank_account_no = "";
            $scope.mobile_bank_type = "";
            $scope.account_no = "";
            $scope.branch_name = "";

            $scope.clickedAsnar = "";
            $scope.printLetter = [{}, {}];
            $scope.queue = [];
            $scope.ee = true;
            $scope.ansarDetail = {};
            $scope.eData = [];
            $scope.units = [];
            $scope.thanas = [];
            $scope.totalLength = 0;
            $scope.ansar_ids = [];
            $scope.listedAnsar = [];
            $scope.loadingKpi = false;
            $scope.loadingDetail = false;
            $scope.loadingAnsar = false;
            $scope.joining_date = "";
            $scope.isAnsarAvailable = false;
            $scope.responseData = '';
            $scope.ea = [];
            $scope.hh = 0;
            $scope.selectAll = false;
            $scope.msg = "";
            $scope.bankList = ["AB Bank Limited", "Agrani Bank Limited", "Al-Arafah Islami Bank Limited", "Bangladesh Commerce Bank Limited",
                "Bangladesh Development Bank Limited", "Bangladesh Krishi Bank", "Bank Al-Falah Limited", "Bank Asia Limited", "BASIC Bank Limited", "BRAC Bank Limited", "Citibank N.A",
                "Commercial Bank of Ceylon Limited", "Dhaka Bank Limited", "Dutch-Bangla Bank Limited", "Eastern Bank Limited", "EXIM Bank Limited", "First Security Islami Bank Limited",
                "Habib Bank Ltd.", "ICB Islamic Bank Ltd.", "IFIC Bank Limited", "Islami Bank Bangladesh Ltd", "Jamuna Bank Ltd", "Janata Bank Limited",
                "Meghna Bank Limited", "Mercantile Bank Limited", "Midland Bank Limited", "Mutual Trust Bank Limited", "National Bank Limited", "National Bank of Pakistan",
                "National Credit & Commerce Bank Ltd", "NRB Commercial Bank Limited", "One Bank Limited", "Premier Bank Limited", "Prime Bank Ltd", "Pubali Bank Limited",
                "Rajshahi Krishi Unnayan Bank", "Rupali Bank Limited", "Shahjalal Bank Limited", "Shimanto Bank Limited", "Social Islami Bank Ltd.",
                "Sonali Bank Limited", "South Bangla Agriculture & Commerce Bank Limited", "Southeast Bank Limited", "Standard Bank Limited",
                "Standard Chartered Bank", "State Bank of India", "The City Bank Ltd.", "The Hong Kong and Shanghai Banking Corporation. Ltd.",
                "Trust Bank Limited", "Union Bank Limited", "United Commercial Bank Limited", "Uttara Bank Limited", "Woori Bank"
            ];

            $scope.mobileBankType = [
                "bkash", "rocket"
            ];
            $scope.$watch('selected', function (n, o) {
                if (n !== undefined && n.constructor === Array && n.length > 0) {
                    var l = 0;
                    n.forEach(function (value, index) {
                        if (value !== false) {
                            l++
                        }
                    });
                    if (l > 0) $scope.ee = false;
                    else $scope.ee = true;
                    if (n.length === l) {
                        $scope.selectAll = true
                    }
                    else $scope.selectAll = false
                }
                else $scope.selectAll = false
            }, true);
            $scope.changeAll = function () {

                if ($scope.selectAll) {
                    $scope.ansarDetail.forEach(function (value, index) {
                        $scope.selected[index] = value.ansar_id;
                    })
                }
                else {
                    $scope.selected = Array.apply(null, Array($scope.ansarDetail.length)).map(Boolean.prototype.valueOf, false);
                }
            };
            $scope.pppp = function (value) {
                return value !== false;
            };
            $scope.loadAnsarDetail = function (id) {
                $("#embodied-modal").modal('hide')
                $scope.loadingAnsar = true;
                $scope.resetData();
                $http({
                    method: 'get',
                    url: '{{URL::route('check-ansar')}}',
                    params: {ansar_id: $scope.q, unit: $scope.params.unit}
                }).then(function (response) {
                    $scope.queue.shift();
                    if ($scope.queue.length > 1) $scope.loadAnsarDetail();
                    $scope.ansarDetail = response.data.apd ? response.data.apd : []
                    $scope.auid = $scope.ansarDetail.length > 0 ? angular.copy($scope.ansarDetail[0]) : $scope.auid
                    $scope.selected = Array.apply(null, Array($scope.ansarDetail.length)).map(Boolean.prototype.valueOf, false)
                    $scope.loadingAnsar = false;
                    // console.log($scope.ansarDetail)
                    $scope.totalLength--;
                    $scope.loadingAnsar = false;
                }, function () {
                    $scope.loadingAnsar = false;
                })
            };
            $scope.loadAnsarDetailForMultipleKPI = function (id) {
                $scope.loadingAnsar = true;
                $http({
                    method: 'get',
                    url: '{{URL::route('check-ansar')}}',
                    params: {ansar_id: id, unit: $scope.params.unit}
                }).then(function (response) {
                    $scope.multipleAnsar = response.data.apd ? response.data.apd : [];
                    $scope.loadingAnsar = false;
                }, function () {
                    $scope.loadingAnsar = false;
                })
            };
            $scope.addToCart = function () {
                $("#cart-modal").modal('hide')
                var exists = 0;
                $scope.listedAnsar.forEach(function (v, i) {
                    if (v.ansar_id == $scope.multipleAnsar[0].ansar_id) {
                        exists = 1;
                        return;
                    }
                })
                if (exists == 1) {
                    notificationService.notify("error", "This Ansar already added to list")
                    return;
                }
                $scope.listedAnsar.push({
                    ansar_id: $scope.multipleAnsar[0].ansar_id,
                    ansar_name: $scope.multipleAnsar[0].ansar_name_bng,
                    rank: $scope.multipleAnsar[0].name_bng,
                    join_date: $scope.joining_datee,
                    kpi_name: $scope.kpiName
                })
                $scope.eData.push({
                    ansar_id: $scope.multipleAnsar[0].ansar_id,
                    joining_date: $scope.joining_datee,
                    reporting_date: $scope.reporting_datee,
                    kpi_id: $scope.paramm.kpi
                })
                $scope.reset = {unit: true, thana: true, kpi: true};

                $scope.joining_datee = ''
                $scope.reporting_datee = ''
                $scope.multipleAnsar = undefined;
                $scope.ansar = ''
                $timeout(function () {
                    $scope.$apply();
                })
            }
            $scope.removeFromCart = function (index) {
                $scope.listedAnsar.splice(index, 1);
                $scope.eData.splice(index, 1);
            }
            $scope.submitEmbodiment = function () {
                console.log($scope.eData);
                $scope.printLetter = [{}, {}];
                $scope.loading = true;
                $http({
                    method: 'post',
                    data: angular.toJson({
                        data: $scope.eData,
                        memorandum_id: $scope.memorandumIdm,
                        mem_date: $scope.memDatee,
                        unit_id: $rootScope.user.district_id ? $rootScope.user.district_id : $scope.params.unit
                    }),
                    url: '{{URL::route('new-embodiment-entry-multiple')}}'
                }).then(function (response) {
                    console.log(response.data)
                    if (response.data.status) {
                        notificationService.notify('success', response.data.message);
                        $("#embodied-modal-mul").modal('hide');
                        $scope.mmmID = angular.copy($scope.memorandumIdm);
                        $scope.eData = [];
                        $scope.listedAnsar = [];
                        $scope.multipleAnsar = [];
                        $scope.ansar = '';
                        $scope.memorandumIdm = ''
                        $scope.memDatee = ''
                        $scope.printLetter[1] = response.data.letterData;
                        $timeout(function () {
                            $scope.$apply();
                        })
                        $scope.loading = false;
                        $scope.loadAnsarDetail();
                    }
                    else {
                        notificationService.notify('error', response.data.message);
                        $scope.loading = false;
                        $scope.printLetter = [{}, {}];
                    }
                }, function (response) {
                    notificationService.notify('error', response.statusText);
                    $scope.loading = false;
                    $scope.printLetter = [{}, {}];
                })

            }
            $scope.resetData = function () {
                $scope.eData = [];
                $scope.listedAnsar = [];
                $scope.multipleAnsar = [];
                $scope.ansar = '';
                $scope.memorandumIdm = ''
                $scope.memDatee = ''
                $scope.loading = false;
                $scope.printLetter = [{}, {}];
            }
            $scope.ppppp = function () {
                $scope.reset = {};
            }
            $scope.$watch('responseData', function (newVal, oldVal) {
                $scope.selected = [];
                if (newVal !== undefined && newVal.constructor === Object) {
                    $scope.printLetter[0] = newVal.printData;
                }
            }, true)
            $scope.setAnsarId = function (ansar) {
                $scope.clickedAsnar = ansar.ansar_id;
            };
            $scope.saveBankInfo = function () {
                $scope.bankDataErrorMessage = "";
                var isSubmit = false;
                var formData = {
                    bank_name: $scope.bank_name,
                    prefer_choice: $scope.prefer_choice,
                    mobile_bank_account_no: $scope.mobile_bank_account_no,
                    mobile_bank_type: $scope.mobile_bank_type,
                    account_no: $scope.account_no,
                    branch_name: $scope.branch_name,
                    ansar_id: $scope.clickedAsnar
                };
                if ($scope.prefer_choice === 'general') {
                    if ($scope.bank_name && $scope.branch_name && $scope.account_no) {
                        isSubmit = true;
                    } else {
                        $scope.bankDataErrorMessage = "You choose general banking. All fields related with general banking are required."
                    }
                }
                else if ($scope.prefer_choice === 'mobile') {
                    if ($scope.mobile_bank_type && $scope.mobile_bank_account_no) {
                        isSubmit = true;
                    } else {
                        $scope.bankDataErrorMessage = "You choose mobile banking. All fields related with mobile banking are required."
                    }
                } else {
                    $scope.bankDataErrorMessage = "Fill all required fields."
                }
                if (isSubmit) {
                    $http({
                        method: 'post',
                        url: '{{URL::route('save-bank-info')}}',
                        params: formData
                    }).then(function (response) {
                        angular.element(document.querySelector('#a-' + $scope.clickedAsnar)).css('display', 'inline-block');
                        angular.element(document.querySelector('#checkbox-' + $scope.clickedAsnar)).css('display', 'inline-block');
                        angular.element(document.querySelector('#button-' + $scope.clickedAsnar)).css('display', 'none');
                        angular.element(document.querySelector('#button1-' + $scope.clickedAsnar)).css('display', 'none');
                        $("#bank-account-modal").modal("toggle");
                        $scope.bank_name = "";
                        $scope.prefer_choice = "";
                        $scope.mobile_bank_account_no = "";
                        $scope.mobile_bank_type = "";
                        $scope.account_no = "";
                        $scope.branch_name = "";
                        $scope.clickedAsnar = "";
                    }, function (error) {
                        console.log(error);
                    })
                }
            }
        })
    </script>
    <div ng-controller="NewEmbodimentController">
        <section class="content" style="position: relative;">
            <div class="box box-solid">
                <div class="overlay" ng-if="loadingAnsar">
                    <span class="fa">
                        <i class="fa fa-refresh fa-spin"></i> <b>Loading...</b>
                    </span>
                </div>
                <div class="box-body">
                    <filter-template
                            show-item="['range','unit']"
                            type="single"
                            data="params"
                            start-load="range"
                            unit-load="resetData()"
                            unit-change="loadAnsarDetail()"
                            on-load="loadAnsarDetail()"
                            field-width="{range:'col-sm-4',unit:'col-sm-4'}"
                    >
                    </filter-template>
                    <div class="nav-tabs-custom">
                        <ul class="nav nav-tabs">
                            <li class="active">
                                <a href="#single-kpi" data-toggle="tab">Single KPI</a>
                            </li>
                            <li>
                                <a href="#multiple-kpi" data-toggle="tab">Multiple KPI</a>
                            </li>
                        </ul>
                        <div class="tab-content">
                            <div class="active tab-pane" id="single-kpi">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-stripped">
                                        <caption>
                                            <database-search q="q" on-change="loadAnsarDetail()"
                                                             queue="queue"></database-search>
                                        </caption>
                                        <tr>
                                            <th>#</th>
                                            <th>Ansar ID</th>
                                            <th>Name</th>
                                            <th>Rank</th>
                                            <th>Home District</th>
                                            <th>প্যানেলভুক্তির তারিখ</th>
                                            <th>প্যানেল আইডি নং</th>
                                            <th>বর্তমান অবস্থা</th>
                                            <th>অফারের তারিখ</th>
                                            <th style="text-align: center">
                                                <input type="checkbox" ng-model="selectAll" ng-true-value="true"
                                                       ng-false-value="false" ng-change="changeAll()"
                                                       ng-disabled="ansarDetail.length<=0">
                                            </th>
                                        </tr>
                                        <tr class="table-middle-data" ng-repeat="ansar in ansarDetail">
                                            <td>[[$index+1]]</td>
                                            <td>[[ansar.ansar_id]]</td>
                                            <td>[[ansar.ansar_name_bng]]</td>
                                            <td>[[ansar.name_bng]]</td>
                                            <td>[[ansar.home_district]]</td>
                                            <td>[[ansar.panel_date|dateformat:"DD-MMM-YYYY"]]</td>
                                            <td style="word-break: break-all;">[[ansar.memorandum_id]]</td>
                                            <td>Offered</td>
                                            <td>[[ansar.offerDate|dateformat:"DD-MMM-YYYY"]]</td>
                                            <td style="text-align: center;vertical-align: middle;">
                                                <input type="checkbox" ng-model="selected[$index]"
                                                       ng-style="{'display': (ansar.prefer_choice == 'general' || ansar.prefer_choice == 'mobile')? 'inline-block':'none'}"
                                                       id="checkbox-[[ansar.ansar_id]]" ng-false-value="false"
                                                       ng-true-value="[[ansar.ansar_id]]">
                                                <button id="button-[[ansar.ansar_id]]" ng-click="setAnsarId(ansar)"
                                                        ng-style="{'display': (ansar.prefer_choice != 'general' && ansar.prefer_choice != 'mobile')? 'inline-block':'none'}"
                                                        type="button" class="btn btn-primary btn-xs" data-toggle="modal"
                                                        data-target=".bd-example-modal-lg">
                                                    <i class="fa fa-plus" aria-hidden="true"></i>&nbsp;Bank Account
                                                </button>
                                            </td>
                                        </tr>
                                        <tr ng-if="ansarDetail==undefined||ansarDetail.length<=0">
                                            <td class="warning" colspan="10">No Ansar available</td>
                                        </tr>
                                    </table>
                                </div>

                                {!! Form::open(['route'=>'print_letter','target'=>'_blank','class'=>'pull-left']) !!}
                                <input type="hidden" ng-repeat="(k,v) in printLetter[0]" name="[[k]]" value="[[v]]">
                                <button ng-show="printLetter[0].status" class="btn btn-primary"><i
                                            class="fa fa-print"></i>&nbsp;Print Embodied Letter
                                </button>
                                {!! Form::close() !!}
                                <a href="#" class="btn btn-primary pull-right" ng-disabled="ee"
                                   data-target="#embodied-modal" data-toggle="modal">
                                    Embodied
                                </a>

                                <div class="clearfix"></div>
                            </div>
                            <div class="tab-pane" id="multiple-kpi">
                                <div class="input-group" style="margin-bottom: 10px">
                                    <input ng-disabled="!params.unit" type="text" placeholder="Search by Ansar ID"
                                           class="form-control" ng-model="ansar">
                                    <span class="input-group-btn">
                                        <button ng-disabled="!params.unit" class="btn btn-default"
                                                ng-click="loadAnsarDetailForMultipleKPI(ansar)">
                                            <i class="fa fa-search"></i>
                                        </button>
                                    </span>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-stripped">
                                        <tr>
                                            <th>#</th>
                                            <th>Name</th>
                                            <th>Rank</th>
                                            <th>Home District</th>
                                            <th>প্যানেলভুক্তির তারিখ</th>
                                            <th>প্যানেল আইডি নং</th>
                                            <th>বর্তমান অবস্থা</th>
                                            <th>অফারের তারিখ</th>
                                            <th>Action</th>
                                        </tr>
                                        <tr ng-repeat="ansar in multipleAnsar">
                                            <td>[[$index+1]]</td>
                                            <td>[[ansar.ansar_name_bng]]</td>
                                            <td>[[ansar.name_bng]]</td>
                                            <td>[[ansar.home_district]]</td>
                                            <td>[[ansar.panel_date|dateformat:"DD-MMM-YYYY"]]</td>
                                            <td style="word-break: break-all;">[[ansar.memorandum_id]]</td>
                                            <td>Offered</td>
                                            <td>[[ansar.offerDate|dateformat:"DD-MMM-YYYY"]]</td>
                                            <td>
                                                <a href="#" class="btn btn-primary btn-xs" ng-click="ppppp()"
                                                   ng-style="{'display': (ansar.prefer_choice == 'general' || ansar.prefer_choice == 'mobile')? 'inline-block':'none'}"
                                                   id="a-[[ansar.ansar_id]]"
                                                   data-target="#cart-modal" data-toggle="modal">
                                                    <i class="fa fa-plus"></i>&nbsp; Add to list
                                                </a>
                                                <button id="button1-[[ansar.ansar_id]]" ng-click="setAnsarId(ansar)"
                                                        ng-style="{'display': (ansar.prefer_choice != 'general' && ansar.prefer_choice != 'mobile')? 'inline-block':'none'}"
                                                        type="button" class="btn btn-primary btn-xs" data-toggle="modal"
                                                        data-target=".bd-example-modal-lg">
                                                    <i class="fa fa-plus" aria-hidden="true"></i>&nbsp;Bank Account
                                                </button>
                                            </td>
                                        </tr>
                                        <tr ng-if="!multipleAnsar||multipleAnsar.length<=0">
                                            <td colspan="9" class="warning">No Ansar available</td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-stripped">
                                        <caption class="text text-bold">Selected Ansar List</caption>
                                        <tr>
                                            <th>#</th>
                                            <th>Ansar ID</th>
                                            <th>Name</th>
                                            <th>Rank</th>
                                            <th>Joining Date</th>
                                            <th>KPI Name</th>
                                            <th>Action</th>
                                        </tr>
                                        <tr ng-repeat="ansar in listedAnsar">
                                            <td>[[$index+1]]</td>
                                            <td>[[ansar.ansar_id]]</td>
                                            <td>[[ansar.ansar_name]]</td>
                                            <td>[[ansar.rank]]</td>
                                            <td>[[ansar.join_date]]</td>
                                            <td>[[ansar.kpi_name]]</td>
                                            <td>
                                                <a href="#" class="btn btn-danger btn-xs"
                                                   ng-click="removeFromCart($index)">
                                                    <i class="fa fa-remove"></i>&nbsp;
                                                </a>
                                            </td>
                                        </tr>
                                        <tr ng-if="listedAnsar.length<=0">
                                            <td colspan="7" class="warning">
                                                No Ansar selected
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                                <div>
                                    {!! Form::open(['route'=>'print_letter','target'=>'_blank','class'=>'pull-left']) !!}
                                    <input type="hidden" ng-repeat="(k,v) in printLetter[1]" name="[[k]]" value="[[v]]">
                                    <button ng-show="printLetter[1].status" class="btn btn-primary"><i
                                                class="fa fa-print"></i>&nbsp;Print Embodied Letter
                                    </button>
                                    {!! Form::close() !!}
                                    <button class="btn btn-primary pull-right" ng-disabled="listedAnsar.length<=0"
                                            data-target="#embodied-modal-mul" data-toggle="modal">Embodied
                                    </button>
                                    <div class="clearfix"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="embodied-modal" class="modal fade" role="dialog">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                {!! Form::open(array('route' => 'new-embodiment-entry', 'name' => 'newEmbodimentForm', 'novalidate','form-submit','errors','response-data'=>'responseData','loading','status','on-reset'=>'loadAnsarDetail()')) !!}
                                <div class="modal-header">
                                    <h4 class="modal-title">Embodiment Form</h4>
                                </div>
                                <input type="hidden" name="unit_id"
                                       value="[[user.district_id?user.district_id:param.unit]]">
                                <div class="modal-body">
                                    <input type="hidden" name="ansar_ids[]" ng-repeat="s in selected|filter:pppp"
                                           value="[[s]]">
                                    <div class="form-group required">
                                        <label class="control-label">Memorandum no. & Date</label>

                                        <div class="row">
                                            <div class="col-md-7" style="padding-right: 0">
                                                <input ng-model="memorandumId"
                                                       type="text" class="form-control" name="memorandum_id"
                                                       placeholder="Enter Memorandum no." required>
                                            </div>
                                            <div class="col-md-5">
                                                <input date-picker ng-model="memDate"
                                                       type="text" class="form-control" name="mem_date"
                                                       placeholder="Memorandum Date" required>
                                            </div>

                                        </div>
                                        <p class="text-danger" ng-if="errors.memorandum_id!=undefined">
                                            [[errors.memorandum_id[0] ]]</p>
                                    </div>
                                    <div class="form-group required">
                                        <label for="reporting_date" class="control-label">Reporting Date</label>
                                        {!! Form::text('reporting_date', null, $attributes = array('class' => 'form-control', 'id' => 'reporting_date', 'ng-model' => 'reporting_date','date-picker'=>'', 'required')) !!}
                                        <p class="text-danger" ng-if="errors.reporting_date!=undefined">
                                            [[errors.reporting_date[0] ]]</p>
                                    </div>
                                    <div class="form-group required">
                                        <label for="joining_date" class="control-label">Embodiment Date</label>
                                        {!! Form::text('joining_date', null, $attributes = array('class' => 'form-control', 'id' => 'joining_date','date-picker'=>'', 'ng-model' => 'joining_date','required')) !!}
                                        <p class="text-danger" ng-if="errors.joining_date!=undefined">
                                            [[errors.joining_date[0] ]]</p>
                                    </div>
                                    <!---->
                                    <!---->
                                    <filter-template
                                            show-item="['unit','thana','kpi']"
                                            type="single"
                                            data="param"
                                            start-load="unit"
                                            layout-vertical="1"
                                            field-name="{unit:'division_name_eng',thana:'thana_name_eng',kpi:'kpi_id'}"
                                            error-key="{unit:'division_name_eng',thana:'thana_name_eng',kpi:'kpi_id'}"
                                            error-message="eMessage"
                                    >

                                    </filter-template>
                                </div>
                                <div class="modal-footer">
                                    <button type="submit" class="btn btn-primary pull-right" ng-disabled="loading">
                                        <i class="fa fa-spinner fa-pulse" ng-show="loading"></i>Embodied
                                    </button>
                                </div>
                                {!! Form::close() !!}
                            </div>
                        </div>
                    </div>
                    <div id="embodied-modal-mul" class="modal fade" role="dialog">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h4 class="modal-title">Embodiment Form</h4>
                                </div>
                                <div class="modal-body">
                                    <div class="form-group required">
                                        <label class="control-label">Memorandum no. & Date</label>

                                        <div class="row">
                                            <div class="col-md-7" style="padding-right: 0">
                                                <input ng-model="memorandumIdm"
                                                       type="text" class="form-control"
                                                       placeholder="Enter Memorandum no." required>
                                            </div>
                                            <div class="col-md-5">
                                                <input date-picker ng-model="memDatee"
                                                       type="text" class="form-control" name="mem_date"
                                                       placeholder="Memorandum Date" required>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="submit" ng-click="submitEmbodiment()"
                                            class="btn btn-primary pull-right" ng-disabled="loading">
                                        <i class="fa fa-spinner fa-pulse" ng-show="loading"></i>Confirm Embodiment
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="cart-modal" class="modal fade" role="dialog">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button class="close pull-right" data-dismiss="modal" aria-hidden="true">&times;
                                    </button>
                                    <h4 class="modal-title">Embodiment Detail</h4>
                                </div>
                                <div class="modal-body">
                                    <div class="form-group required">
                                        <label for="reporting_date" class="control-label">Reporting Date</label>
                                        {!! Form::text('reporting_date', null, $attributes = array('class' => 'form-control', 'id' => 'reporting_datee', 'ng-model' => 'reporting_datee','date-picker'=>'', 'required')) !!}
                                        <p class="text-danger" ng-if="errors.reporting_date!=undefined">
                                            [[errors.reporting_date[0] ]]</p>
                                    </div>
                                    <div class="form-group required">
                                        <label for="joining_date" class="control-label">Embodiment Date</label>
                                        {!! Form::text('joining_date', null, $attributes = array('class' => 'form-control', 'id' => 'joining_datee','date-picker'=>'', 'ng-model' => 'joining_datee','required')) !!}
                                        <p class="text-danger" ng-if="errors.joining_date!=undefined">
                                            [[errors.joining_date[0] ]]</p>
                                    </div>
                                    <!---->
                                    <!---->
                                    <filter-template
                                            show-item="['unit','thana','kpi']"
                                            type="single"
                                            data="paramm"
                                            start-load="unit"
                                            reset="reset"
                                            layout-vertical="1"
                                            get-kpi-name="kpiName"
                                            field-name="{unit:'division_name_eng',thana:'thana_name_eng',kpi:'kpi_id'}"
                                            error-key="{unit:'division_name_eng',thana:'thana_name_eng',kpi:'kpi_id'}"
                                            error-message="{division_name_eng:errors.division_name_eng[0],thana_name_eng:errors.thana_name_eng[0],kpi_id:errors.kpi_id[0]}"
                                    >

                                    </filter-template>
                                </div>
                                <div class="modal-footer">
                                    <button type="submit" class="btn btn-primary pull-right" ng-click="addToCart()"
                                            ng-disabled="loading">
                                        <i class="fa fa-check"></i>Confirm
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="bank-account-modal" class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog"
                         aria-labelledby="myLargeModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button class="close pull-right" data-dismiss="modal" aria-hidden="true">&times;
                                    </button>
                                    <h4 class="modal-title">ব্যাংক অ্যাকাউন্ট তথ্য</h4>
                                </div>
                                <div class="modal-body">
                                    <div class="box-body">
                                        <div class="alert alert-danger"
                                             ng-if="bankDataErrorMessage!='' && bankDataErrorMessage!=null">
                                            [[bankDataErrorMessage]]
                                        </div>
                                        <h5 class="text-center">সাধারণ ব্যাংকিং তথ্য</h5>
                                        <div class="form-horizontal col-md-12 ">
                                            <label class="control-label col-sm-3" for="email">ব্যাংকের
                                                নাম</label>
                                            <div class="col-sm-9 ">
                                                <select class="form-control" id="sell" ng-model="bank_name">
                                                    <option value="">--ব্যাংক নির্বাচন করুন--</option>
                                                    <option ng-repeat="x in bankList" value="[[x]]">[[x]]
                                                    </option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-horizontal col-md-12 ">
                                            <label class="control-label col-sm-3" for="email">ব্রাঞ্চের নাম:</label>
                                            <div class="col-sm-9">
                                                <input type="text" class="form-control" id="branch_name"
                                                       ng-model="branch_name" placeholder="ব্রাঞ্চের নাম"/>
                                            </div>
                                        </div>
                                        <div class="form-horizontal col-md-12 ">
                                            <label class="control-label col-sm-3" for="email">অ্যাকাউন্ট
                                                নম্বর:</label>
                                            <div class="col-sm-9">
                                                <input type="text" class="form-control" id="branch_name"
                                                       ng-model="account_no" placeholder="ব্যাংক অ্যাকাউন্ট নম্বর"/>
                                            </div>
                                        </div>
                                        <h4 style="display: inline-block;text-align: center;width: 100%;">মোবাইল
                                            ব্যাংকিং তথ্য</h4>
                                        <div class="form-horizontal col-md-12 ">
                                            <label class="control-label col-sm-3" for="email">মোবাইল ব্যাংকিং
                                                ধরন</label>
                                            <div class="col-sm-9 ">
                                                <select class="form-control" id="sell" ng-model="mobile_bank_type">
                                                    <option value="">--নির্বাচন করুন--</option>
                                                    <option ng-repeat="x in mobileBankType" value="[[x]]">[[x]]
                                                    </option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-horizontal col-md-12 ">
                                            <label class="control-label col-sm-3" for="email">অ্যাকাউন্ট
                                                নম্বর:</label>
                                            <div class="col-sm-9">
                                                <input type="text" class="form-control" id="branch_name"
                                                       ng-model="mobile_bank_account_no" value=""
                                                       placeholder="ব্যাংক অ্যাকাউন্ট নম্বর"/>
                                            </div>
                                        </div>
                                        <div class="form-horizontal col-md-12 ">
                                            <label class="control-label col-sm-3" for="email">কোন অ্যাকাউন্ট এ
                                                টাকা পেতে চান?</label>
                                            <div class="col-sm-9 ">
                                                <select class="form-control" id="sell" ng-model="prefer_choice">
                                                    <option value="">--নির্বাচন করুন--</option>
                                                    <option value="general">সাধারন ব্যাংকিং</option>
                                                    <option value="mobile">মোবাইল ব্যাংকিং</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                    <button type="button" class="btn btn-primary" ng-click="saveBankInfo()">Save
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@stop