{{--User: Shreya--}}
{{--Date: 10/15/2015--}}
{{--Time: 10:49 AM--}}

@extends('template.master')
@section('title','Withdraw Ansar')
@section('breadcrumb')
    {!! Breadcrumbs::render('ansar_withdraw_view') !!}
    @endsection
@section('content')
    <script>
        $(document).ready(function () {
            $('#kpi_withdraw_date').datePicker(true);
        })
        GlobalApp.controller('ReportGuardSearchController', function ($scope, $http, $sce) {
            $scope.isAdmin = parseInt('{{Auth::user()->type}}')
            $scope.districts = [];
            $scope.thanas = [];
            $scope.selectedDistrict = "";
            $scope.selectedThana = "";
            $scope.selectedKpi = "";
            $scope.guards = [];
            $scope.guardDetail = [];
            $scope.ansars = [];
            $scope.loadingUnit = false;
            $scope.loadingThana = false;
            $scope.loadingKpi = false;
            $scope.report = {};
            $scope.reportType = 'eng';
            $scope.memorandumId = "";
            $scope.isVerified = false;
            $scope.isVerifying = false;
            $scope.allLoading = false;
            $scope.kpi_withdraw_reason = "Freeze Ansar for Withdrawal";
            $scope.kpi_withdraw_date="";
            $scope.dcDistrict = parseInt('{{Auth::user()->district_id}}');
            $scope.errorMessage = '';
            $scope.errorFound = 0;
            $scope.verifyMemorandumId = function () {
                var data = {
                    memorandum_id: $scope.memorandumId
                }
                $scope.isVerified = false;
                $scope.isVerifying = true;
                $http.post('{{action('UserController@verifyMemorandumId')}}', data).then(function (response) {
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
                    $scope.errorFound = 0;
                    $scope.ansars = response.data.ansars;
                    $scope.guardDetail = response.data.guard;
                },function(response){
                    $scope.errorFound = 1;
                    $scope.ansars = [];
                    $scope.guardDetail = [];
                    $scope.errorMessage = $sce.trustAsHtml("<tr class='warning'><td colspan='"+$('.table').find('tr').find('th').length+"'>"+response.data+"</td></tr>");
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
                        scope.kpi_withdraw_date = "";
                        scope.$digest()
                        $("#withrdaw-option").modal("toggle")
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
    <div ng-controller="ReportGuardSearchController">
        {{--<div class="breadcrumbplace">--}}
            {{--{!! Breadcrumbs::render('ansar_withdraw_view') !!}--}}
        {{--</div>--}}
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
                <div class="overlay" id="all-loading" style="display: none;">
                    <span class="fa">
                        <i class="fa fa-refresh fa-spin"></i> <b>Loading...</b>
                    </span>
                </div>
                <div class="box-body">
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
                                        ng-change="loadThana(selectedDistrict)" name="unit_id">
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
                                        ng-change="loadGuard(selectedThana)" name="thana_id">
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
                                        id="kpi_name_list" name="kpi_name_list">
                                    <option value="">--Select a Guard--</option>
                                    <option ng-repeat="d in guards" value="[[d.id]]">[[d.kpi_name]]
                                    </option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="pc-table">

                                    <tr>
                                        <th>Sl No.</th>
                                        <th>Ansar ID</th>
                                        <th>Ansar Name</th>
                                        <th>Ansar Designation</th>
                                        <th>Ansar Sex</th>
                                        <th>KPI Name</th>
                                        <th>KPI District</th>
                                        <th>KPI Unit</th>
                                        <th>Reporting Date</th>
                                        <th>Embodiment Date</th>
                                    </tr>
                                    <tbody ng-if="errorFound==1" ng-bind-html="errorMessage"></tbody>
                                    <tbody id="ansar-all" class="status">
                                    <tr colspan="10" class="warning" id="not-find-info" ng-if="errorFound==0">
                                        <td colspan="10">No Ansar is available to Withdraw</td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <button class="pull-right btn btn-primary" id="withdraw-guard-confirmation" open-hide-modal disabled>
                        Withdraw Ansar
                    </button>
                </div>
            </div>
            <!--Modal Open-->
            {!! Form::open(array('route' => 'ansar-withdraw-update', 'name' => 'kpiWithdrawForm', 'id'=> 'kpi-form', 'ng-app' => 'myValidateApp', 'novalidate')) !!}
            <div id="withrdaw-option" class="modal fade" role="dialog">
                <div class="modal-dialog" style="width: 80%;overflow: auto;">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal"
                                    ng-click="modalOpen = false">&times;</button>
                            <h3 class="modal-title">Ansars' Withdrawal Confirmation</h3>
                        </div>
                        <div class="modal-body">
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
                                        <div class="col-sm-4">
                                            <div class="form-group">
                                                <label class="control-label">Date of
                                                    Withdrawal:&nbsp;&nbsp;&nbsp;</label>
                                                {!! Form::text('kpi_withdraw_date', $value = "", $attributes = array('class' => 'form-control', 'id' => 'kpi_withdraw_date', 'ng_model' => 'kpi_withdraw_date', 'required')) !!}
                                            </div>
                                        </div>
                                        <div class="col-sm-4">
                                            <div class="form-group">
                                                <label class="control-label">Reason for
                                                    Withdrawal:&nbsp;&nbsp;&nbsp;</label>
                                                {!! Form::text('kpi_withdraw_reason', $value = "", $attributes = array('class' => 'form-control', 'id' => 'kpi_withdraw_reason', 'ng_model' => 'kpi_withdraw_reason', 'required')) !!}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table table-bordered" id="pc-table">
                                            <tr>
                                                <th>Sl No.</th>
                                                <th>Ansar ID</th>
                                                <th>Ansar Name</th>
                                                <th>Ansar Designation</th>
                                                <th>Ansar Sex</th>
                                                <th>KPI Name</th>
                                                <th>KPI District</th>
                                                <th>KPI Unit</th>
                                            </tr>
                                            <tbody id="cansar-all" class="status">
                                            <tr colspan="10" class="warning" id="not-find-info">
                                                <td colspan="10">No Ansar is available to Withdraw</td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <button class="btn btn-primary pull-right"
                                            ng-disabled="!kpi_withdraw_date||!kpi_withdraw_reason||!memorandumId||isVerified||isVerifying">
                                        Confirm
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
        var h = "";
        $.ajaxSetup({
            headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'}
        })
        //$('#withdraw-guard-confirmation').prop('disabled', true);

        $('#kpi_name_list').change(function () {
            $("#all-loading").css('display', 'block');
            var selectedKPIName = $('select[name=kpi_name_list]').val();
            $.ajax({
                url: '{{URL::route('ansar_list_for_withdraw')}}',
                type: 'get',
                data: {selected_name: selectedKPIName},
                success: function (data) {
//                    $("#ansar-all").html(data);
//                    h = data;
//                    var rowCount = $('#ansar-withdraw-table tbody tr').length;
//                    if(($("tbody").is(":empty"))){
//                        $('#withdraw-guard-confirmation').prop('disabled', true);
//                    } else {
//                        $('#withdraw-guard-confirmation').prop('disabled', false);
//                    }
                    $("#all-loading").css('display', 'none');

                    if (data.result == undefined && data.valid == undefined) {
                        $('#withdraw-guard-confirmation').prop('disabled', false);
                        $("#ansar-all").html(data);
                        h = data;
                    }
                    else if (data.result!=undefined && data.valid == undefined){
                        $('#withdraw-guard-confirmation').prop('disabled', true);
//                        alert($("#status-all").html())
                        $("#ansar-all").html('<tr colspan="11" class="warning" id="not-find-info"> <td colspan="11">No Ansar is available to Withdraw</td></tr>');
                    }else if(data.result==undefined && data.valid != undefined){
                        $('#withdraw-guard-confirmation').prop('disabled', true);
//                        alert($("#status-all").html())
                        $("#ansar-all").html('<tr colspan="11" class="warning" id="not-find-info"> <td colspan="11">Invalid Request (400)</td></tr>');
                    }
                }
            });
        })


        $('#withdraw-guard-confirmation').click(function (e) {
            e.preventDefault();
            if (h) {
                $('#cansar-all').html(h);
                var l = $('#cansar-all').children('tr').children('td').length;
                if (l > 1) {
                    $('#cansar-all').children('tr').each(function () {
                        $($(this).children('td')[$(this).children('td').length - 1]).remove()
                        $($(this).children('td')[$(this).children('td').length - 1]).remove()
//                        $($(this).children('td')[$(this).children('td').length - 3]).remove()
                    })
                }
            }
        });
    </script>
@stop