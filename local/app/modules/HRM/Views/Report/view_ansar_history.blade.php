@extends('template.master')
@section('title','View Ansar History')
@section('breadcrumb')
    {!! Breadcrumbs::render('view_ansar_history') !!}
@endsection
@section('content')
    <script>
        GlobalApp.controller("ViewAnsarHistoryController", function ($scope, $http) {
            $scope.ansarDetail = {};
            $scope.allLoading = false;
            $scope.loadAnsarDetail = function (id) {
                $scope.allLoading = true;
                $scope.errorFound = 0;
                $scope.errorMessage = "";
                $http({
                    method: 'get',
                    url: '{{URL::route('view_ansar_history_report')}}',
                    params: {ansar_id: id}
                }).then(function (response) {
                    console.log(Array.isArray(response.data['lPanel']) && response.data['lPanel'].length > 0)
                    $scope.ansarDetail = response.data;
                    $scope.allLoading = false;
                }, function (response) {
                    $scope.ansarDetail = {};
                    $scope.errorFound = 1;
                    $scope.errorMessage = "Please enter a valid Ansar ID";
                    $scope.allLoading = false;
                })
            };
            $scope.getKPIInfo = function (kpi) {
                if (kpi) {
                    return kpi.kpi_name + ", " + kpi.thana.thana_name_bng + ", " + kpi.unit.unit_name_bng + ", " + kpi.division.division_name_bng;
                }
                return "";
            };
            $scope.getUnitAddress = function (unit) {
                if (unit) {
                    return unit.unit_name_bng + ", " + unit.division.division_name_bng;
                }
                return "";
            }
        });
    </script>
    <style></style>
    <div ng-controller="ViewAnsarHistoryController">
        <section class="content">
            <div class="box box-solid">
                <div class="overlay" ng-if="allLoading">
                    <span class="fa">
                        <i class="fa fa-refresh fa-spin"></i> <b>Loading...</b>
                    </span>
                </div>
                <div class="box-body"><br>
                    <div class="row">
                        <div class="col-md-6 col-centered">
                            <div class="col-md-6 col-sm-12 col-xs-12">
                                <div class="form-group">
                                    <input type="text" ng-model="ansar_id" class="form-control"
                                           placeholder="Enter Ansar ID">
                                    <span class="text-danger" ng-if="errorFound==1">[[errorMessage]]</span>
                                </div>
                            </div>
                            <div class="col-md-6 col-sm-12 col-xs-12">
                                <button class="btn btn-primary" ng-click="loadAnsarDetail(ansar_id)">Generate Ansar
                                    Service Record
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="box box-solid" ng-if="ansarDetail && ansarDetail['ansar']">
                <div class="box-title"><h3 style="margin: 1%;">Personal Information</h3></div>
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-12">
                            <table class="table table-bordered table-striped">
                                <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Designation</th>
                                    <th>Current Status</th>
                                    <th>Mobile Number</th>
                                    <th>Gender</th>
                                    <th>Picture</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td><a href="/HRM/entryreport/[[ansarDetail['ansar'].ansar_id]]">[[ansarDetail['ansar'].ansar_name_bng]]</a></td>
                                    <td>[[ansarDetail['ansar'].designation.name_bng]]</td>
                                    <td>[[ansarDetail['status'].join()]]</td>
                                    <td>[[ansarDetail['ansar'].mobile_no_self]]</td>
                                    <td>[[ansarDetail['ansar'].sex]]</td>
                                    <td><img src="[[ansarDetail['ansar'].profile_pic]]" style="width: 80px;height: 80px"
                                             alt="[[ansarDetail['ansar'].ansar_name_bng]]"/></td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="box box-solid" ng-if="ansarDetail && (ansarDetail['cOffer'] || ansarDetail['lOffer'])">
                <div class="box-title"><h3 style="margin: 1%;">Offer Information</h3></div>
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-12">
                            <h4>Current</h4>
                            <p ng-if="!ansarDetail['cOffer']">No data</p>
                            <table class="table table-bordered table-striped" ng-if="ansarDetail['cOffer']">
                                <thead>
                                <tr>
                                    <th>Offer Date</th>
                                    <th>Offer District</th>
                                    <th>Offer Type</th>
                                    <th>Come From</th>
                                    <th>SMS Status</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td>[[ansarDetail['cOffer'].sms_send_datetime]]</td>
                                    <td>[[getUnitAddress(ansarDetail['cOffer'].district)]]</td>
                                    <td>[[ansarDetail['cOffer'].offerType]]</td>
                                    <td>[[ansarDetail['cOffer'].come_from]]</td>
                                    <td>[[ansarDetail['cOffer'].sms_status]]</td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="col-md-12">
                            <h4>History</h4>
                            <p ng-if="!Array.isArray(ansarDetail['lOffer']) && ansarDetail['lOffer'].length<=0">No data</p>
                            <table class="table table-bordered table-striped" ng-if="ansarDetail['lOffer'] && ansarDetail['lOffer'].length>0">
                                <thead>
                                <tr>
                                    <th>Offer date</th>
                                    <th>Offer district</th>
                                    <th>Offer type</th>
                                    <th>Reply type</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr ng-repeat="loffer in ansarDetail['lOffer']">
                                    <td>[[loffer.offered_date]]</td>
                                    <td>[[getUnitAddress(loffer.district)]]</td>
                                    <td>[[loffer.offerType]]</td>
                                    <td>[[loffer.reply_type]]</td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="col-md-12">
                            <h4>Offer Block</h4>
                            <p ng-if="!Array.isArray(ansarDetail['bOffer']) && ansarDetail['bOffer'].length<=0">No data</p>
                            <table class="table table-bordered table-striped" ng-if="ansarDetail['bOffer'] && ansarDetail['bOffer'].length>0">
                                <thead>
                                <tr>
                                    <th>Status</th>
                                    <th>Block Date</th>
                                    <th>Unblock Date</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr ng-repeat="boffer in ansarDetail['bOffer']">
                                    <td>[[boffer.status]]</td>
                                    <td>[[boffer.blocked_date]]</td>
                                    <td>[[boffer.unblocked_date]]</td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="box box-solid" ng-if="ansarDetail && (ansarDetail['cPanel'] || ansarDetail['lPanel'])">
                <div class="box-title"><h3 style="margin: 1%;">Panel Information</h3></div>
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-12">
                            <h4>Current</h4>
                            <p ng-if="!ansarDetail['cPanel']">No Data</p>
                            <table class="table table-bordered table-striped" ng-if="ansarDetail['cPanel']">
                                <thead>
                                <tr>
                                    <th>Global Panel Date</th>
                                    <th>Global Panel Position</th>
                                    <th>Regional Panel Date</th>
                                    <th>Regional Panel Position</th>
                                    <th>Come From</th>
                                    <th>Memorandum Id</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td>[[ansarDetail['cPanel'].panel_date]]</td>
                                    <td>[[ansarDetail['cPanel'].go_panel_position]]</td>
                                    <td>[[ansarDetail['cPanel'].re_panel_date]]</td>
                                    <td>[[ansarDetail['cPanel'].re_panel_position]]</td>
                                    <td>[[ansarDetail['cPanel'].come_from]]</td>
                                    <td>[[ansarDetail['cPanel'].memorandum_id]]</td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="col-md-12">
                            <h4>History</h4>
                            <p ng-if="!Array.isArray(ansarDetail['lPanel']) && ansarDetail['lPanel'].length<=0">No
                                Data</p>
                            <table class="table table-bordered table-striped"
                                   ng-if="ansarDetail['lPanel'] && ansarDetail['lPanel'].length>0">
                                <thead>
                                <tr>
                                    <th>Global Panel Date</th>
                                    <th>Global Panel Position</th>
                                    <th>Regional Panel Date</th>
                                    <th>Regional Panel Position</th>
                                    <th>Come From</th>
                                    <th>Memorandum Id</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr ng-repeat="lpanel in ansarDetail['lPanel']">
                                    <td>[[lpanel.panel_date]]</td>
                                    <td>[[lpanel.go_panel_position]]</td>
                                    <td>[[lpanel.re_panel_date]]</td>
                                    <td>[[lpanel.re_panel_position]]</td>
                                    <td>[[lpanel.come_from]]</td>
                                    <td>[[lpanel.old_memorandum_id]]</td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="box box-solid"
                 ng-if="ansarDetail && (ansarDetail['cEmbodiment'] || ansarDetail['lEmbodiment'])">
                <div class="box-title"><h3 style="margin: 1%;">Embodiment Information</h3></div>
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-12">
                            <h4>Current</h4>
                            <p ng-if="!ansarDetail['cEmbodiment']">No Data</p>
                            <table class="table table-bordered table-striped" ng-if="ansarDetail['cEmbodiment']">
                                <thead>
                                <tr>
                                    <th>Embodiment Date</th>
                                    <th>Service End Date</th>
                                    <th>KPI</th>
                                    <th>Memorandum Id</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td>[[ansarDetail['cEmbodiment'].joining_date]]</td>
                                    <td>[[ansarDetail['cEmbodiment'].service_ended_date]]</td>
                                    <td>[[getKPIInfo(ansarDetail['cEmbodiment'].kpi)]]</td>
                                    <td>[[ansarDetail['cEmbodiment'].memorandum_id]]</td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="col-md-12">
                            <h4>History</h4>
                            <p ng-if="!Array.isArray(ansarDetail['lEmbodiment']) && ansarDetail['lEmbodiment'].length<=0">
                                No Data</p>
                            <table class="table table-bordered table-striped"
                                   ng-if="ansarDetail['lEmbodiment'] && ansarDetail['lEmbodiment'].length>0">
                                <thead>
                                <tr>
                                    <th>Embodiment Date</th>
                                    <th>Disembodiment Date</th>
                                    <th>Disembodiment Reason</th>
                                    <th>KPI</th>
                                    <th>Memorandum Id</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr ng-repeat="lembodiment in ansarDetail['lEmbodiment']">
                                    <td>[[lembodiment.joining_date]]</td>
                                    <td>[[lembodiment.release_date]]</td>
                                    <td>[[lembodiment.disembodiment_reason.reason_in_bng]]</td>
                                    <td>[[getKPIInfo(lembodiment.kpi)]]</td>
                                    <td>[[lembodiment.old_memorandum_id]]</td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="box box-solid">
                <div class="box-title"><h3 style="margin: 1%;">Transfer Log</h3></div>
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-12">

                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection