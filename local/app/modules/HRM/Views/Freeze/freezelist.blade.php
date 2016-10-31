@extends('template.master')
@section('title','Freezed Ansar List')
@section('breadcrumb')
    {!! Breadcrumbs::render('freezelist') !!}
@endsection
@section('content')
    <style>
        .temp-label {
            float: left;
            padding: 5px 10px;
            box-shadow: 0px 1px 4px 0px #cccccc;
            border-radius: 5px;
            margin: 5px 5px 5px;
        }

        .temp-label:last-child {
            margin-right: 0;
        }
    </style>
    <script>

        GlobalApp.controller('freezeController', function ($scope, $http, notificationService) {
//        $scope.filter_name = "0";
            $scope.allLoading = false;
            $scope.allFreezeAnsar = [];
            $scope.loadingThana = false;
            $scope.loadingKpi = false;
            $scope.checked = [];
            $scope.checkedAll = false;
            $scope.units = [];
            $scope.thanas = [];
            $scope.action = ''
            $scope.child = {
                selectedUnit: ""
            }
            $scope.verifyTransfer = false
            $scope.verify = false;
            $scope.kpis = [];
            $scope.selectedKpi = "";
            $scope.selectedThana = ""
            $scope.isAdmin = false;
            $scope.verifying = false;
            $scope.isDc = false;
            $scope.isRC = false;
            $scope.actions = [
                {
                    value: 'continue',
                    text: 'Continue Service'
                },
                {
                    value: 'reembodied',
                    text: 'Transfer'
                },
                {
                    value: 'disembodied',
                    text: 'Disembodied'
                },
                {
                    value: 'black',
                    text: 'Black'
                },

            ]
            var userType = parseInt('{{auth()->user()->type}}')
            $scope.loadKpi = function () {
                $scope.loadingKpi = true;
                $http({
                    url: '{{URL::route('kpi_name')}}',
                    params: {id: $scope.selectedThana},
                    method: 'get'
                }).then(function (response) {
                    $scope.kpis = response.data
                    $scope.loadingKpi = false;
                    $scope.selectedKpi = "";
                }, function (response) {
                    $scope.loadingKpi = false;
                })
            }

            $scope.loadUnit = function () {
                $http({
                    url: '{{URL::to('HRM/DistrictName')}}',
                    method: 'get'
                }).then(function (response) {
                    $scope.units = response.data;
                }, function () {

                })
            }
            $scope.loadThana = function () {
                $scope.loadingThana = true;
                $http({
                    url: "{{URL::to('HRM/ThanaName')}}",
                    method: 'get',
                    params: {id: $scope.child.selectedUnit}
                }).then(function (response) {
                    $scope.thanas = response.data;
                    $scope.loadingThana = false;
                    $scope.selectedThana = ""
                    $scope.selectedKpi = "";
                }, function (response) {
                    $scope.loadingThana = false;
                })
            }
            switch (userType) {
                case 22:
                    $scope.isDc = true;
                    $scope.child.selectedUnit = parseInt('{{auth()->user()->district_id}}')
                    $scope.loadThana();
                    break;
                case 66:
                    $scope.isRC = true;

                    $scope.loadUnit();
                    break;
                default :
                    $scope.isAdmin = true;
                    $scope.loadUnit();
                    break;
            }
            $scope.getFreezeList = function () {
                $scope.allLoading = true;
                $http({
                    url: "{{URL::route('getfreezelist')}}",
                    method: 'get',
                    params: {filter: $scope.filter_name}
                }).then(function (response) {
//            alert(JSON.stringify(response.data));
                    $scope.allFreezeAnsar = response.data;
                    $scope.checked = Array.apply(null, Array($scope.allFreezeAnsar.length)).map(Boolean.prototype.valueOf, false);
                    console.log($scope.checked)
                    $scope.allLoading = false;
                }, function (response) {
                    $scope.allLoading = false;
                })
            }
//        $scope.getFreezeList();

            $scope.reEmbodied = function (ansarids) {
                console.log(ansarids)
                $scope.submitting = true;
                $http({
                    url: "{{URL::to('HRM/freezeRembodied')}}",
                    method: 'post',
                    data: angular.toJson({ansarId: ansarids})
                }).then(function (response) {
                    console.log(response.data);
                    $scope.submitting = false;
                    if (response.data[0].status) {
                        notificationService.notify('success', response.data[0].message);
                        $scope.getFreezeList();
                    }
                    else {
                        notificationService.notify('error', response.data[0].message);
                    }
                }, function (response) {
                    notificationService.notify('error', "An unexpected error occur. Error code :" + response.status);
                })
            }
            $scope.reEmbodiedChecked = function (ansarids, indexes) {
                console.log(ansarids)
                $scope.submitting = true;
                $http({
                    url: "{{URL::to('HRM/freezeRembodied')}}",
                    method: 'post',
                    data: angular.toJson({ansarId: ansarids})
                }).then(function (response) {
                    console.log(response.data);
                    $scope.submitting = false;
                    var t = true
                    response.data.forEach(function (value, index, array) {

                        if (value.status) {
                            $("#continue-modal").modal('hide')
                            notificationService.notify('success', value.message);
                            $scope.allFreezeAnsar.splice(indexes[index], 1);
                            $scope.checked.splice($scope.checked.indexOf(indexes[index]), 1);
                        }
                        else {
                            t = false;
                            notificationService.notify('error', value.message);
                        }

                    })
                    if (t) $("#continue-modal").modal('hide');

                }, function (response) {
                    notificationService.notify('error', "An unexpected error occur. Error code :" + response.status);
                })
            }
            $scope.transferAnsar = function (ansarId) {
                $scope.submitting = true;
                $scope.transferData['ansarIds'] = ansarId;
                console.log($scope.transferData);
                $http({
                    url: '{{URL::route('transfer_freezed_ansar')}}',
                    method: 'post',
                    data: angular.toJson($scope.transferData)
                }).then(function (response) {
                    $scope.submitting = false;
                    if (response.data.status) {
                        notificationService.notify('success', response.data.message);
                        $("#re-embodied-model,#re-embodied-model-mul").modal('hide')
                        $scope.transferData = {};
                        $scope.getFreezeList();
                    }
                    else {
                        notificationService.notify('error', response.data.message)
                    }

                }, function (response) {
                    $scope.submitting = false;
                    notificationService.notify('error', "An unexpected error occur. Error code :" + response.status);
                })
            }
            $scope.continueChecked = function () {
                var ansarIds = [];
                var indexes = [];
                $scope.checked.forEach(function (value, index, array) {
                    if (value !== false) {
                        ansarIds.push($scope.allFreezeAnsar[value].ansar_id)
                        indexes.push(value)
                    }
                })
                $scope.reEmbodiedChecked(ansarIds, indexes)
            }
            $scope.transChecked = function () {
                var ansarIds = [];
                $scope.checked.forEach(function (value, index, array) {
                    if (value !== false) {
                        ansarIds.push($scope.allFreezeAnsar[value].ansar_id)
                    }
                })
                $scope.transferAnsar(ansarIds)
            }
            $scope.blackChecked = function () {
                var ansarIds = [];
                $scope.checked.forEach(function (value, index, array) {
                    if (value !== false) {
                        ansarIds.push($scope.allFreezeAnsar[value].ansar_id)
                    }
                })
                $scope.blackAnsar(ansarIds)
            }
            $scope.checkMemorandum = function (id, type) {
                $scope.verifying = true;
                $http({
                    url: "{{action('UserController@verifyMemorandumId')}}",
                    method: 'post',
                    data: {memorandum_id: id}
                }).then(function (response) {
                    if (type == 0) $scope.verify = response.data.status;
                    else $scope.verifyTransfer = response.data.status;

                    $scope.verifying = false;
                })
            }
            $scope.disEmbodied = function (ansarid) {
                if (ansarid) {
                    $scope.submitting = true;
                    $http({
                        url: "{{URL::to('HRM/freezeDisEmbodied')}}",
                        method: 'post',
                        data: angular.toJson({
                            memorandum: $scope.memorandum,
                            rest_date: $scope.rest_date,
                            disembodiment_reason_id: $scope.disembodiment_reason_id,
                            comment: $scope.comment,
                            ansarId: [$scope.getSingleRow.ansar_id]
                        })
                    }).then(function (response) {
//                    alert(JSON.stringify(response.data));
                        $scope.submitting = false;
                        if (response.data.status) {
                            notificationService.notify('success', response.data.message);
                            $("#myModal").modal('hide')
                        }
                        else {
                            notificationService.notify('error', response.data.message)
                        }
                        $scope.allFreezeAnsar.splice($scope.allFreezeAnsar.indexOf($scope.getSingleRow), 1)
                    }, function (response) {
                        $scope.submitting = false;
                        notificationService.notify('error', "An unexpected error occur. Error code :" + response.status);
                    })
                }
            }
            $scope.disEmbodiedChecked = function () {
                var ansarIds = [];
                $scope.checked.forEach(function (value, index, array) {
                    if (value !== false) {
                        ansarIds.push($scope.allFreezeAnsar[value].ansar_id)
                    }
                })
                $scope.formData['ansarId'] = ansarIds
//                console.log($scope.formData);return;
                $scope.submitting = true;
                $http({
                    url: "{{URL::to('HRM/freezeDisEmbodied')}}",
                    method: 'post',
                    data: angular.toJson($scope.formData)
                }).then(function (response) {
                    console.log(response.data)
//                    alert(JSON.stringify(response.data));
                    $scope.submitting = false;
                    if (response.data.status) {
                        notificationService.notify('success', response.data.message);
                        $("#dis-embodied-model-multiple").modal('hide')
                        $scope.getFreezeList();
                    }
                    else {
                        notificationService.notify('error', response.data.message)
                    }
                    //$scope.allFreezeAnsar.splice($scope.allFreezeAnsar.indexOf($scope.getSingleRow), 1)
                }, function (response) {
                    $scope.submitting = false;
                    notificationService.notify('error', "An unexpected error occur. Error code :" + response.status);
                })

            }
            $scope.blackAnsar = function (ansarids) {
                $scope.blackData['ansarid'] = ansarids;
                $scope.submitting = true;
                console.log($scope.blackData);
//                return;
                $http({
                    url: "{{URL::to('HRM/freezeblack')}}",
                    method: 'post',
                    data: angular.toJson($scope.blackData)
                }).then(function (response) {
                    $scope.submitting = false;
                    if (response.data.status) {
                        notificationService.notify('success', response.data.message);
                        $("#black-modal,#black-modal-mul").modal('hide')
                        $scope.blackData = {};
                        $scope.getFreezeList();
                    }
                    else {
                        notificationService.notify('error', response.data.message)
                    }

                }, function (response) {
                    $scope.submitting = false;
                    notificationService.notify('error', "An unexpected error occur. Error code :" + response.status);
                })
            }
            $scope.doAction = function (i) {
                var ansar = $scope.allFreezeAnsar[i];
//                alert($scope.action[i])
                switch ($scope.action) {
                    case 'continue':
                        $("#continue-modal").modal('show')
                        break;
                    case 'reembodied':
                        $scope.getSingleRow = $scope.allFreezeAnsar[i];
                        $("#re-embodied-model-mul").modal('show')
                        break;
                    case 'disembodied':
                        $scope.getSingleRow = $scope.allFreezeAnsar[i];
                        $("#dis-embodied-model-multiple").modal('show')
                        break;
                    case 'black':
                        $("#black-modal-mul").modal('show')
                        break;

                }
            }
            $scope.reEmbodiedAll = function (data) {
                $http({
                    url: '{{URL::route('confirm_transfer')}}',
                    method: 'post',
                    data: angular.toJson(data)
                }).then(function (response) {

                }, function (response) {

                })
            }
            $scope.$watch('checked', function (n, o) {
                if (n.length <= 0) return;
                var r = n.every(function (i) {
                    return i !== false;
                })
                $scope.checkedAll = r;
            }, true)
            $scope.checkAll = function () {
                if (!$scope.checkedAll)$scope.checked = Array.apply(null, Array($scope.allFreezeAnsar.length)).map(Boolean.prototype.valueOf, false);
                else {
                    $scope.allFreezeAnsar.forEach(function (value, index) {
                        $scope.checked[index] = index;
                    })
                }
                console.log($scope.checked)
            }
            $scope.actualValue = function (value, index, array) {

                return value !== false;

            }
            $scope.convertDate = function (d) {
                return moment(d).format('DD-MMM-YYYY')
            }
            $scope.modal = function (index) {
                $scope.getSingleRow = $scope.allFreezeAnsar[index];
            }
        })
        $(document).ready(function (e) {
            $("body").on('click', '#action-freeze', function (e) {
                e.stopPropagation();
                var sb = '';
                if ($(this).siblings('.test-dropdown-below').length > 0) sb = $(this).siblings('.test-dropdown-below')
                else sb = $(this).siblings('.test-dropdown-above')
                var cl = $(this).offset().left;
                var pl = $('.box-body').offset().left;
                var l = cl - pl - (sb.outerWidth() / 2) + ($(this).outerWidth() / 2);
                var t = $(this).offset().top + $(this).outerHeight() + sb.outerHeight();
                if (t > $(window).innerHeight()) {
                    t = $(this).offset().top - sb.outerHeight() - $('.box-body').offset().top + $(this).outerHeight()
                    sb.removeClass().addClass('test-dropdown-above')
                    sb.css({
                        top: t + "px"
                    })
                }
                else {
                    sb.attr('style', '')
                    sb.removeClass().addClass('test-dropdown-below')
                }
                sb.css({
                    left: l + "px",
                    display: "block"
                })
                //alert(cl+"  "+pl)
            })
            $("body").on('click', '#reEmbodied', function (e) {
                $("#re-embodied-model").modal()
            })
            $("body").on('click', '.test-dropdown-below', function (e) {
                e.stopPropagation();
            })
            $("body").on('click', '.test-dropdown-above', function (e) {
                e.stopPropagation();
            })
            $(window).on('click', function (e) {
                //console.log({class_name:e.target.className})
                $('.test-dropdown-below,.test-dropdown-above').css('display', 'none');
                //if(e.target.id=="action-freeze"||e.target.className=="test-dropdown"||$(e.target).parents('.test-dropdown').length>0) return;

            })
            $(window).resize(function () {
                $('.test-dropdown-below,.test-dropdown-above').css('display', 'none');
            })
            $("#joining_date,#rest_date").datePicker(false);
        })
    </script>




    <div ng-controller="freezeController">
        {{--<div class="breadcrumbplace">--}}
        {{--{!! Breadcrumbs::render('freezelist') !!}--}}
        {{--</div>--}}
        <section class="content">
            <div>
                <div class="box box-solid">
                    <div class="overlay" ng-if="allLoading">
                    <span class="fa">
                        <i class="fa fa-refresh fa-spin"></i> <b>Loading...</b>
                    </span>
                    </div>
                    <div class="box-body">
                        <div class="box-body" id="change-body">
                            <div class="form-group">
                                <h5 class="pull-left" style="padding-right:5px; ;"><b>Filter by: </b></h5>
                                <select ng-model="filter_name" name="filter_name" class="form-control"
                                        style="width:75%;" ng-change="getFreezeList()">
                                    <option value="">--Select freeze reason--</option>
                                    <option value="0">All</option>
                                    <option value="1">Guard Withdraw</option>
                                    <option value="2">Guard Reduce</option>
                                    <option value="3">Disciplinary Actions</option>
                                </select>
                            </div>
                            <div class="loading-data"><i class="fa fa-4x fa-refresh fa-spin loading-icon"></i>
                            </div>
                            <div class="table-responsive">
                                <table class="table  table-bordered table-striped" id="ansar-table">

                                    <tr>
                                        <th class="text-center"><input type="checkbox" ng-model="checkedAll"
                                                                       ng-change="checkAll()"></th>
                                        <th class="text-center"> ক্রঃ নং</th>

                                        <th class="text-center">আইডি</th>
                                        <th class="text-center">পদবি</th>
                                        <th class="text-center">নাম</th>
                                        <th class="text-center">নিজ জেলা</th>
                                        <th class="text-center">অঙ্গীভূত তারিখ</th>
                                        <th class="text-center">ফ্রিজ করনের তারিখ</th>
                                        <th class="text-center">ফ্রিজকালীন ক্যাম্পের নাম</th>
                                        <th class="text-center">ফ্রিজকরনের কারণ</th>
                                        <th class="text-center" style="width:160px;">কার্যক্রম/Action</th>

                                    </tr>
                                    <tr ng-show="allFreezeAnsar.length>0" ng-repeat="freezeAnsar in allFreezeAnsar">
                                        <td>
                                            <input type="checkbox" ng-true-value="[[$index]]" ng-false-value="false"
                                                   ng-model="checked[$index]">
                                        </td>
                                        <td>[[$index+1]]</td>
                                        <td>
                                            <a href="{{ URL::to('/entryreport/') }}/[[freezeAnsar.ansar_id]]">[[freezeAnsar.ansar_id]]</a>
                                        </td>
                                        <td>[[freezeAnsar.name_bng]]</td>
                                        <td>[[freezeAnsar.ansar_name_bng]]</td>
                                        <td>[[freezeAnsar.unit_name_bng]]</td>
                                        <td>[[convertDate(freezeAnsar.reporting_date)]]</td>
                                        <td>[[convertDate(freezeAnsar.freez_date)]]</td>
                                        <td>[[freezeAnsar.kpi_name]]</td>
                                        <td>[[freezeAnsar.freez_reason]]</td>
                                        <td>
                                            <a id="action-freeze" class="btn btn-success btn-xs verification"
                                               title="Re-Embodied">
                                                <span class="fa fa-check"></span>
                                                <!--<i class="fa fa-spinner fa-pulse"></i>-->
                                            </a>

                                            <div class="test-dropdown-below">
                                                <ul>
                                                    <li>
                                                        <button class="btn btn-primary" confirm
                                                                callback="reEmbodied(ansarid)" event="click"
                                                                message="Are you sure want to Re-Embodied this ansar"
                                                                data="{ansarid:[freezeAnsar.ansar_id]}">
                                                            Continue Service
                                                        </button>
                                                    </li>
                                                    <li>
                                                        <button class="btn btn-primary" id="reEmbodied"
                                                                ng-click="modal($index)">Re-Embodied
                                                        </button>
                                                    </li>
                                                </ul>


                                            </div>
                                            <button class="btn btn-danger btn-xs verification" title="Disembodied"
                                                    data-toggle="modal" data-target="#myModal"
                                                    ng-click="modal($index)">
                                                <span class="fa fa-retweet"></span>
                                                <!--<i class="fa fa-spinner fa-pulse"></i>-->
                                            </button>
                                            <a class="btn btn-danger btn-xs verification" title="Add to Blacklist"
                                               data-toggle="modal" data-target="#black-modal" ng-click="modal($index)">
                                                <span class="fa fa-remove"></span>
                                                <!--<i class="fa fa-spinner fa-pulse"></i>-->
                                            </a>
                                        </td>
                                    </tr>
                                    <tr ng-show="allFreezeAnsar.length==0">
                                        <td class="warning" colspan="11">No information found</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="" class="control-label">
                                            With Selected
                                        </label>
                                        <select name="" id="" class="form-control" ng-model="action"
                                                ng-change="doAction()">
                                            <option value="">--Select Action--</option>
                                            <option ng-repeat="a in actions" value="[[a.value]]">[[a.text]]</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </section>
        <div id="myModal" class="modal fade" role="dialog">
            <div class="modal-dialog">

                <!-- Modal content-->
                <div class="box-body modal-content">
                    <form class="form" role="form" method="post" ng-submit="disEmbodied(getSingleRow.ansar_id)">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h4 class="modal-title">Ansar
                                Id:[[getSingleRow.ansar_id]],Name:[[getSingleRow.ansar_name_bng]]</h4>
                        </div>

                        <div class="modal-body row">


                            <div class="form-group col-md-offset-1 col-md-10">
                                <label class="control-label" for="memorandum_id">
                                    *স্বারক নংঃ <span style="font-weight: normal" ng-if="verifying"><i
                                                class="fa fa-pulse fa-spinner"></i> Verifying</span>
                                </label><span style="color:red" ng-if="verify"> This id has already been taken</span>
                                <input ng-blur="checkMemorandum(memorandum,0)" type="text" class="form-control"
                                       id="memorandum_id" ng-model="memorandum" name="memorandum_id">
                            </div>

                            <div class="form-group col-md-offset-1 col-md-10">
                                <label class="control-label" for="rest_date">
                                    *Disembodiment Date:
                                </label>
                                <input type="text" class="form-control" id="rest_date" id="memorandum_id"
                                       ng-model="rest_date"
                                       name="rest_date">
                            </div>

                            <div class="form-group col-md-offset-1 col-md-10">
                                <label class="control-label" for="disembodiment_reason_id">
                                    *Reason:
                                </label>
                                <select ng-model="disembodiment_reason_id" name="disembodiment_reason_id"
                                        class="form-control">
                                    <option value="">---অ-অঙ্গীভূত এর কারণ নির্বাচন করুন---</option>
                                    <option value=1>অঙ্গীভূত কাল শেষ  হলে</option>
                                    <option value=2>পদত্যাগ পত্র গৃহীত হলে</option>
                                    <option value=3>পেশাগত কাজে অযোগ্য</option>
                                    <option value=4>শারীরিক ও মানসিক ভাবে অক্ষম</option>
                                    <option value=5>শৃ্ঙ্খলা ভঙ্গের কারনে শাস্তিপ্রাপ্ত</option>
                                    <option value=6>সংশ্লিষ্ট সংস্থা বা প্রতিষ্ঠানে অঙ্গীভূত আনসার নিযুক্ত রাখার
                                        প্রয়োজনীয়তা শেষ হলে
                                    </option>
                                    <option value=7>মহাপরিচালক কর্তৃক নির্ধারিত অন্যান্য কারনে</option>
                                    <option value=8>অন্যান্য কারনে</option>
                                </select>
                            </div>

                            <div class="form-group col-md-offset-1 col-md-10">
                                <label class="control-label" for="comment">
                                    Comment:
                                </label>
                                <input type="text" class="form-control" id="comment" ng-model="comment" name="comment">
                            </div>
                            <div class="form-group col-md-offset-1 col-md-4">
                                <button type="submit" class="btn btn-default"
                                        ng-disabled="!disembodiment_reason_id || !memorandum || !rest_date || verify||submitting">
                                    <i class="fa fa-spinner fa-pulse" ng-if="submitting"></i>Submit
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

            </div>
        </div>
        <div id="black-modal" class="modal fade" role="dialog">
            <div class="modal-dialog">

                <!-- Modal content-->
                <div class="box-body modal-content">
                    <form class="form" role="form" method="post" confirm callback="blackAnsar(ansarids)" data="{ansarids:[getSingleRow.ansar_id]}" event="submit" message="Are you sure want to Black this ansar">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal"
                                    onclick="event.preventDefault()">&times;</button>
                            <h4 class="modal-title">Ansar
                                Id:[[getSingleRow.ansar_id]],Name:[[getSingleRow.ansar_name_bng]]</h4>
                        </div>
                        <div class="modal-body row">

                            <div class="form-group col-md-offset-1 col-md-10">
                                <label class="control-label" for="black_date">
                                    *Black Date:
                                </label>
                                <input type="text" class="form-control" id="black_date"
                                       ng-model="blackData.black_date"
                                       name="black_date">
                            </div>

                            <div class="form-group col-md-offset-1 col-md-10">
                                <label class="control-label" for="comment">
                                    Comment:
                                </label>
                                <input type="text" class="form-control" id="black_comment"
                                       ng-model="blackData.black_comment"
                                       name="black_comment">
                            </div>
                            <div class="form-group col-md-offset-1 col-md-4">
                                <button type="submit" class="btn btn-default" ng-disabled=" !blackData.black_date||submitting">
                                    <i class="fa fa-pulse fa-spinner" ng-if="submitting"></i>Submit
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

            </div>
        </div>
        <div id="black-modal-mul" class="modal fade" role="dialog">
            <div class="modal-dialog">

                <!-- Modal content-->
                <div class="box-body modal-content">
                    <form class="form" role="form" method="post" confirm callback="blackChecked()" event="submit" message="Are you sure want to Black those ansars">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" onclick="event.preventDefault()">&times;</button>
                            <h4 class="modal-title">Black List</h4>
                        </div>
                        <div class="modal-body">
                            <div class="row" style="margin-bottom: 10px">
                                <div class="col-sm-12">
                                    <div class="temp-label" ng-repeat="c in checked|filter:actualValue">
                                        <span style="vertical-align: middle">[[allFreezeAnsar[c].ansar_name_bng]]</span>
                                        <span>
                                            <button class="btn btn-box-tool"
                                                    ng-click="checked[checked.indexOf(c)]=false">
                                                <i class="fa fa-times"></i>
                                            </button>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-md-offset-1 col-md-10">
                                    <label class="control-label" for="black_date">
                                        *Black Date:
                                    </label>
                                    <input type="text" class="form-control" id="black_date"
                                           ng-model="blackData.black_date"
                                           name="black_date">
                                </div>

                                <div class="form-group col-md-offset-1 col-md-10">
                                    <label class="control-label" for="comment">
                                        Comment:
                                    </label>
                                    <input type="text" class="form-control" id="black_comment"
                                           ng-model="blackData.black_comment"
                                           name="black_comment">
                                </div>
                                <div class="form-group col-md-offset-1 col-md-4">
                                    <button type="submit" class="btn btn-default" ng-disabled=" !blackData.black_date||submitting">
                                        <i class="fa fa-pulse fa-spinner" ng-if="submitting"></i>&nbsp;Submit
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

            </div>
        </div>
        <div id="re-embodied-model" class="modal fade" role="dialog" >
            <div class="modal-dialog">

                <!-- Modal content-->
                <div class="box-body modal-content">
                    <form class="form" role="form" method="post" ng-submit="transferAnsar([getSingleRow.ansar_id])">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal"  onclick="event.preventDefault()">&times;</button>
                            <h4 class="modal-title" style="text-align:center">
                                <label class="control-label" for="disembodiment_reason_id">
                                    Ansar ID: [[getSingleRow.ansar_id]]
                                </label><br>
                                <label class="control-label" for="disembodiment_reason_id">
                                    Name: [[getSingleRow.ansar_name_bng]]
                                </label>
                            </h4>
                        </div>

                        <div class="modal-body row">
                            <div class="form-group required col-md-offset-1 col-md-10" ng-if="isAdmin||isRC">
                                <label class="control-label" for="disembodiment_reason_id">
                                    জেলা নির্বাচন করুন:
                                </label>
                                <select ng-model="child.selectedUnit" name="unit" ng-change="loadThana()"
                                        class="form-control">
                                    <option value="">---@lang('title.unit')---</option>
                                    <option ng-repeat="k in units" value="[[k.id]]">[[k.unit_name_bng]]</option>
                                </select>
                            </div>
                            <div class="form-group required col-md-offset-1 col-md-10">
                                <label class="control-label" for="disembodiment_reason_id">
                                    থানা নির্বাচন করুন:<i class="fa fa-spinner fa-pulse" ng-show="loadingThana"></i>
                                </label>
                                <select ng-disabled="loadingThana||loadingKpi" ng-model="selectedThana" name="thana"
                                        class="form-control" ng-change="loadKpi()">
                                    <option value="">---@lang('title.thana')---</option>
                                    <option ng-repeat="k in thanas" value="[[k.id]]">[[k.thana_name_bng]]</option>
                                </select>
                            </div>
                            <div class="form-group required col-md-offset-1 col-md-10">
                                <label class="control-label" for="disembodiment_reason_id">
                                    গার্ড নির্বাচন করুন:<i class="fa fa-spinner fa-pulse" ng-show="loadingKpi"></i>
                                </label>
                                <select ng-disabled="loadingKpi||loadingThana" ng-model="transferData.selectedKpi"
                                        name="transfered_kpi" class="form-control">
                                    <option value="">---@lang('title.kpi')---</option>
                                    <option ng-repeat="k in kpis" value="[[k.id]]" ng-disabled="k.id==getSingleRow.id">
                                        [[k.kpi_name]]
                                    </option>
                                </select>
                            </div>
                            <div class="form-group required col-md-offset-1 col-md-10">
                                <label class="control-label" for="rest_date">
                                    যোগদানের তারিখ:
                                </label>
                                <input type="text" class="form-control" id="joining_date" ng-model="transferData.joining_date"
                                       name="joining_date">
                            </div>
                            <div class="form-group required col-md-offset-1 col-md-10">
                                <label class="control-label" for="memorandum_id">
                                    স্বারক নংঃ <span style="font-weight: normal" ng-if="verifying"><i
                                                class="fa fa-pulse fa-spinner"></i> Verifying</span>
                                </label><span style="color:red"
                                              ng-if="verifyTransfer"> This ID has already been taken</span>
                                <input ng-blur="checkMemorandum(transferData.memorandum_transfer,1)" type="text" class="form-control"
                                       id="memorandum_id"
                                       ng-model="transferData.memorandum_transfer" name="memorandum_id"
                                       placeholder="Enter Memorandum ID">
                            </div>

                            <div class="form-group col-md-offset-1 col-md-4">
                                <button type="submit" class="btn btn-default" ng-disabled="!transferData.memorandum_transfer||!transferData.joining_date||!transferData.selectedKpi||verifyTransfer||verifying||submitting">
                                    <i class="fa fa-pulse fa-spinner" ng-if="submitting"></i>&nbsp;Submit
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

            </div>
        </div>
        <div id="re-embodied-model-mul" class="modal fade" role="dialog" >
            <div class="modal-dialog">

                <!-- Modal content-->
                <div class="box-body modal-content">
                    <form class="form" role="form" method="post" ng-submit="transChecked()">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" onclick="event.preventDefault()">&times;</button>
                            <h4 class="modal-title">
                                Transfer
                            </h4>
                        </div>

                        <div class="modal-body">
                            <div class="row" style="margin-bottom: 10px">
                                <div class="col-sm-12">
                                    <div class="temp-label" ng-repeat="c in checked|filter:actualValue">
                                        <span style="vertical-align: middle">[[allFreezeAnsar[c].ansar_name_bng]]</span>
                                        <span>
                                            <button class="btn btn-box-tool"
                                                    ng-click="checked[checked.indexOf(c)]=false">
                                                <i class="fa fa-times"></i>
                                            </button>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group required col-md-offset-1 col-md-10" ng-if="isAdmin||isRC">
                                    <label class="control-label" for="disembodiment_reason_id">
                                        জেলা নির্বাচন করুন:
                                    </label>
                                    <select ng-model="child.selectedUnit" name="unit" ng-change="loadThana()"
                                            class="form-control">
                                        <option value="">---@lang('title.unit')---</option>
                                        <option ng-repeat="k in units" value="[[k.id]]">[[k.unit_name_bng]]</option>
                                    </select>
                                </div>
                                <div class="form-group required col-md-offset-1 col-md-10">
                                    <label class="control-label" for="disembodiment_reason_id">
                                        থানা নির্বাচন করুন:<i class="fa fa-spinner fa-pulse" ng-show="loadingThana"></i>
                                    </label>
                                    <select ng-disabled="loadingThana||loadingKpi" ng-model="selectedThana" name="thana"
                                            class="form-control" ng-change="loadKpi()">
                                        <option value="">---@lang('title.thana')---</option>
                                        <option ng-repeat="k in thanas" value="[[k.id]]">[[k.thana_name_bng]]</option>
                                    </select>
                                </div>
                                <div class="form-group required col-md-offset-1 col-md-10">
                                    <label class="control-label" for="disembodiment_reason_id">
                                        গার্ড নির্বাচন করুন:<i class="fa fa-spinner fa-pulse" ng-show="loadingKpi"></i>
                                    </label>
                                    <select ng-disabled="loadingKpi||loadingThana" ng-model="transferData.selectedKpi"
                                            name="transfered_kpi" class="form-control">
                                        <option value="">---@lang('title.kpi')---</option>
                                        <option ng-repeat="k in kpis" value="[[k.id]]" ng-disabled="k.id==getSingleRow.id">
                                            [[k.kpi_name]]
                                        </option>
                                    </select>
                                </div>
                                <div class="form-group required col-md-offset-1 col-md-10">
                                    <label class="control-label" for="rest_date">
                                        যোগদানের তারিখ:
                                    </label>
                                    <input type="text" class="form-control" id="joining_date" ng-model="transferData.joining_date"
                                           name="joining_date">
                                </div>
                                <div class="form-group required col-md-offset-1 col-md-10">
                                    <label class="control-label" for="memorandum_id">
                                        স্বারক নংঃ <span style="font-weight: normal" ng-if="verifying"><i
                                                    class="fa fa-pulse fa-spinner"></i> Verifying</span>
                                    </label><span style="color:red"
                                                  ng-if="verifyTransfer"> This ID has already been taken</span>
                                    <input ng-blur="checkMemorandum(transferData.memorandum_transfer,1)" type="text" class="form-control"
                                           id="memorandum_id"
                                           ng-model="transferData.memorandum_transfer" name="memorandum_id"
                                           placeholder="Enter Memorandum ID">
                                </div>
                                <div class="form-group col-md-offset-1 col-md-4">
                                    <button type="submit" class="btn btn-default" ng-disabled="!transferData.memorandum_transfer||!transferData.joining_date||!transferData.selectedKpi||verifyTransfer||verifying||submitting">
                                        <i class="fa fa-pulse fa-spinner" ng-if="submitting"></i>&nbsp;Submit
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

            </div>
        </div>
        <div id="dis-embodied-model-multiple" class="modal fade" role="dialog">
            <div class="modal-dialog">

                <!-- Modal content-->
                <div class="modal-content">
                    <form class="form" role="form" method="post" ng-submit="disEmbodiedChecked()">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h4 class="modal-title">
                                Dis-Embodied
                            </h4>
                        </div>

                        <div class="modal-body">
                            <div class="row" style="margin-bottom: 10px">
                                <div class="col-sm-12">
                                    <div class="temp-label" ng-repeat="c in checked|filter:actualValue">
                                        <span style="vertical-align: middle">[[allFreezeAnsar[c].ansar_name_bng]]</span>
                                        <span>
                                            <button class="btn btn-box-tool"
                                                    ng-click="checked[checked.indexOf(c)]=false">
                                                <i class="fa fa-times"></i>
                                            </button>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-md-offset-1 col-md-10">
                                    <label class="control-label" for="memorandum_id">
                                        *স্বারক নংঃ <span style="font-weight: normal" ng-if="verifying"><i
                                                    class="fa fa-pulse fa-spinner"></i> Verifying</span>
                                    </label><span style="color:red"
                                                  ng-if="verify"> This id has already been taken</span>
                                    <input ng-blur="checkMemorandum(formData.memorandum,0)" type="text"
                                           class="form-control"
                                           id="memorandum_id" ng-model="formData.memorandum" name="memorandum_id">
                                </div>

                                <div class="form-group col-md-offset-1 col-md-10">
                                    <label class="control-label" for="rest_date">
                                        *Disembodiment Date:
                                    </label>
                                    <input type="text" class="form-control" id="rest_date" id="memorandum_id"
                                           ng-model="formData.rest_date"
                                           name="rest_date">
                                </div>

                                <div class="form-group col-md-offset-1 col-md-10">
                                    <label class="control-label" for="disembodiment_reason_id">
                                        *Reason:
                                    </label>
                                    <select ng-model="formData.disembodiment_reason_id" name="disembodiment_reason_id"
                                            class="form-control">
                                        <option value="">---অ-অঙ্গীভূত এর কারণ নির্বাচন করুন---</option>
                                        <option value=1>অঙ্গীভূত কাল শেষ  হলে</option>
                                        <option value=2>পদত্যাগ পত্র গৃহীত হলে</option>
                                        <option value=3>পেশাগত কাজে অযোগ্য</option>
                                        <option value=4>শারীরিক ও মানসিক ভাবে অক্ষম</option>
                                        <option value=5>শৃ্ঙ্খলা ভঙ্গের কারনে শাস্তিপ্রাপ্ত</option>
                                        <option value=6>সংশ্লিষ্ট সংস্থা বা প্রতিষ্ঠানে অঙ্গীভূত আনসার নিযুক্ত রাখার
                                            প্রয়োজনীয়তা শেষ হলে
                                        </option>
                                        <option value=7>মহাপরিচালক কর্তৃক নির্ধারিত অন্যান্য কারনে</option>
                                        <option value=8>অন্যান্য কারনে</option>
                                    </select>
                                </div>

                                <div class="form-group col-md-offset-1 col-md-10">
                                    <label class="control-label" for="comment">
                                        Comment:
                                    </label>
                                    <input type="text" class="form-control" id="comment" ng-model="formData.comment"
                                           name="comment">
                                </div>
                                <div class="form-group col-md-offset-1 col-md-4">
                                    <button type="submit" class="btn btn-default"
                                            ng-disabled="!formData.disembodiment_reason_id || !formData.memorandum || !formData.rest_date || verify||submitting">
                                        <i class="fa fa-pulse fa-spinner" ng-if="submitting"></i>Submit
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

            </div>
        </div>
        <div id="continue-modal" class="modal fade" role="dialog">
            <div class="modal-dialog">

                <!-- Modal content-->
                <div class="modal-content">
                    <form class="form" role="form" method="post" confirm callback="continueChecked()" event="submit"
                          message="Are you sure want to Re-Embodied those ansar">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal"
                                    onclick="event.preventDefault()">&times;</button>
                            <h4 class="modal-title">
                                Dis-Embodied
                            </h4>
                        </div>

                        <div class="modal-body">
                            <div class="row" style="margin-bottom: 10px">
                                <div class="col-sm-12">
                                    <div class="temp-label" ng-repeat="c in checked|filter:actualValue">
                                        <span style="vertical-align: middle">[[allFreezeAnsar[c].ansar_name_bng]]</span>
                                        <span>
                                            <button class="btn btn-box-tool"
                                                    ng-click="checked[checked.indexOf(c)]=false">
                                                <i class="fa fa-times"></i>
                                            </button>
                                        </span>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-default pull-right" ng-disabled="submitting">
                                <i class="fa fa-pulse fa-spinner" ng-if="submitting"></i>Submit
                            </button>
                        </div>
                    </form>
                </div>

            </div>
        </div>

    </div>
    <script>
        $("input[name='black_date']").datePicker({
            defaultValue:false
        })
    </script>
@stop