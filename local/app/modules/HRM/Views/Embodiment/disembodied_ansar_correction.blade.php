{{--Ansar Transfer Complete--}}

@extends('template.master')
@section('title','Transfer Ansars')
@section('breadcrumb')
    {!! Breadcrumbs::render('transfer') !!}
@endsection
@section('content')
    <script>
        GlobalApp.controller("TransferController", function ($scope, $http, $timeout,$rootScope) {
            $scope.reasons = [];
            $scope.params={};
            $http({
                method:'get',
                url:'{{URL::route('load_disembodiment_reason')}}'
            }).then(function (response) {
                $scope.reasons = response.data;
            },function (response) {
                $scope.reasons = [];
            })
        })

    </script>
    <div notification-message ng-controller="TransferController">

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
                            kpi-change="loadAnsar()"
                            start-load="range"
                            field-width="{range:'col-sm-3',unit:'col-sm-3',thana:'col-sm-3',kpi:'col-sm-3'}"
                            data = "params"
                    ></filter-template>
                    <div class="row">
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label for="" class="control-label">Select Reason</label>
                                <select ng-model="params.reason" class="form-control">
                                    <option value="">--Select a Reason</option>
                                    <option ng-repeat="r in reasons" value="[[r.id]]">[[r.reason_in_bng]]</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered" id="pc-table">
                            <caption>
                                <table-search q="q" results="results"></table-search>
                            </caption>
                            <tr>
                                <th>SL. No</th>
                                <th>ID</th>
                                <th>Designation</th>
                                <th>Name</th>
                                <th>Division</th>
                                <th>District</th>
                                <th>Last Embodied KPI</th>
                                <th>Total Service Days</th>
                                <th>Disembodied Date</th>
                                <th>Action</th>
                                {{--<th>
                                    <div class="styled-checkbox">
                                        <input ng-disabled="ansars.length<=0" type="checkbox" id="all"
                                               ng-change="changeSelectAll()" ng-model="selectAll">
                                        <label for="all"></label>
                                    </div>
                                </th>--}}
                            </tr>
                            <tr class="warning" ng-if="ansars.length<=0">
                                <td colspan="9">No Ansar Found to Transfer</td>
                            </tr>
                            <tr ng-repeat="ansar in ansars|filter:q as results" ng-if="ansars.length>0">
                                <td>[[$index+1]]</td>
                                <td>[[ansar.ansar_id]]</td>
                                <td>[[ansar.name_bng]]</td>
                                <td>[[ansar.ansar_name_bng]]</td>
                                <td>[[ansar.division_name_bng]]</td>
                                <td>[[ansar.unit_name_bng]]</td>
                                <td>[[ansar.kpi_name]]</td>
                                <td>[[ansar.transfered_date|dateformat:'DD-MMM-YYYY']]</td>
                                <td>
                                    <div class="styled-checkbox">
                                        <input type="checkbox" id="a_[[ansar.ansar_id]]"
                                               ng-change="changeSelectAnsar($index)"
                                               ng-model="selectAnsar[$index]">
                                        <label for="a_[[ansar.ansar_id]]"></label>
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div>
                        {!! Form::open(['route'=>'print_letter','target'=>'_blank','ng-if'=>'letterOption.status','class'=>'pull-left']) !!}
                        <input type="hidden" ng-repeat="(key,value) in letterOption" name="[[key]]" value="[[value]]">
                        <button class="btn btn-primary"><i class="fa fa-print"></i>&nbsp;Print Transfer Letter</button>
                        {!! Form::close() !!}
                        <button class="pull-right btn btn-primary" open-hide-modal ng-click="modalOpen=true">
                            <i class="fa fa-send"></i>&nbsp;&nbsp;Transfer
                        </button>
                        <div class="clearfix"></div>
                    </div>
                </div>

            </div>
            <div id="transfer-option" class="modal fade" role="dialog">
                <div class="modal-dialog"
                     style="width: 70% !important;margin: 0 auto !important;margin-top: 20px !important;">
                    <div class="modal-content">
                        <div class="modal-header">
                            <strong>Transfer Option</strong>
                            <button type="button" class="close" data-dismiss="modal"
                                    ng-click="modalOpen = false">&times;</button>
                        </div>
                        <div class="modal-body">
                            <div class="register-box" style="margin: 0;width: auto">
                                <div class="register-box-body  margin-bottom" style="padding: 0;padding-bottom: 10px">
                                    <filter-template
                                            show-item="['range','unit','thana','kpi']"
                                            type="single"
                                            start-load="range"
                                            kpi-disabled="params.kpi"
                                            field-width="{range:'col-sm-3',unit:'col-sm-3',thana:'col-sm-3',kpi:'col-sm-3'}"
                                            data = "trans"
                                            reset-all="[[resetValue]]"
                                    ></filter-template>

                                    <div class="row">
                                        <div class="col-sm-4">
                                            <div class="form-group">
                                                <label class="control-label">Memorandum no. & Date&nbsp;&nbsp;&nbsp;<span
                                                            ng-show="isVerifying"><i class="fa fa-spinner fa-pulse"></i>&nbsp;Verifying</span>
                                                    <span class="text-danger"
                                                          ng-if="isVerified">This id already taken</span></label>

                                                <div class="row">
                                                    <div class="col-md-7" style="padding-right: 0"><input ng-blur="verifyMemorandumId()"
                                                                                 ng-model="memorandumId"
                                                                                 type="text" class="form-control"
                                                                                 name="memorandum_id"
                                                                                 placeholder="Enter memorandum id">
                                                    </div>
                                                    <div class="col-md-5">
                                                        <input date-picker ng-model="memDate"
                                                               type="text" class="form-control" name="mem_date"
                                                               placeholder="Memorandum Date" required>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-4">
                                            <div class="form-group">
                                                <label class="control-label">Joining date in transfered kpi.</label>
                                                <input type="text" ng-model="joinDate" id="join_date_in_tk"
                                                       class="form-control"
                                                       name="memorandum_id">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table table-bordered" style="max-height: 200px">
                                            <tr>
                                                <th>SL. No</th>
                                                <th>ID</th>
                                                <th>Designation</th>
                                                <th>Name</th>
                                                <th>Division</th>
                                                <th>District</th>
                                                <th>KPI Name</th>
                                                <th>Joining Date</th>
                                            </tr>
                                            <tr class="warning" ng-if="selectedAnsar.length<=0">
                                                <td colspan="8">No Ansar Found to Transfer</td>
                                            </tr>
                                            <tr ng-repeat="ansar in selectedAnsar" ng-if="selectedAnsar.length>0">
                                                <td>[[$index+1]]</td>
                                                <td>[[ansar.ansar_id]]</td>
                                                <td>[[ansar.name_bng]]</td>
                                                <td>[[ansar.ansar_name_bng]]</td>
                                                <td>[[ansar.division_name_bng]]</td>
                                                <td>[[ansar.unit_name_bng]]</td>
                                                <td>[[ansar.kpi_name]]</td>
                                                <td>[[ansar.joining_date|dateformat:'DD-MMM-YYYY']]</td>
                                            </tr>
                                        </table>
                                    </div>
                                    <button class="btn btn-primary pull-right" open-hide-modal
                                            ng-disabled="selectedAnsar.length<=0||!memorandumId||!joinDate||!trans.kpi||isVerified||isVerifying"
                                            ng-click="confirmTransferAnsar()">
                                        <i class="fa fa-check"></i>&nbsp;Confirm
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