@extends('template.master')

@section('content')

    <script>

        GlobalApp.controller('freezeController', function ($scope, $http) {
//        $scope.filter_name = "0";
            $scope.loading = false;
            $scope.allFreezeAnsar = [];
            $scope.loadingThana = false;
            $scope.loadingKpi = false;
            $scope.units = [];
            $scope.thanas = [];
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
                $scope.loading = true;
                $http({
                    url: "{{URL::route('getfreezelist')}}",
                    method: 'get',
                    params: {filter: $scope.filter_name}
                }).then(function (response) {
//            alert(JSON.stringify(response.data));
                    $scope.allFreezeAnsar = response.data;
                    $scope.loading = false;
                }, function (response) {
                    $scope.loading = false;
                })
            }
//        $scope.getFreezeList();

            $scope.reEmbodied = function (ansarid, index) {
                $http({
                    url: "{{URL::to('HRM/freezeRembodied')}}/" + ansarid,
                    method: 'get'
                }).then(function (response) {
//                    alert(JSON.stringify(response.data));
                    $scope.allFreezeAnsar.splice(index, 1);
                })
            }
            $scope.transferAnsar = function (ansarId, mem_id, t_date, kpi) {
                //alert($scope.child.memorandum)
                //alert(ansarId+" "+index+" "+mem_id+" "+t_date+" "+kpi);
                //return;
                $http({
                    url: '{{URL::route('transfer_freezed_ansar')}}',
                    method: 'post',
                    data: angular.toJson({
                        ansar_id: ansarId,
                        mem_id: mem_id,
                        transfered_date: t_date,
                        kpi_id: kpi
                    })
                }).then(function (response) {
                    console.log(response.data)
                })
            }
            $scope.checkMemorandum = function (id,type) {
                $scope.verifying = true;
                $http({
                    url: "{{action('UserController@verifyMemorandumId')}}",
                    method: 'post',
                    data: {memorandum_id: id}
                }).then(function (response) {
                    if(type==0) $scope.verify = response.data.status;
                    else $scope.verifyTransfer = response.data.status;
                    console.log(response.data)
                    $scope.verifying = false;
                })
            }
            $scope.disEmbodied = function (ansarid, index) {
                if (ansarid) {
                    $http({
                        url: "{{URL::to('HRM/freezeDisEmbodied')}}/" + $scope.getSingleRow.ansar_id,
                        method: 'post',
                        data: {
                            memorandum: $scope.memorandum,
                            rest_date: $scope.rest_date,
                            reason: $scope.disembodiment_reason_id,
                            comment: $scope.comment,
                        }
                    }).then(function (response) {
//                    alert(JSON.stringify(response.data));
                        $scope.allFreezeAnsar.splice($scope.allFreezeAnsar.indexOf($scope.getSingleRow), 1)
                    }, function (response) {
                        document.getElementById("error").innerHTML = response.data;
                    })
                }
            }
            $scope.blackAnsar = function (ansarid, index) {
                if (ansarid) {
                    $http({
                        url: "{{URL::to('HRM/freezeblack')}}/" + $scope.getSingleRow.ansar_id,
                        method: 'post',
                        data: {
                            black_date: $scope.black_date,
                            black_comment: $scope.black_comment,
                        }
                    }).then(function (response) {
//                    alert(JSON.stringify(response.data));
                        $scope.allFreezeAnsar.splice($scope.allFreezeAnsar.indexOf($scope.getSingleRow), 1)
                    }, function (response) {
                        document.getElementById("error").innerHTML = response.data;
                    })
                }
            }
            $scope.convertDate = function (d) {
                return moment(d).format('DD-MMM-YYYY')
            }
            $scope.modal = function (index) {
                $scope.getSingleRow = $scope.allFreezeAnsar[index];
            }
        })

        GlobalApp.directive('confirmDialog', function ($parse) {
            return {
                restrict: 'A',
                link: function (scope, elem, attr) {
                    var b = JSON.parse(attr.confirmDialog);
                    var d = scope.allFreezeAnsar[b.index];
                    $(elem).on('click', function () {
                        if(b.action == "continue") {
                            if (d.withdraw_status == 1) {
                                $('body').notifyDialog({type: 'error', message: 'This kpi already withdrawed'})
                                return;
                            }
                            else if (d.withdraw_date) {
                                $(elem).confirmDialog({
                                    message: 'This ansar kpi will be withdraw in '+moment(d.withdraw_date).format("DD-MMM-YYYY")+'.<br> Are you sure re-embodied this ansar',
                                    ok_button_text: 'Yes',
                                    cancel_button_text: 'No,Thanks',
                                    ok_callback: function (element) {
                                        scope.reEmbodied(b.ansarid, b.index);
                                    },
                                    cancel_callback: function (element) {
//                            alert('Canceled');
                                    }
                                })
                            }
                            else{
                                $(elem).confirmDialog({
                                    message: 'Are you sure want to continue this ansar',
                                    ok_button_text: 'Yes',
                                    cancel_button_text: 'No,Thanks',
                                    ok_callback: function (element) {
                                        scope.reEmbodied(b.ansarid, b.index);
                                    },
                                    cancel_callback: function (element) {
//                            alert('Canceled');
                                    }
                                })
                            }
                        }
                        else if (b.action == "dis-embodied") {
                            $(elem).confirmDialog({
                                message: 'Are you sure want to dis-embodied this ansar',
                                ok_button_text: 'Yes',
                                cancel_button_text: 'No,Thanks',
                                ok_callback: function (element) {
                                    scope.disEmbodied(b.ansarid, b.index);
                                },
                                cancel_callback: function (element) {
//                            alert('Canceled');
                                }
                            })
                        }
                        else if (b.action == "black") {
                            $(elem).confirmDialog({
                                message: 'Are you sure want to Black this ansar',
                                ok_button_text: 'Yes',
                                cancel_button_text: 'No,Thanks',
                                ok_callback: function (element) {
                                    scope.blackAnsar(b.ansarid, b.index);
                                },
                                cancel_callback: function (element) {
//                            alert('Canceled');
                                }
                            })
                        }
                    })

                }
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

            <div class="loading-report animated" ng-show="loading">
                <img src="{{asset('dist/img/ring-alt.gif')}}" class="center-block">
                <h4>Loading...</h4>
            </div>
            <div class="row">
                <div style="width:90%;margin:0 auto;">
                    <div class="row">
                        <div style="width: 97%;margin:0 auto;">
                            <div class="table-header">
                                <h4>Freeze list</h4>
                            </div>

                        </div>
                    </div>
                    <div class="clearfix"></div>
                    <div class="box table-list" style="margin-top: 0px;">
                        <div class="pull-left form-group" style="margin: 10px 0 0 10px;">
                            <h5 class="pull-left" style="padding-right:5px; ;"><b>Filter by: </b></h5>
                            <select ng-model="filter_name" name="filter_name" class="form-control pull-left"
                                    style="width:75%;" ng-change="getFreezeList()">
                                <option value="">--Select freeze reason--</option>
                                <option value="0">All</option>
                                <option value="1">Guard Withdraw</option>
                                <option value="2">Guard Reduce</option>
                                <option value="3">Disciplinary Actions</option>
                            </select>
                        </div>
                        <div class="clearfix"></div>
                        <div class="box-body" id="change-body">
                            <div class="loading-data"><i class="fa fa-4x fa-refresh fa-spin loading-icon"></i>
                            </div>
                            <div class="table-responsive">
                                <table class="table  table-bordered table-striped" id="ansar-table">

                                    <tr>
                                        <th class="text-center"> ক্রঃ নং</th>
                                        <th class="text-center">আইডি</th>
                                        <th class="text-center">পদবি</th>
                                        <th class="text-center">নাম</th>
                                        <th class="text-center">নিজ জেলা</th>
                                        <th class="text-center">অঙ্গীভূত তারিখ</th>
                                        <th class="text-center">ফ্রিজ করনের তারিখ</th>
                                        <th class="text-center">ফ্রিজকালীন ক্যাম্পের নাম</th>
                                        <th class="text-center">ফ্রিজকরনের কারণ</th>
                                        <th class="text-center" style="width:100px;">কার্যক্রম/Action</th>

                                    </tr>
                                    <tr ng-show="allFreezeAnsar.length>0" ng-repeat="freezeAnsar in allFreezeAnsar">
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
                                               title="Re-embodied">
                                                <span class="fa fa-check"></span>
                                                <!--<i class="fa fa-spinner fa-pulse"></i>-->
                                            </a>

                                            <div class="test-dropdown-below">
                                                <ul>
                                                    <li>
                                                        <button class="btn btn-primary"
                                                                confirm-dialog='{"ansarid":"[[freezeAnsar.ansar_id]]","index":"[[$index]]","action":"continue"}'>
                                                            Continue
                                                        </button>
                                                    </li>
                                                    <li>
                                                        <button class="btn btn-primary" id="reEmbodied"
                                                                ng-click="modal($index)">Re Emodied
                                                        </button>
                                                    </li>
                                                </ul>


                                            </div>
                                            <button class="btn btn-danger btn-xs verification" title="Dis-embodied"
                                                    data-toggle="modal" data-target="#myModal"
                                                    ng-click="modal($index)">
                                                <span class="fa fa-retweet"></span>
                                                <!--<i class="fa fa-spinner fa-pulse"></i>-->
                                            </button>
                                            <a class="btn btn-danger btn-xs verification" title="Black"
                                               data-toggle="modal" data-target="#blackModal" ng-click="modal($index)">
                                                <span class="fa fa-remove"></span>
                                                <!--<i class="fa fa-spinner fa-pulse"></i>-->
                                            </a>
                                        </td>
                                    </tr>
                                    <tr ng-show="allFreezeAnsar.length==0">
                                        <td class="warning" colspan="10">No ansar found</td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                    </div>

                </div>
            </div>
        </section>
        <div id="myModal" class="modal fade" role="dialog">
            <div class="modal-dialog">

                <!-- Modal content-->
                <div class="modal-content">
                    <form class="form" role="form" method="post">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h4 class="modal-title">Ansar
                                Id:[[getSingleRow.ansar_id]],Name:[[getSingleRow.ansar_name_bng]]</h4>
                        </div>

                        <div class="modal-body row">


                            <div class="form-group col-md-offset-1 col-md-10">
                                <label class="control-label" for="memorandum_id">
                                    *স্বারক নংঃ  <span style="font-weight: normal"  ng-if="verifying"><i class="fa fa-pulse fa-spinner"></i> Verifying</span>
                                </label><span style="color:red" ng-if="verify"> This id has already been taken</span>
                                <input ng-blur="checkMemorandum(memorandum,0)" type="text" class="form-control" id="memorandum_id" ng-model="memorandum" name="memorandum_id">
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
                                <button type="button" class="btn btn-default" data-dismiss="modal"
                                        confirm-dialog='{"ansarid":"[[getSingleRow.ansar_id]]","index":"[[getSingleRow.index]]","action":"dis-embodied"}'
                                        ng-disabled="!disembodiment_reason_id || !memorandum || !rest_date || verify">
                                    Submit
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

            </div>
        </div>
        <div id="blackModal" class="modal fade" role="dialog">
            <div class="modal-dialog">

                <!-- Modal content-->
                <div class="modal-content">
                    <form class="form" role="form" method="post">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h4 class="modal-title">Ansar
                                Id:[[getSingleRow.ansar_id]],Name:[[getSingleRow.ansar_name_bng]]</h4>
                        </div>
                        <div class="modal-body row">

                            <div class="form-group col-md-offset-1 col-md-10">
                                <label class="control-label" for="black_date">
                                    *Black Date:
                                </label>
                                <input type="text" class="form-control" id="memorandum_id" ng-model="black_date"
                                       name="black_date">
                            </div>

                            <div class="form-group col-md-offset-1 col-md-10">
                                <label class="control-label" for="comment">
                                    Comment:
                                </label>
                                <input type="text" class="form-control" id="black_comment" ng-model="black_comment"
                                       name="black_comment">
                            </div>
                            <div class="form-group col-md-offset-1 col-md-4">
                                <button type="button" class="btn btn-default" data-dismiss="modal"
                                        confirm-dialog='{"ansarid":"[[getSingleRow.ansar_id]]","index":"[[getSingleRow.index]]","action":"black"}'
                                        ng-disabled=" !black_date">Submit
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

            </div>
        </div>
        <div id="re-embodied-model" class="modal fade" role="dialog">
            <div class="modal-dialog">

                <!-- Modal content-->
                <div class="modal-content">
                    <form class="form" role="form" method="post">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h4 class="modal-title">Ansar
                                Id:[[getSingleRow.ansar_id]],Name:[[getSingleRow.ansar_name_bng]]</h4>
                        </div>

                        <div class="modal-body row">
                            <div class="form-group col-md-offset-1 col-md-10" ng-if="isAdmin||isRC">
                                <label class="control-label" for="disembodiment_reason_id">
                                    *জেলা নির্বাচন করুন:
                                </label>
                                <select ng-model="child.selectedUnit" name="unit" ng-change="loadThana()"
                                        class="form-control">
                                    <option value="">---Select a unit---</option>
                                    <option ng-repeat="k in units" value="[[k.id]]">[[k.unit_name_bng]]</option>
                                </select>
                            </div>
                            <div class="form-group col-md-offset-1 col-md-10">
                                <label class="control-label" for="disembodiment_reason_id">
                                    *থানা নির্বাচন করুন:<i class="fa fa-spinner fa-pulse" ng-show="loadingThana"></i>
                                </label>
                                <select ng-disabled="loadingThana||loadingKpi" ng-model="selectedThana" name="thana" class="form-control" ng-change="loadKpi()">
                                    <option value="">---Select a Thana---</option>
                                    <option ng-repeat="k in thanas" value="[[k.id]]">[[k.thana_name_bng]]</option>
                                </select>
                            </div>
                            <div class="form-group col-md-offset-1 col-md-10">
                                <label class="control-label" for="disembodiment_reason_id">
                                    *গার্ড নির্বাচন করুন:<i class="fa fa-spinner fa-pulse" ng-show="loadingKpi"></i>
                                </label>
                                <select ng-disabled="loadingKpi||loadingThana" ng-model="selectedKpi" name="transfered_kpi" class="form-control">
                                    <option value="">---Select a kpi---</option>
                                    <option ng-repeat="k in kpis" value="[[k.id]]" ng-disabled="k.id==getSingleRow.id">
                                        [[k.kpi_name]]
                                    </option>
                                </select>
                            </div>
                            <div class="form-group col-md-offset-1 col-md-10">
                                <label class="control-label" for="rest_date">
                                    *যোগদানের তারিখ:
                                </label>
                                <input type="text" class="form-control" id="joining_date" ng-model="joining_date"
                                       name="joining_date">
                            </div>
                            <div class="form-group col-md-offset-1 col-md-10">
                                <label class="control-label" for="memorandum_id">
                                    *স্বারক নংঃ  <span style="font-weight: normal"  ng-if="verifying"><i class="fa fa-pulse fa-spinner"></i> Verifying</span>
                                </label><span style="color:red" ng-if="verifyTransfer"> This id has already been taken</span>
                                <input ng-blur="checkMemorandum(memorandum_transfer,1)" type="text" class="form-control"
                                       id="memorandum_id"
                                       ng-model="memorandum_transfer" name="memorandum_id">
                            </div>

                            <div class="form-group col-md-offset-1 col-md-4">
                                <button type="button" class="btn btn-default" data-dismiss="modal"
                                        ng-disabled="!memorandum_transfer||!joining_date||!selectedKpi||verifyTransfer||verifying"
                                        ng-click="transferAnsar(getSingleRow.ansar_id,memorandum_transfer,joining_date,selectedKpi)"
                                        >
                                    Submit
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

            </div>
        </div>

    </div>
@stop