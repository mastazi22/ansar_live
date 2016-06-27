{{--User: Shreya--}}
{{--Date: 1/5/2015--}}
{{--Time: 12:49 PM--}}

@extends('template.master')
@section('content')
    <script>
        $(document).ready(function () {
            $('#reduce_guard_strength_date').datePicker(true);
        })
        GlobalApp.controller('AnsarReduceController', function ($scope, $http) {
            $scope.isAdmin = parseInt('{{Auth::user()->type}}')
            $scope.districts = [];
            $scope.thanas = [];
            $scope.selectedDistrict = "";
            $scope.selectedThana = "";
            $scope.selectedKpi = "";
            $scope.loadingUnit = false;
            $scope.loadingThana = false;
            $scope.loadingKpi = false;
            $scope.memorandumId = "";
            $scope.isVerified = false;
            $scope.isVerifying = false;
            $scope.allLoading = false;
            $scope.reduce_reason = "Freeze for Guard Reduction";
//
            $scope.dcDistrict = parseInt('{{Auth::user()->district_id}}');
            $scope.verifyMemorandumId = function () {
                var data = {
                    memorandum_id: $scope.memorandumId
                }
                $scope.isVerified = false;
                $scope.isVerifying = true;
                $http.post('{{URL::to('verify_memorandum_id')}}', data).then(function (response) {
//                    alert(response.data.status)
                    $scope.isVerified = response.data.status;
                    $scope.isVerifying = false;
                }, function (response) {

                })
            }
            $scope.loadDistrict = function () {
                $scope.loadingUnit = true;
                $http({
                    method: 'get',
                    url: '{{URL::to('HRM/DistrictName')}}'
                }).then(function (response) {
                    $scope.districts = response.data;
                    $scope.loadingUnit = false;
                    $scope.thanas = [];
                    $scope.selectedThana = "";
                })
            }
            $scope.loadThana = function (id) {
                $scope.loadingThana = true;
                $http({
                    method: 'get',
                    url: '{{URL::to('HRM/ThanaName')}}',
                    params: {id: id}
                }).then(function (response) {
                    $scope.thanas = response.data;
                    $scope.selectedThana = "";
                    $scope.loadingThana = false;
                })
            }
            $scope.loadGuard = function (id) {
                $scope.loadingKpi = true;
                $http({
                    method: 'get',
                    url: '{{URL::route('kpi_name')}}',
                    params: {id: id}
                }).then(function (response) {
                    $scope.guards = response.data;
                    $scope.selectedKpi = "";
                    $scope.loadingKpi = false;
                })
            }
            $scope.loadAnsar = function (id) {
                $http({
                    method: 'get',
                    url: '{{URL::route('guard_list')}}',
                    params: {kpi_id: id}
                }).then(function (response) {
                    $scope.ansars = response.data.ansars;
                    $scope.guardDetail = response.data.guard;
                })
            }
            if ($scope.isAdmin == 11) {
                $scope.loadDistrict()
            }
            else {
                if (!isNaN($scope.dcDistrict)) {
                    $scope.loadThana($scope.dcDistrict)
                }
            }

        })
        GlobalApp.directive('openHideModal', function () {
            return {
                restrict: 'AC',
                link: function (scope, elem, attr) {
                    $(elem).on('click', function () {
                        //alert("hh")
                        scope.memorandumId = "";
                        scope.reduce_guard_strength_date = "";
                        scope.$digest()
                        $("#reduce-guard-option").modal("toggle")
//                        $("#withrdaw-option").on('show.bs.modal', function () {
//                            alert("hh")
//                            scope.memorandumId = "";
//                            scope.kpi_withdraw_reason = "";
//                            scope.kpi_withdraw_date = "";
//                        })
//                        $("#withrdaw-option").on('hide.bs.modal', function () {
//                            //modalOpen = false;
//                        })
                    })
                }
            }
        })
    </script>
    <div ng-controller="AnsarReduceController">
        {{--<div class="breadcrumbplace">--}}
            {{--{!! Breadcrumbs::render('reduce_guard_strength') !!}--}}
        {{--</div>--}}
        <div id="all-loading"
             style="position:fixed;width: 100%;height: 100%;background-color: rgba(255, 255, 255, 0.27);z-index: 100; margin-left: 30%; display: none; overflow: hidden">
            <div style="position: fixed;width: 20%;height: auto;margin: 20% auto;text-align: center;background: #FFFFFF">
                <img class="img-responsive" src="{{asset('dist/img/loading-data.gif')}}"
                     style="position: relative;margin: 0 auto">
                <h4>Loading....</h4>
            </div>

        </div>
        @if(Session::has('success_message'))
            <div style="padding: 10px 20px 0 20px;">
                <div class="alert alert-success">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                    <span class="glyphicon glyphicon-ok"></span> {{Session::get('success_message')}}
                </div>
            </div>
        @endif
        <section class="content">
            <div class="box box-solid">
                <div class="nav-tabs-custom" style="background-color: transparent">
                    <ul class="nav nav-tabs">
                        <li class="active">
                            <a href="#">Reduce Guard Strength</a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane active">
                            <div class="row">
                                <div class="col-sm-4" ng-show="isAdmin==11">
                                    <div class="form-group">
                                        <label class="control-label">
                                            Select a District&nbsp;&nbsp;
                                            <img src="{{asset('dist/img/facebook.gif')}}" style="width: 16px;"
                                                 ng-show="loadingUnit">
                                        </label>
                                        <select class="form-control" ng-disabled="loadingUnit||loadingThana||loadingKpi"
                                                ng-model="selectedDistrict"
                                                ng-change="loadThana(selectedDistrict)">
                                            <option value="">--Select a District--</option>
                                            <option ng-repeat="d in districts" value="[[d.id]]">[[d.unit_name_bng]]
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label class="control-label">
                                            Select a Thana&nbsp;&nbsp;
                                            <img src="{{asset('dist/img/facebook.gif')}}" style="width: 16px;"
                                                 ng-show="loadingThana">
                                        </label>
                                        <select class="form-control" ng-disabled="loadingUnit||loadingThana||loadingKpi"
                                                ng-model="selectedThana"
                                                ng-change="loadGuard(selectedThana)">
                                            <option value="">--Select a Thana--</option>
                                            <option ng-repeat="t in thanas" value="[[t.id]]">[[t.thana_name_bng]]
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label class="control-label">
                                            Select a Guard&nbsp;&nbsp;
                                            <img src="{{asset('dist/img/facebook.gif')}}" style="width: 16px;"
                                                 ng-show="loadingKpi">
                                        </label>
                                        <select class="form-control" ng-disabled="loadingUnit||loadingThana||loadingKpi"
                                                ng-model="selectedKPI"
                                                id="kpi_name_list_for_reduce" name="kpi_name_list_for_reduce">
                                            <option value="">--Select a Guard--</option>
                                            <option ng-repeat="d in guards" value="[[d.id]]">[[d.kpi_name]]
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="table-responsive">
                                        <table class="table table-bordered" id="pc-table">

                                            <tr class="info">
                                                <th>Ansar ID</th>
                                                <th>Ansar Name</th>
                                                <th>Ansar Designation</th>
                                                <th>Ansar Sex</th>
                                                <th>KPI Name</th>
                                                <th>KPI District</th>
                                                <th>KPI Unit</th>
                                                <th>Reporting Date</th>
                                                <th>Embodiment Date</th>
                                                <th>Select From Here</th>
                                            </tr>
                                            <tbody id="ansar-all-for-reduce" class="status">
                                            <tr colspan="10" class="warning" id="not-find-info">
                                                <td colspan="10">No Ansar Found to Withdraw</td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <button class="pull-right btn btn-primary" id="reduce-guard-strength-confirmation" open-hide-modal disabled>
                        <i class="fa fa-send"></i>&nbsp;&nbsp;Reduce Guard Strength
                    </button>
                </div>
            </div>
            <!--Modal Open-->
            <div id="reduce-guard-option" class="modal fade" role="dialog">
                <div class="modal-dialog" style="width: 70%;">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal"
                                    ng-click="modalOpen = false">&times;</button>
                            <h3 class="modal-title">Ansar for Reduction</h3>
                        </div>
                        <div class="modal-body">
                            {!! Form::open(array('route' => 'ansar-reduce-update', 'name' => 'kpiReduceForm', 'ng-app' => 'myValidateApp', 'novalidate')) !!}
                            <div class="register-box" style="width: auto;margin: 0">
                                <div class="register-box-body  margin-bottom">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <div class="form-group">
                                                <label class="control-label">Memorandum no.&nbsp;&nbsp;&nbsp;<span
                                                            ng-show="isVerifying"><i
                                                                class="fa fa-spinner fa-pulse"></i>&nbsp;Verifying</span><span
                                                            class="text-danger"
                                                            ng-if="isVerified&&!memorandumId">Memorandum ID is required.</span><span
                                                            class="text-danger"
                                                            ng-if="isVerified&&memorandumId">This id already taken.</span></label>
                                                <input ng-blur="verifyMemorandumId()" ng-model="memorandumId"
                                                       type="text" class="form-control" name="memorandum_id"
                                                       placeholder="Enter memorandum id" required>
                                            </div>
                                        </div>
                                        <div class="col-sm-4"
                                             ng-class="{ 'has-error': kpiReduceForm.reduce_guard_strength_date.$touched && kpiReduceForm.reduce_guard_strength_date.$invalid }">
                                            <div class="form-group">
                                                <label class="control-label">Date of
                                                    Withdrawal:&nbsp;&nbsp;&nbsp;<span class="text-danger"
                                                                                       ng-if="kpiReduceForm.reduce_guard_strength_date.$touched && kpiReduceForm.reduce_guard_strength_date.$error.required">Date is required.</span></label>
                                                {!! Form::text('reduce_guard_strength_date', $value = null, $attributes = array('class' => 'form-control', 'id' => 'reduce_guard_strength_date', 'ng_model' => 'reduce_guard_strength_date', 'required')) !!}
                                            </div>
                                        </div>
                                        <div class="col-sm-4"
                                             ng-class="{ 'has-error': kpiReduceForm.reduce_reason.$touched && kpiReduceForm.reduce_reason.$invalid }">
                                            <div class="form-group">
                                                <label class="control-label">Reason of
                                                    Withdrawal:&nbsp;&nbsp;&nbsp;<span class="text-danger"
                                                                                       ng-if="kpiReduceForm.reduce_reason.$touched && kpiReduceForm.reduce_reason.$error.required">Reason is required.</span></label>
                                                {!! Form::text('reduce_reason', $value = null, $attributes = array('class' => 'form-control', 'id' => 'reduce_reason', 'ng_model' => 'reduce_reason', 'required')) !!}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table table-bordered" id="pc-table">
                                            <tr class="info">
                                                <th>Ansar ID</th>
                                                <th>Ansar Name</th>
                                                <th>Ansar Designation</th>
                                                <th>Ansar Sex</th>
                                                <th>KPI Name</th>
                                                <th>KPI District</th>
                                                <th>KPI Unit</th>
                                            </tr>
                                            <tbody id="ansar-all-for-reduce-modal">
                                            </tbody>
                                        </table>
                                    </div>
                                    <button id="reduce-confirm" class="btn btn-primary pull-right"
                                            ng-disabled="kpiReduceForm.reduce_guard_strength_date.$error.required||kpiReduceForm.reduce_reason.$error.required||!memorandumId||isVerified||isVerifying">
                                        <i class="fa fa-check"></i>&nbsp;Confirm
                                    </button>
                                    {!! Form::close() !!}
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
            <!--Modal Close-->
            <!-- /.row -->
        </section>
    </div>
    <script>

        var selectedAnsars = [];
        var ansar_ids = [];
        $('#kpi_name_list_for_reduce').change(function () {
            $("#all-loading").css('display', 'block');
            var selectedKPIName = $('select[name=kpi_name_list_for_reduce]').val();
            $.ajax({
                url: '{{URL::route('ansar_list_for_reduce')}}',
                type: 'get',
                data: {selected_name: selectedKPIName},
                success: function (data) {
                    $("#all-loading").css('display', 'none');
//                    $("#ansar-all-for-reduce").html(data);
//                    alert(data)
//
                    if (data.result == undefined) {
                        $('#reduce-guard-strength-confirmation').prop('disabled', false);
                        $("#ansar-all-for-reduce").html(data);
                        //h = data;
                    }
                    else {
                        $('#reduce-guard-strength-confirmation').prop('disabled', true);
//                        alert($("#status-all").html())
                        $("#ansar-all-for-reduce").html('<tr colspan="11" class="warning" id="not-find-info"> <td colspan="11">No Ansar Found to add to Reduce</td> </tr>');
                    }
                }
            });
        })
        $('#reduce-guard-strength-confirmation').click(function () {
            var innerHtml = "";
            selectedAnsars.forEach(function (a, b, c) {
                var d = a.clone();
                d.children('td')[0].innerHTML += "<input type='hidden' name='selected-ansar_id[]' value='" + $.trim($(d.children('td')[0]).text()) + "'>";
                ansar_ids.push($.trim($(d.children('td')[0]).text()));
                d.children('td')[9].remove();
                d.children('td')[8].remove();
                d.children('td')[7].remove();
                innerHtml += '<tr>' + d.html() + '</tr>';
            })
            $('#ansar-all-for-reduce-modal').html(innerHtml)
        });
        $(document).on('change', '.reduce-guard-strength-check', function () {

            selectedAnsars=[]
            $('.reduce-guard-strength-check:checked').each(function () {
                selectedAnsars.push($(this).parents('tr'))
            })
        });
        $('#reduce-confirm').click(function (e) {
            $("#all-loading").css('display', 'block');
            e.preventDefault();
            //alert(ansar_ids);
            $.ajax({


                url: '{{URL::route('ansar-reduce-update')}}',
                type: "get",

                data: {ansaridget: ansar_ids, memorandum_id: $('input[name=memorandum_id]').val(), reduce_date:$('input[name= reduce_guard_strength_date]').val(), reduce_reason:$('input[name= reduce_reason]').val()},

                success: function (data) {
                    console.log(data)
                    selectedAnsars.forEach(function (a, b, c) {
                        a.remove()
                    })
                    if(data.status){
                        $("#all-loading").css('display', 'none');
                        $('body').notifyDialog({type:'success',message:data.message}).showDialog();
                    }
                    // $('#success-message').css('display','block')
                    //$('#success-message').children('div').append(data.message)
                },
                error: function (res) {
                    $("#all-loading").css('display', 'none');
                    $('body').notifyDialog({type:'error',message:"Server Error. Please Try again!"}).showDialog();
                }
            });
            $(".close").trigger('click');
        });
    </script>
@stop