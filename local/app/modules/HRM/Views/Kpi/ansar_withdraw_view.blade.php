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
            $('#kpi_withdraw_date').datepicker({
                dateFormat:'dd-M-yy'
            });
        })
        GlobalApp.controller('ReportGuardSearchController', function ($scope, $http, $sce) {
            $scope.ansars = [];
            $scope.reportType = 'eng';
            $scope.memorandumId = "";
            $scope.params = ''
            $scope.isVerified = false;
            $scope.isVerifying = false;
            $scope.allLoading = false;
            $scope.kpi_withdraw_reason = "Freeze Ansar for Withdrawal";
            $scope.kpi_withdraw_date="";
            $scope.dcDistrict = parseInt('{{Auth::user()->district_id}}');
            $scope.errorMessage = '';
            $scope.errorFound = 0;
            $scope.allSelected = false;
            $scope.selected = [];
            $scope.selectedAnsar = [];
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
            $scope.reset = function () {
                $scope.allSelected = false;
                $scope.selectedAnsar = [];
                $scope.ansars = [];
                $scope.selected = []
            }
            $scope.changeSelected = function (i) {
                var a = 0;
                if($scope.selected[i]===false){
                    var i = $scope.selectedAnsar.indexOf($scope.ansars[i]);
                    $scope.selectedAnsar.splice(i,1);
                }
                else {
                    $scope.selectedAnsar.push($scope.ansars[$scope.selected[i]])
                }
                $scope.selected.forEach(function (value,index) {

                    if(value!==false) {

                        a++
                    }
                })
                console.log($scope.selectedAnsar)
                $scope.allSelected = a==$scope.ansars.length;
            }
            $scope.changeAll = function () {
                if($scope.allSelected){
                    $scope.selectedAnsar = []
                    $scope.ansars.forEach(function (value,index) {

                            $scope.selectedAnsar.push(value);
                            $scope.selected[index] = index;

                    })
                }
                else{
                    $scope.selectedAnsar = [];
                    $scope.selected = Array.apply(null,Array($scope.ansars.length)).map(Boolean.prototype.valueOf,false);
                }
            }
            $scope.loadAnsar = function (param) {
                console.log(param)
                $scope.allLoading = true;
                $http({
                    method: 'get',
                    url: '{{URL::route('ansar_list_for_withdraw')}}',
                    params: param
                }).then(function (response) {
                    $scope.errorFound = 0;
                    $scope.ansars = response.data;
                    $scope.allLoading = false;
                    $scope.selected = Array.apply(null,Array($scope.ansars.length)).map(Boolean.prototype.valueOf,false);
                },function(response){
                    $scope.allLoading = false;
                    $scope.errorFound = 1;
                    $scope.ansars = [];
                    $scope.guardDetail = [];
                    $scope.errorMessage = $sce.trustAsHtml("<tr class='warning'><td colspan='"+$('.table').find('tr').find('th').length+"'>"+response.data+"</td></tr>");
                })
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
        @if(Session::has('error_message'))
            <div style="padding: 10px 20px 0 20px;">
                <div class="alert alert-danger">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                    <span class="fa fa-warning"></span> {{Session::get('error_message')}}
                </div>
            </div>
        @endif
        <section class="content">
            <div class="box box-solid">
                <div class="overlay" ng-if="allLoading">
                    <span class="fa">
                        <i class="fa fa-refresh fa-spin"></i> <b>Loading...</b>
                    </span>
                </div>
                <div class="box-body">
                    <filter-template
                            show-item="['range','unit','thana','kpi']"
                            type="single"
                            kpi-change="loadAnsar(param)"
                            range-change="reset()"
                            unit-change="reset()"
                            thana-change="reset()"
                            start-load="range"
                            field-width="{range:'col-sm-3',unit:'col-sm-3',thana:'col-sm-3',kpi:'col-sm-3'}"
                            data="params"
                    ></filter-template>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <table class="table table-bordered">

                                    <tr>
                                        <th>Sl No.</th>
                                        <th>Ansar ID</th>
                                        <th>Ansar Name</th>
                                        <th>Ansar Designation</th>
                                        <th>Ansar Gender</th>
                                        <th>Home District</th>
                                        <th>Reporting Date</th>
                                        <th>Embodiment Date</th>
                                        <th>
                                            <input type="checkbox" ng-model="allSelected" ng-change="changeAll()">
                                        </th>
                                    </tr>
                                    <tr ng-repeat="a in ansars">
                                        <td>[[$index+1]]</td>
                                        <td>[[a.ansar_id]]</td>
                                        <td>[[a.ansar_name_eng]]</td>
                                        <td>[[a.name_bng]]</td>
                                        <td>[[a.sex]]</td>
                                        <td>[[a.unit_name_bng]]</td>
                                        <td>[[a.reporting_date|dateformat:'DD-MMM-YYYY']]</td>
                                        <td>[[a.joining_date|dateformat:'DD-MMM-YYYY']]</td>
                                        <td>
                                            <input type="checkbox" ng-model="selected[$index]" ng-change="changeSelected($index)" ng-true-value="[[$index]]" ng-false-value="false">
                                        </td>
                                    </tr>

                                    <tr colspan="7" class="warning" ng-if="ansars.length<=0">
                                        <td colspan="10">No Ansar is available to Withdraw</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <button class="pull-right btn btn-primary" id="withdraw-guard-confirmation" ng-disabled="ansars.length<=0||!params.kpi" open-hide-modal>
                        Withdraw Ansar
                    </button>
                </div>
            </div>
            <!--Modal Open-->
            {!! Form::open(array('route' => 'ansar-withdraw-update', 'name' => 'kpiWithdrawForm', 'id'=> 'kpi-form', 'ng-app' => 'myValidateApp', 'novalidate')) !!}
            {!! Form::hidden('kpi_id_withdraw','[[params.kpi]]') !!}
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
                                        <div class="col-md-12">
                                            <div class="table-responsive">
                                                <table class="table table-bordered">

                                                    <tr>
                                                        <th>Sl No.</th>
                                                        <th>Ansar ID</th>
                                                        <th>Ansar Name</th>
                                                        <th>Ansar Designation</th>
                                                        <th>Ansar Gender</th>
                                                        <th>Home District</th>
                                                        <th>Reporting Date</th>
                                                        <th>Embodiment Date</th>
                                                    </tr>
                                                    <tr ng-repeat="a in selectedAnsar">
                                                        <input type="hidden" name="ansarIds[]" value="[[a.ansar_id]]">
                                                        <td>[[$index+1]]</td>
                                                        <td>[[a.ansar_id]]</td>
                                                        <td>[[a.ansar_name_eng]]</td>
                                                        <td>[[a.name_bng]]</td>
                                                        <td>[[a.sex]]</td>
                                                        <td>[[a.unit_name_bng]]</td>
                                                        <td>[[a.reporting_date|dateformat:'DD-MMM-YYYY']]</td>
                                                        <td>[[a.joining_date|dateformat:'DD-MMM-YYYY']]</td>

                                                    </tr>

                                                    <tr colspan="7" class="warning" ng-if="ansars.length<=0">
                                                        <td colspan="10">No Ansar is available to Withdraw</td>
                                                    </tr>
                                                </table>
                                            </div>
                                        </div>
                                        <div class="col-sm-4">
                                            <div class="form-group">
                                                <label class="control-label">Memorandum no.&nbsp;&nbsp;&nbsp;<span
                                                            ng-show="isVerifying"><i
                                                                class="fa fa-spinner fa-pulse"></i>&nbsp;Verifying</span><span
                                                            class="text-danger"
                                                            ng-if="isVerified&&!memorandumId">Memorandum no. is required.</span><span
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
@stop